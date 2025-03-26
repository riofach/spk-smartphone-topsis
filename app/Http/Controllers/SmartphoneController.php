<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use App\Models\Smartphone;
use App\Services\SmartphoneImageService;
use App\Services\TopsisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SmartphoneController extends Controller
{
    protected $topsisService;
    protected $smartphoneImageService;

    public function __construct(TopsisService $topsisService, SmartphoneImageService $smartphoneImageService)
    {
        $this->topsisService = $topsisService;
        $this->smartphoneImageService = $smartphoneImageService;

        // Buat direktori jika belum ada
        $this->createDirectoryIfNotExists();
    }

    /**
     * Memastikan direktori untuk menyimpan gambar ada
     */
    protected function createDirectoryIfNotExists()
    {
        $storagePath = storage_path('app/public/smartphones');
        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
        }

        $publicPath = public_path('images/smartphones');
        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0755, true);
        }

        $publicPath = public_path('images');
        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0755, true);
        }

        // Buat gambar placeholder jika belum ada
        if (!File::exists(public_path('images/no-image.png'))) {
            File::copy(public_path('favicon.ico'), public_path('images/no-image.png'));
        }
    }

    /**
     * Menampilkan daftar smartphone
     */
    public function index(Request $request)
    {
        $query = Smartphone::withinTwoYears();

        // Pencarian real-time untuk AJAX
        if ($request->has('query')) {
            $searchTerm = $request->input('query');
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
            });

            // Terapkan filter lainnya untuk query AJAX juga
            if ($request->has('min_price') && $request->min_price) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->has('max_price') && $request->max_price) {
                $query->where('price', '<=', $request->max_price);
            }

            if ($request->has('ram') && $request->ram) {
                $query->where('ram', '=', $request->ram);
            }

            if ($request->has('storage') && $request->storage) {
                $query->where('storage', '=', $request->storage);
            }

            if ($request->has('release_year') && $request->release_year) {
                $query->where('release_year', '=', $request->release_year);
            }

            // Terapkan sort untuk query AJAX
            if ($request->has('sort')) {
                switch ($request->sort) {
                    case 'price_low_high':
                        $query->orderBy('price', 'asc');
                        break;
                    case 'price_high_low':
                        $query->orderBy('price', 'desc');
                        break;
                    case 'latest':
                    default:
                        $query->latest();
                        break;
                }
            } else {
                $query->latest();
            }

            // Batasi hasil untuk pencarian real-time
            $smartphones = $query->limit(20)->get();

            // Tambahkan URL ke data
            $smartphones->transform(function ($smartphone) {
                $smartphone->edit_url = route('smartphones.edit', $smartphone->id);
                $smartphone->delete_url = route('smartphones.destroy', $smartphone->id);
                return $smartphone;
            });

            // Pastikan response JSON dengan header yang benar
            try {
                return response()->json([
                    'smartphones' => $smartphones
                ], 200, ['Content-Type' => 'application/json']);
            } catch (\Exception $e) {
                Log::error('Error pada pencarian real-time: ' . $e->getMessage());
                return response()->json([
                    'error' => 'Terjadi kesalahan pada server',
                    'message' => $e->getMessage()
                ], 500);
            }
        }

        // Filter berdasarkan range harga
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter berdasarkan RAM
        if ($request->has('ram') && $request->ram) {
            $query->where('ram', '=', $request->ram);
        }

        // Filter berdasarkan Storage
        if ($request->has('storage') && $request->storage) {
            $query->where('storage', '=', $request->storage);
        }

        // Filter berdasarkan tahun rilis
        if ($request->has('release_year') && $request->release_year) {
            $query->where('release_year', '=', $request->release_year);
        }

        // Sort by price ASC/DESC atau latest
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_low_high':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high_low':
                    $query->orderBy('price', 'desc');
                    break;
                case 'latest':
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $smartphones = $query->paginate(10)->withQueryString();
        $criteria = Criteria::all();

        // Get unique values for filters
        $ramOptions = Smartphone::select('ram')->distinct()->orderBy('ram')->pluck('ram');
        $storageOptions = Smartphone::select('storage')->distinct()->orderBy('storage')->pluck('storage');
        $releaseYearOptions = Smartphone::select('release_year')->distinct()->orderBy('release_year', 'desc')->pluck('release_year');

        // For autocomplete suggestions
        $suggestions = null;
        if ($request->has('suggest') && $request->suggest) {
            $suggestions = Smartphone::where('name', 'like', '%' . $request->suggest . '%')
                ->orWhere('processor', 'like', '%' . $request->suggest . '%')
                ->limit(5)
                ->get(['id', 'name', 'processor', 'image_url']);

            return response()->json($suggestions);
        }

        return view('smartphones.index', compact(
            'smartphones',
            'criteria',
            'ramOptions',
            'storageOptions',
            'releaseYearOptions'
        ));
    }

    /**
     * Menampilkan form tambah smartphone
     */
    public function create()
    {
        $currentYear = now()->year;
        return view('smartphones.create', compact('currentYear'));
    }

    /**
     * Menyimpan data smartphone baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'camera_score' => 'required|numeric|min:1|max:10',
            'performance_score' => 'required|numeric|min:1|max:10',
            'design_score' => 'required|numeric|min:1|max:10',
            'battery_score' => 'required|numeric|min:1|max:10',
            'release_year' => 'required|integer|min:' . (now()->year - 2) . '|max:' . now()->year,
            'image' => 'required|image|mimes:png|max:2048',
            'ram' => 'required|integer|min:1',
            'storage' => 'required|integer|min:8',
            'processor' => 'required|string',
            'battery' => 'required|integer|min:1000',
            'camera' => 'required|integer|min:5',
            'screen_size' => 'required|numeric|min:3|max:10',
        ]);

        // Default image URL
        $imageUrl = null;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Upload to Supabase via our service
            $imageUrl = $this->smartphoneImageService->processAndUpload(
                $request->file('image'),
                $request->name
            );
        }

        // If no image URL (upload failed or no image), use default
        if (!$imageUrl) {
            $imageUrl = asset('images/no-image.png');
        }

        Smartphone::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'camera_score' => $request->camera_score,
            'performance_score' => $request->performance_score,
            'design_score' => $request->design_score,
            'battery_score' => $request->battery_score,
            'release_year' => $request->release_year,
            'image_url' => $imageUrl,
            'ram' => $request->ram,
            'storage' => $request->storage,
            'processor' => $request->processor,
            'battery' => $request->battery,
            'camera' => $request->camera,
            'screen_size' => $request->screen_size,
        ]);

        return redirect()->route('smartphones.index')
            ->with('success', 'Smartphone berhasil ditambahkan dengan gambar di Supabase');
    }

    /**
     * Menampilkan form edit smartphone
     */
    public function edit(Smartphone $smartphone)
    {
        $currentYear = now()->year;
        return view('smartphones.edit', compact('smartphone', 'currentYear'));
    }

    /**
     * Mengupdate data smartphone
     */
    public function update(Request $request, Smartphone $smartphone)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'camera_score' => 'required|numeric|min:1|max:10',
            'performance_score' => 'required|numeric|min:1|max:10',
            'design_score' => 'required|numeric|min:1|max:10',
            'battery_score' => 'required|numeric|min:1|max:10',
            'release_year' => 'required|integer|min:' . (now()->year - 2) . '|max:' . now()->year,
            'ram' => 'required|integer|min:1',
            'storage' => 'required|integer|min:8',
            'processor' => 'required|string',
            'battery' => 'required|integer|min:1000',
            'camera' => 'required|integer|min:5',
            'screen_size' => 'required|numeric|min:3|max:10',
        ];

        // Jika ada file gambar baru
        if ($request->hasFile('image')) {
            $rules['image'] = 'image|mimes:png|max:2048';
        }

        $validatedData = $request->validate($rules);

        // Update data dasar
        $smartphone->update([
            'name' => $validatedData['name'],
            'price' => $validatedData['price'],
            'description' => $validatedData['description'],
            'camera_score' => $validatedData['camera_score'],
            'performance_score' => $validatedData['performance_score'],
            'design_score' => $validatedData['design_score'],
            'battery_score' => $validatedData['battery_score'],
            'release_year' => $validatedData['release_year'],
            'ram' => $validatedData['ram'],
            'storage' => $validatedData['storage'],
            'processor' => $validatedData['processor'],
            'battery' => $validatedData['battery'],
            'camera' => $validatedData['camera'],
            'screen_size' => $validatedData['screen_size'],
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Check if there's an existing Supabase image to delete
            $currentImageUrl = $smartphone->getRawOriginal('image_url');
            if ($currentImageUrl && filter_var($currentImageUrl, FILTER_VALIDATE_URL)) {
                // If the current image is hosted on Supabase, delete it
                if (strpos($currentImageUrl, env('SUPABASE_URL')) !== false) {
                    $this->smartphoneImageService->deleteImage($currentImageUrl);
                }
                // If local image, delete using previous code
                elseif ($currentImageUrl != asset('images/no-image.png') && file_exists(public_path($currentImageUrl))) {
                    unlink(public_path($currentImageUrl));
                }
            }

            // Upload to Supabase via our service
            $imageUrl = $this->smartphoneImageService->processAndUpload(
                $request->file('image'),
                $validatedData['name']
            );

            // If upload succeeded, update image URL
            if ($imageUrl) {
                $smartphone->update(['image_url' => $imageUrl]);
            }
        }

        return redirect()->route('smartphones.index')
            ->with('success', 'Smartphone berhasil diperbarui');
    }

    /**
     * Menghapus data smartphone
     */
    public function destroy(Smartphone $smartphone)
    {
        try {
            // Dapatkan path gambar asli dari database (tanpa accessor)
            $imageUrl = $smartphone->getRawOriginal('image_url');

            // Cek apakah gambar di Supabase atau lokal
            if ($imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                // Jika gambar di Supabase, hapus menggunakan service
                if (strpos($imageUrl, env('SUPABASE_URL')) !== false) {
                    $this->smartphoneImageService->deleteImage($imageUrl);
                }
                // Jika gambar lokal, hapus dengan cara biasa
                elseif ($imageUrl != asset('images/no-image.png') && file_exists(public_path($imageUrl))) {
                    unlink(public_path($imageUrl));
                }
            }

            // Hapus data smartphone
            $smartphone->delete();

            return redirect()->route('smartphones.index')
                ->with('success', 'Smartphone berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error saat menghapus smartphone: ' . $e->getMessage());
            return redirect()->route('smartphones.index')
                ->with('error', 'Terjadi kesalahan saat menghapus smartphone: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form rekomendasi
     */
    public function recommendationForm()
    {
        $criteria = Criteria::all();
        return view('smartphones.recommendation', compact('criteria'));
    }

    /**
     * Mendapatkan rekomendasi smartphone
     */
    public function getRecommendation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'min_budget' => 'required|integer|min:0',
            'max_budget' => 'required|integer|min:0|gt:min_budget',
            'criteria_weights' => 'required|array',
            'criteria_weights.*' => 'required|numeric|min:0|max:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $recommendations = $this->topsisService->getRecommendation(
            $request->min_budget,
            $request->max_budget,
            $request->criteria_weights
        );

        // Batasi hanya 6 rekomendasi teratas
        // $recommendations = $recommendations->take(6);

        return view('smartphones.recommendation-results', [
            'recommendations' => $recommendations,
            'min_budget' => $request->min_budget,
            'max_budget' => $request->max_budget,
            'criteria_weights' => $request->criteria_weights,
        ]);
    }

    // Method to clean up obsolete smartphones (can be called from a scheduled command)
    public function cleanupObsoleteSmartphones()
    {
        $obsoleteSmartphones = Smartphone::where('release_year', '<', now()->year - 2)->get();
        $count = 0;

        foreach ($obsoleteSmartphones as $smartphone) {
            try {
                // Dapatkan path gambar asli dari database (tanpa accessor)
                $imageUrl = $smartphone->getRawOriginal('image_url');

                // Cek apakah gambar di Supabase atau lokal
                if ($imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                    // Jika gambar di Supabase, hapus menggunakan service
                    if (strpos($imageUrl, env('SUPABASE_URL')) !== false) {
                        $this->smartphoneImageService->deleteImage($imageUrl);
                    }
                    // Jika gambar lokal, hapus dengan cara biasa
                    elseif ($imageUrl != asset('images/no-image.png') && file_exists(public_path($imageUrl))) {
                        unlink(public_path($imageUrl));
                    }
                }

                // Hapus data smartphone
                $smartphone->delete();
                $count++;
            } catch (\Exception $e) {
                Log::error('Error saat menghapus smartphone usang: ' . $e->getMessage());
            }
        }

        return $count . ' smartphone usang telah dihapus.';
    }
}