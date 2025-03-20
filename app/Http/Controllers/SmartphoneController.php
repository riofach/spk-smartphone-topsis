<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use App\Models\Smartphone;
use App\Services\TopsisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
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
    private function createDirectoryIfNotExists()
    {
        $storagePath = storage_path('app/public/smartphones');
        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
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
    public function index()
    {
        $smartphones = Smartphone::orderBy('name')->get();
        $criteria = Criteria::all();

        return view('smartphones.index', compact('smartphones', 'criteria'));
    }

    /**
     * Menampilkan form tambah smartphone
     */
    public function create()
    {
        return view('smartphones.create');
    }

    /**
     * Menyimpan data smartphone baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'camera_score' => 'required|numeric|min:0|max:10',
            'performance_score' => 'required|numeric|min:0|max:10',
            'design_score' => 'required|numeric|min:0|max:10',
            'battery_score' => 'required|numeric|min:0|max:10',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:png|max:1024',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

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

            $data['image'] = $imageName;
        }

        Smartphone::create($data);

        return redirect()->route('smartphones.index')
            ->with('success', 'Smartphone berhasil ditambahkan');
    }

    /**
     * Menampilkan form edit smartphone
     */
    public function edit(Smartphone $smartphone)
    {
        return view('smartphones.edit', compact('smartphone'));
    }

    /**
     * Mengupdate data smartphone
     */
    public function update(Request $request, Smartphone $smartphone)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'camera_score' => 'required|numeric|min:0|max:10',
            'performance_score' => 'required|numeric|min:0|max:10',
            'design_score' => 'required|numeric|min:0|max:10',
            'battery_score' => 'required|numeric|min:0|max:10',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:png|max:1024',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($smartphone->image) {
                $oldImagePath = public_path('images/smartphones/' . $smartphone->image);
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

            $data['image'] = $imageName;
        }

        $smartphone->update($data);

        return redirect()->route('smartphones.index')
            ->with('success', 'Smartphone berhasil diperbarui');
    }

    /**
     * Menghapus data smartphone
     */
    public function destroy(Smartphone $smartphone)
    {
        // Delete image if exists
        if ($smartphone->image) {
            $imagePath = public_path('images/smartphones/' . $smartphone->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        $smartphone->delete();

        return redirect()->route('smartphones.index')
            ->with('success', 'Smartphone berhasil dihapus');
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

        return view('smartphones.recommendation-results', [
            'recommendations' => $recommendations,
            'min_budget' => $request->min_budget,
            'max_budget' => $request->max_budget,
            'criteria_weights' => $request->criteria_weights,
        ]);
    }
}