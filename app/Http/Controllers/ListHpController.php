<?php

namespace App\Http\Controllers;

use App\Models\Smartphone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ListHpController extends Controller
{
    public function index(Request $request)
    {
        // Pencarian real-time untuk AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return $this->handleAjaxRequest($request);
        }

        // Cek autocomplete suggestions
        if ($request->has('suggest') && $request->suggest) {
            return $this->handleSuggestions($request);
        }

        // Key cache untuk data
        $cacheKey = 'list-hp.data.' . md5(json_encode($request->all()));

        // Ambil data dari cache atau database
        $data = Cache::remember($cacheKey, 3600, function () use ($request) {
            // Mulai dengan query dasar tanpa filter tahun
            $query = Smartphone::query();

            // Pencarian berdasarkan nama atau processor (untuk non-AJAX)
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
                });
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

            // Gunakan cached filter options  
            $filterOptions = Smartphone::getCachedFilterOptions();

            return [
                'smartphones' => $query->paginate(10)->withQueryString(),
                'filterOptions' => $filterOptions
            ];
        });

        // Render view dengan data dari cache
        return view('list-hp.index', [
            'smartphones' => $data['smartphones'],
            'ramOptions' => $data['filterOptions']['ram_options'],
            'storageOptions' => $data['filterOptions']['storage_options'],
            'releaseYearOptions' => $data['filterOptions']['release_year_options']
        ]);
    }

    /**
     * Menangani permintaan AJAX untuk pencarian real-time
     */
    private function handleAjaxRequest(Request $request)
    {
        Log::info('AJAX request received', [
            'query' => $request->all(),
            'is_ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson()
        ]);

        // Buat cache key khusus untuk AJAX request
        $ajaxCacheKey = 'list-hp.ajax.' . md5(json_encode($request->all()));

        // Ambil data dari cache atau database
        $smartphones = Cache::remember($ajaxCacheKey, 60, function () use ($request) {
            // Mulai dengan query dasar tanpa filter tahun
            $query = Smartphone::query();

            $searchTerm = $request->input('search');
            if ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
                });
            }

            // Terapkan filter lainnya untuk query AJAX
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
            $result = $query->limit(20)->get();

            // Tambahkan URL ke data
            return $result->map(function ($smartphone) {
                $smartphone->edit_url = route('smartphones.edit', $smartphone->id);
                $smartphone->delete_url = route('smartphones.destroy', $smartphone->id);
                return $smartphone;
            });
        });

        // Siapkan response
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

    /**
     * Menangani permintaan saran autocomplete
     */
    private function handleSuggestions(Request $request)
    {
        $suggestCacheKey = 'list-hp.suggest.' . $request->suggest;

        $suggestions = Cache::remember($suggestCacheKey, 60, function () use ($request) {
            return Smartphone::where('name', 'like', '%' . $request->suggest . '%')
                ->orWhere('processor', 'like', '%' . $request->suggest . '%')
                ->limit(5)
                ->get(['id', 'name', 'processor', 'image_url']);
        });

        return response()->json($suggestions);
    }
}