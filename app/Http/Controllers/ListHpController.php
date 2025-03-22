<?php

namespace App\Http\Controllers;

use App\Models\Smartphone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ListHpController extends Controller
{
    public function index(Request $request)
    {
        $query = Smartphone::withinTwoYears();

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

        // Pencarian berdasarkan nama atau processor (untuk non-AJAX)
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('processor', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%');
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

        $smartphones = $query->paginate(10)->withQueryString();

        // Get unique values for filters
        $ramOptions = Smartphone::select('ram')->distinct()->orderBy('ram')->pluck('ram');
        $storageOptions = Smartphone::select('storage')->distinct()->orderBy('storage')->pluck('storage');
        $releaseYearOptions = Smartphone::select('release_year')->distinct()->orderBy('release_year', 'desc')->pluck('release_year');

        // For autocomplete suggestions
        if ($request->has('suggest') && $request->suggest) {
            $suggestions = Smartphone::where('name', 'like', '%' . $request->suggest . '%')
                ->orWhere('processor', 'like', '%' . $request->suggest . '%')
                ->limit(5)
                ->get(['id', 'name', 'processor', 'image_url']);

            return response()->json($suggestions);
        }

        return view('list-hp.index', compact(
            'smartphones',
            'ramOptions',
            'storageOptions',
            'releaseYearOptions'
        ));
    }
}