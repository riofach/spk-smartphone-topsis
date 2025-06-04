<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Smartphone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'image_url',
        'model_3d_url',
        'description',
        'camera_score',
        'performance_score',
        'design_score',
        'battery_score',
        'release_year',
        'ram',
        'storage',
        'processor',
        'battery',
        'camera',
        'screen_size',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'camera_score' => 'decimal:1',
        'performance_score' => 'decimal:1',
        'design_score' => 'decimal:1',
        'battery_score' => 'decimal:1',
        'release_year' => 'integer',
        'ram' => 'integer',
        'storage' => 'integer',
        'battery' => 'integer',
        'camera' => 'integer',
        'screen_size' => 'decimal:1',
    ];

    /**
     * Boot method untuk model
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when model is saved or deleted
        static::saved(function () {
            self::clearModelCache();
        });

        static::deleted(function () {
            self::clearModelCache();
        });

        // Clear cache when model is created
        static::created(function () {
            self::clearModelCache();
        });

        // Clear cache when model is updated
        static::updated(function () {
            self::clearModelCache();
        });
    }

    /**
     * Clear all caches related to smartphones
     */
    public static function clearModelCache()
    {
        Cache::forget('smartphones.all');
        Cache::forget('smartphones.recent');
        Cache::forget('smartphones.filters');
        Cache::forget('smartphones.yearly');
        Cache::forget('smartphones.ram_options');
        Cache::forget('smartphones.storage_options');
        Cache::forget('smartphones.release_year_options');

        // Hapus semua cache list-hp
        $cacheKeys = Cache::get('list-hp.cache_keys', []);
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Hapus cache dengan prefix list-hp secara manual
        foreach (['list-hp.data', 'list-hp.ajax', 'list-hp.suggest'] as $prefix) {
            Cache::forget($prefix);
        }
    }

    /**
     * Get all smartphones with caching
     */
    public static function getCachedAll()
    {
        return Cache::remember('smartphones.all', 3600, function () {
            return self::all();
        });
    }

    /**
     * Get recent smartphones with caching
     */
    public static function getCachedRecent($limit = 10)
    {
        return Cache::remember('smartphones.recent', 3600, function () use ($limit) {
            return self::withinTwoYears()
                ->latest()
                ->take($limit)
                ->get();
        });
    }

    /**
     * Get filter options with caching
     */
    public static function getCachedFilterOptions()
    {
        // Hapus cache filter options untuk memastikan data terbaru
        Cache::forget('smartphones.filters');

        return Cache::remember('smartphones.filters', 3600, function () {
            return [
                'ram_options' => self::select('ram')->distinct()->orderBy('ram')->pluck('ram'),
                'storage_options' => self::select('storage')->distinct()->orderBy('storage')->pluck('storage'),
                'release_year_options' => self::select('release_year')->distinct()->orderBy('release_year', 'desc')->pluck('release_year'),
            ];
        });
    }

    /**
     * Get the criteria scores for this smartphone as array.
     *
     * @return array
     */
    public function getCriteriaScores(): array
    {
        return [
            'camera' => $this->camera_score,
            'performance' => $this->performance_score,
            'design' => $this->design_score,
            'battery' => $this->battery_score,
        ];
    }

    /**
     * Get the image URL attribute
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if (empty($this->attributes['image_url'])) {
            return asset('images/no-image.png');
        }

        return asset($this->attributes['image_url']);
    }

    /**
     * Get the 3D model URL attribute with default fallback
     *
     * @return string|null
     */
    public function getModel3dUrlAttribute()
    {
        if (empty($this->attributes['model_3d_url'])) {
            return null;
        }

        return $this->attributes['model_3d_url'];
    }

    // Scope for filtering only smartphones released within the last 2 years
    public function scopeWithinTwoYears($query)
    {
        $currentYear = now()->year;
        return $query->where('release_year', '>=', $currentYear - 2);
    }

    // Check if the smartphone is obsolete (more than 2 years old)
    public function isObsolete()
    {
        $currentYear = now()->year;
        return $this->release_year < ($currentYear - 2);
    }
}