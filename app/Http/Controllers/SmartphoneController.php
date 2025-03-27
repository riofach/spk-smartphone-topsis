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
            'image' => 'required|image|mimes:png|max:2048',
            'description' => 'required|string',
            'release_year' => 'required|integer|min:2000|max:' . date('Y'),
            'ram' => 'required|numeric|min:1',
            'storage' => 'required|numeric|min:1',
            'processor' => 'required|string|max:255',
            'battery' => 'required|numeric|min:1000',
            'camera' => 'required|numeric|min:1',
            'screen_size' => 'required|numeric|min:1',
            'model_3d_url' => 'nullable|url|max:255',
            'camera_score' => 'required|numeric|min:0|max:10',
            'performance_score' => 'required|numeric|min:0|max:10',
            'design_score' => 'required|numeric|min:0|max:10',
            'battery_score' => 'required|numeric|min:0|max:10',
        ]);

        try {
            $imagePath = null;

            // Process image if uploaded
            if ($request->hasFile('image')) {
                if ($this->smartphoneImageService) {
                    $imagePath = $this->smartphoneImageService->processAndUpload($request->file('image'), $request->name);
                } else {
                    $imagePath = $this->saveImageToLocal($request->file('image'));
                }
            }

            // Create smartphone
            $smartphone = Smartphone::create([
                'name' => $request->name,
                'price' => $request->price,
                'image_url' => $imagePath,
                'description' => $request->description,
                'release_year' => $request->release_year,
                'ram' => $request->ram,
                'storage' => $request->storage,
                'processor' => $request->processor,
                'battery' => $request->battery,
                'camera' => $request->camera,
                'screen_size' => $request->screen_size,
                'model_3d_url' => $request->model_3d_url,
                'camera_score' => $request->camera_score,
                'performance_score' => $request->performance_score,
                'design_score' => $request->design_score,
                'battery_score' => $request->battery_score,
            ]);

            return redirect()->route('smartphones.index')
                ->with('success', 'Smartphone berhasil ditambahkan');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan smartphone: ' . $e->getMessage())
                ->withInput();
        }
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
            'image' => 'nullable|image|mimes:png|max:2048',
            'description' => 'required|string',
            'release_year' => 'required|integer|min:2000|max:' . date('Y'),
            'ram' => 'required|numeric|min:1',
            'storage' => 'required|numeric|min:1',
            'processor' => 'required|string|max:255',
            'battery' => 'required|numeric|min:1000',
            'camera' => 'required|numeric|min:1',
            'screen_size' => 'required|numeric|min:1',
            'model_3d_url' => 'nullable|url|max:255',
            'camera_score' => 'required|numeric|min:0|max:10',
            'performance_score' => 'required|numeric|min:0|max:10',
            'design_score' => 'required|numeric|min:0|max:10',
            'battery_score' => 'required|numeric|min:0|max:10',
        ];

        $request->validate($rules);

        try {
            $data = $request->except(['image', '_token', '_method']);

            // Proses image jika ada
            if ($request->hasFile('image')) {
                $oldImageUrl = $smartphone->getRawOriginal('image_url');

                // Hapus image lama jika bukan default
                if (!empty($oldImageUrl) && !str_contains($oldImageUrl, 'no-image.png')) {
                    // Hapus file gambar lama
                    if (file_exists(public_path($oldImageUrl))) {
                        unlink(public_path($oldImageUrl));
                    }
                }

                // Simpan gambar baru
                if ($this->smartphoneImageService) {
                    $data['image_url'] = $this->smartphoneImageService->processAndUpload($request->file('image'), $request->name);
                } else {
                    $data['image_url'] = $this->saveImageToLocal($request->file('image'));
                }
            }

            // No need to calculate scores, use the values from the form
            $smartphone->update($data);

            return redirect()->route('smartphones.index')
                ->with('success', 'Data smartphone berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data smartphone: ' . $e->getMessage())
                ->withInput();
        }
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

    /**
     * Menyimpan gambar ke penyimpanan lokal
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @return string
     */
    private function saveImageToLocal($image)
    {
        $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $filename);
        return 'images/' . $filename;
    }

    /**
     * Menghitung skor kamera berdasarkan megapixel
     *
     * @param int $megapixels
     * @return float
     */
    private function calculateCameraScore($megapixels)
    {
        // Skor dasar dari 1-10 berdasarkan megapixel kamera
        if ($megapixels >= 108)
            return 10;
        if ($megapixels >= 64)
            return 9;
        if ($megapixels >= 48)
            return 8;
        if ($megapixels >= 32)
            return 7;
        if ($megapixels >= 16)
            return 6;
        if ($megapixels >= 12)
            return 5;
        if ($megapixels >= 8)
            return 4;
        if ($megapixels >= 5)
            return 3;
        if ($megapixels >= 2)
            return 2;
        return 1;
    }

    /**
     * Menghitung skor performa berdasarkan RAM dan prosesor
     *
     * @param int $ram
     * @param string $processor
     * @return float
     */
    private function calculatePerformanceScore($ram, $processor)
    {
        // Skor RAM (0-5 poin)
        $ramScore = 0;
        if ($ram >= 16)
            $ramScore = 5;
        else if ($ram >= 12)
            $ramScore = 4;
        else if ($ram >= 8)
            $ramScore = 3;
        else if ($ram >= 6)
            $ramScore = 2;
        else if ($ram >= 4)
            $ramScore = 1;

        // Skor prosesor (0-5 poin) - basic scoring based on keywords
        $processorScore = 0;
        $highEndKeywords = ['snapdragon 8', 'a16', 'a17', 'm1', 'm2', '9 gen', '8+ gen', '888', '8 gen'];
        $midHighKeywords = ['snapdragon 7', 'dimensity 9', 'dimensity 8', 'a15', 'a14', '778', '7 gen'];
        $midRangeKeywords = ['snapdragon 6', 'dimensity 7', 'helio g9', 'a13', '695', '6 gen'];
        $lowMidKeywords = ['snapdragon 4', 'dimensity 6', 'helio g8', 'a12', '480', '4 gen'];

        $processorLower = strtolower($processor);

        foreach ($highEndKeywords as $keyword) {
            if (strpos($processorLower, $keyword) !== false) {
                $processorScore = 5;
                break;
            }
        }

        if ($processorScore == 0) {
            foreach ($midHighKeywords as $keyword) {
                if (strpos($processorLower, $keyword) !== false) {
                    $processorScore = 4;
                    break;
                }
            }
        }

        if ($processorScore == 0) {
            foreach ($midRangeKeywords as $keyword) {
                if (strpos($processorLower, $keyword) !== false) {
                    $processorScore = 3;
                    break;
                }
            }
        }

        if ($processorScore == 0) {
            foreach ($lowMidKeywords as $keyword) {
                if (strpos($processorLower, $keyword) !== false) {
                    $processorScore = 2;
                    break;
                }
            }
        }

        // Default score for other processors
        if ($processorScore == 0) {
            $processorScore = 1;
        }

        // Combine scores (RAM + Processor) with adjustment to 10-point scale
        return min(10, $ramScore + $processorScore);
    }

    /**
     * Menghitung skor baterai berdasarkan kapasitas
     *
     * @param int $capacity
     * @return float
     */
    private function calculateBatteryScore($capacity)
    {
        // Skor 1-10 berdasarkan kapasitas baterai (mAh)
        if ($capacity >= 6000)
            return 10;
        if ($capacity >= 5500)
            return 9;
        if ($capacity >= 5000)
            return 8;
        if ($capacity >= 4500)
            return 7;
        if ($capacity >= 4000)
            return 6;
        if ($capacity >= 3500)
            return 5;
        if ($capacity >= 3000)
            return 4;
        if ($capacity >= 2500)
            return 3;
        if ($capacity >= 2000)
            return 2;
        return 1;
    }

    /**
     * Menghitung skor desain berdasarkan ukuran layar
     *
     * @param float $screenSize
     * @return float
     */
    private function calculateDesignScore($screenSize)
    {
        // Asumsi ukuran 6.1-6.7 inch adalah ideal (skor tinggi)
        // Di luar itu, akan mendapat skor lebih rendah
        if ($screenSize >= 6.1 && $screenSize <= 6.7)
            return 9;
        if ($screenSize >= 5.9 && $screenSize < 6.1)
            return 8;
        if ($screenSize > 6.7 && $screenSize <= 7.0)
            return 8;
        if ($screenSize >= 5.7 && $screenSize < 5.9)
            return 7;
        if ($screenSize > 7.0 && $screenSize <= 7.5)
            return 7;
        if ($screenSize >= 5.5 && $screenSize < 5.7)
            return 6;
        if ($screenSize > 7.5 && $screenSize <= 8.0)
            return 6;
        if ($screenSize >= 5.0 && $screenSize < 5.5)
            return 5;
        if ($screenSize > 8.0)
            return 5;
        if ($screenSize >= 4.5 && $screenSize < 5.0)
            return 4;
        if ($screenSize >= 4.0 && $screenSize < 4.5)
            return 3;
        if ($screenSize < 4.0)
            return 2;

        // Default value
        return 5;
    }
}