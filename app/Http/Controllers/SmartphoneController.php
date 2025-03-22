<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use App\Models\Smartphone;
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

    public function __construct(TopsisService $topsisService)
    {
        $this->topsisService = $topsisService;

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

        // Pencarian berdasarkan nama atau processor
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('processor', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // Pencarian real-time untuk AJAX
        if ($request->has('query')) {
            $searchTerm = $request->input('query');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('processor', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%');
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
            'image' => 'required|image|mimes:png|max:1024',
            'ram' => 'required|integer|min:1',
            'storage' => 'required|integer|min:8',
            'processor' => 'required|string',
            'battery' => 'required|integer|min:1000',
            'camera' => 'required|integer|min:5',
            'screen_size' => 'required|numeric|min:3|max:10',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::slug($request->name) . '-' . time() . '.' . $image->getClientOriginalExtension();

            // Store image directly to public/images/smartphones
            $uploadPath = public_path('images/smartphones');

            // Buat direktori jika belum ada
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            // Simpan gambar
            $image->move($uploadPath, $imageName);

            Smartphone::create([
                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
                'camera_score' => $request->camera_score,
                'performance_score' => $request->performance_score,
                'design_score' => $request->design_score,
                'battery_score' => $request->battery_score,
                'release_year' => $request->release_year,
                'image_url' => 'images/smartphones/' . $imageName,
                'ram' => $request->ram,
                'storage' => $request->storage,
                'processor' => $request->processor,
                'battery' => $request->battery,
                'camera' => $request->camera,
                'screen_size' => $request->screen_size,
            ]);
        } else {
            Smartphone::create([
                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
                'camera_score' => $request->camera_score,
                'performance_score' => $request->performance_score,
                'design_score' => $request->design_score,
                'battery_score' => $request->battery_score,
                'release_year' => $request->release_year,
                'image_url' => 'images/no-image.png',
                'ram' => $request->ram,
                'storage' => $request->storage,
                'processor' => $request->processor,
                'battery' => $request->battery,
                'camera' => $request->camera,
                'screen_size' => $request->screen_size,
            ]);
        }

        return redirect()->route('smartphones.index')
            ->with('success', 'Smartphone berhasil ditambahkan');
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
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'camera_score' => 'required|numeric|min:1|max:10',
            'performance_score' => 'required|numeric|min:1|max:10',
            'design_score' => 'required|numeric|min:1|max:10',
            'battery_score' => 'required|numeric|min:1|max:10',
            'release_year' => 'required|integer|min:' . (now()->year - 2) . '|max:' . now()->year,
            'image' => 'nullable|image|mimes:png|max:1024',
            'ram' => 'required|integer|min:1',
            'storage' => 'required|integer|min:8',
            'processor' => 'required|string',
            'battery' => 'required|integer|min:1000',
            'camera' => 'required|integer|min:5',
            'screen_size' => 'required|numeric|min:3|max:10',
        ]);

        // Update data dasar
        $smartphone->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'camera_score' => $request->camera_score,
            'performance_score' => $request->performance_score,
            'design_score' => $request->design_score,
            'battery_score' => $request->battery_score,
            'release_year' => $request->release_year,
            'ram' => $request->ram,
            'storage' => $request->storage,
            'processor' => $request->processor,
            'battery' => $request->battery,
            'camera' => $request->camera,
            'screen_size' => $request->screen_size,
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists and not default image
            $oldImagePath = null;
            if (!empty($smartphone->getRawOriginal('image_url')) && $smartphone->getRawOriginal('image_url') != 'images/no-image.png') {
                $oldImagePath = public_path($smartphone->getRawOriginal('image_url'));
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            $image = $request->file('image');
            $imageName = Str::slug($request->name) . '-' . time() . '.' . $image->getClientOriginalExtension();

            // Store image directly to public/images/smartphones
            $uploadPath = public_path('images/smartphones');

            // Buat direktori jika belum ada
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            // Simpan gambar
            $image->move($uploadPath, $imageName);

            // Update image_url
            $smartphone->update([
                'image_url' => 'images/smartphones/' . $imageName,
            ]);
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
            $imagePath = $smartphone->getRawOriginal('image_url');

            // Hapus gambar dari direktori jika bukan gambar default
            if (!empty($imagePath) && $imagePath != 'images/no-image.png') {
                $fullImagePath = public_path($imagePath);
                if (file_exists($fullImagePath)) {
                    unlink($fullImagePath);
                    Log::info('Gambar berhasil dihapus: ' . $fullImagePath);
                } else {
                    Log::warning('Gambar tidak ditemukan: ' . $fullImagePath);
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
                $imagePath = $smartphone->getRawOriginal('image_url');

                // Hapus gambar dari direktori jika bukan gambar default
                if (!empty($imagePath) && $imagePath != 'images/no-image.png') {
                    $fullImagePath = public_path($imagePath);
                    if (file_exists($fullImagePath)) {
                        unlink($fullImagePath);
                        Log::info('Gambar smartphone usang berhasil dihapus: ' . $fullImagePath);
                    } else {
                        Log::warning('Gambar smartphone usang tidak ditemukan: ' . $fullImagePath);
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