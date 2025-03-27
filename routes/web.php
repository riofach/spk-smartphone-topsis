<?php

use App\Http\Controllers\SmartphoneController;
use App\Http\Controllers\ListHpController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Response;
use App\Models\Smartphone;
use Illuminate\Support\Facades\Cache;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Route untuk melihat daftar smartphone
Route::get('/smartphones-icibos', [SmartphoneController::class, 'index'])->name('smartphones.index');
Route::get('/smartphones-icibos/{smartphone}/edit', [SmartphoneController::class, 'edit'])->name('smartphones.edit');
Route::put('/smartphones-icibos/{smartphone}', [SmartphoneController::class, 'update'])->name('smartphones.update');
Route::delete('/smartphones-icibos/{smartphone}', [SmartphoneController::class, 'destroy'])->name('smartphones.destroy');

// Route untuk rekomendasi SPK TOPSIS
Route::get('/recommendation', [SmartphoneController::class, 'recommendationForm'])->name('recommendation.form');
Route::post('/recommendation', [SmartphoneController::class, 'getRecommendation'])->name('recommendation.result');

// Route tersembunyi untuk tambah smartphone - hanya yang tahu URL yang bisa akses
Route::get('/admin-add-smartphone', [SmartphoneController::class, 'create'])->name('smartphones.create');
Route::post('/admin-add-smartphone', [SmartphoneController::class, 'store'])->name('smartphones.store');

Route::get('/list-hp', [ListHpController::class, 'index'])->name('list-hp.index');

// Sitemap.xml route untuk SEO
Route::get('/sitemap.xml', function () {
    // Cache sitemap untuk performa
    $content = Cache::remember('sitemap.xml', 86400, function () {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
$sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    // Add homepage
    $sitemap .= '<url>';
        $sitemap .= '<loc>' . url('/') . '</loc>';
        $sitemap .= '<changefreq>weekly</changefreq>';
        $sitemap .= '<priority>1.0</priority>';
        $sitemap .= '</url>';

    // Add list-hp page
    $sitemap .= '<url>';
        $sitemap .= '<loc>' . route('list-hp.index') . '</loc>';
        $sitemap .= '<changefreq>daily</changefreq>';
        $sitemap .= '<priority>0.9</priority>';
        $sitemap .= '</url>';

    // Add recommendation page
    $sitemap .= '<url>';
        $sitemap .= '<loc>' . route('recommendation.form') . '</loc>';
        $sitemap .= '<changefreq>weekly</changefreq>';
        $sitemap .= '<priority>0.8</priority>';
        $sitemap .= '</url>';

    // Add smartphones
    $smartphones = Smartphone::withinTwoYears()->get();

    foreach ($smartphones as $smartphone) {
    $sitemap .= '<url>';
        $sitemap .= '<loc>' . route('list-hp.index') . '?id=' . $smartphone->id . '</loc>';
        $sitemap .= '<lastmod>' . $smartphone->updated_at->toAtomString() . '</lastmod>';
        $sitemap .= '<changefreq>monthly</changefreq>';
        $sitemap .= '<priority>0.7</priority>';
        $sitemap .= '</url>';
    }

    $sitemap .= '</urlset>';

return $sitemap;
});

return (new Response($content, 200))
->header('Content-Type', 'application/xml');
});