<?php

namespace App\Helpers;

class ScoreColorHelper
{
    /**
     * Mendapatkan warna badge berdasarkan nilai skor
     *
     * @param float $score
     * @return string
     */
    public static function getColor($score)
    {
        if ($score >= 9) {
            return 'success';
        }
        if ($score >= 7) {
            return 'primary';
        }
        if ($score >= 5) {
            return 'info';
        }
        if ($score >= 3) {
            return 'warning';
        }
        return 'danger';
    }
}