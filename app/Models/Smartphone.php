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
        'camera_score',
        'performance_score',
        'design_score',
        'battery_score',
        'description',
        'image',
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
     * Get image URL attribute
     *
     * @return string
     */
    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return asset('images/no-image.png');
        }

        return asset('images/smartphones/' . $this->image);
    }
}