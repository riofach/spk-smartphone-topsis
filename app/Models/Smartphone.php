<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Smartphone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'image_url',
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