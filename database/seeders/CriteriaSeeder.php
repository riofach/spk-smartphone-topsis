<?php

namespace Database\Seeders;

use App\Models\Criteria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $criteria = [
            [
                'name' => 'Kamera',
                'code' => 'camera',
                'weight' => 1.0,
                'type' => 'benefit',
                'description' => 'Kualitas kamera smartphone',
            ],
            [
                'name' => 'Performa',
                'code' => 'performance',
                'weight' => 1.0,
                'type' => 'benefit',
                'description' => 'Performa dan kecepatan smartphone',
            ],
            [
                'name' => 'Desain',
                'code' => 'design',
                'weight' => 1.0,
                'type' => 'benefit',
                'description' => 'Desain dan build quality smartphone',
            ],
            [
                'name' => 'Baterai',
                'code' => 'battery',
                'weight' => 1.0,
                'type' => 'benefit',
                'description' => 'Kapasitas dan daya tahan baterai smartphone',
            ],
        ];

        foreach ($criteria as $criterion) {
            Criteria::updateOrCreate(
                ['code' => $criterion['code']],
                $criterion
            );
        }
    }
}