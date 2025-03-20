<?php

namespace Database\Seeders;

use App\Models\Smartphone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SmartphoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $smartphones = [
            [
                'name' => 'Samsung Galaxy S21',
                'price' => 10999000,
                'camera_score' => 9.2,
                'performance_score' => 9.0,
                'design_score' => 8.5,
                'battery_score' => 8.7,
                'description' => 'Flagship Samsung dengan kamera dan performa premium',
                'image' => null,
            ],
            [
                'name' => 'iPhone 13',
                'price' => 14499000,
                'camera_score' => 9.4,
                'performance_score' => 9.5,
                'design_score' => 8.8,
                'battery_score' => 7.8,
                'description' => 'Flagship Apple dengan ekosistem tertutup dan performa tinggi',
                'image' => null,
            ],
            [
                'name' => 'Xiaomi Redmi Note 10',
                'price' => 2799000,
                'camera_score' => 7.5,
                'performance_score' => 7.0,
                'design_score' => 7.2,
                'battery_score' => 8.5,
                'description' => 'Smartphone kelas menengah dengan harga terjangkau',
                'image' => null,
            ],
            [
                'name' => 'OPPO Reno 7',
                'price' => 4999000,
                'camera_score' => 8.0,
                'performance_score' => 7.5,
                'design_score' => 8.2,
                'battery_score' => 7.8,
                'description' => 'Smartphone dengan fokus pada kamera dan desain',
                'image' => null,
            ],
            [
                'name' => 'Realme GT Neo 2',
                'price' => 5499000,
                'camera_score' => 7.8,
                'performance_score' => 8.5,
                'design_score' => 7.5,
                'battery_score' => 7.9,
                'description' => 'Smartphone yang fokus pada performa gaming',
                'image' => null,
            ],
            [
                'name' => 'Poco F3',
                'price' => 4399000,
                'camera_score' => 7.5,
                'performance_score' => 8.7,
                'design_score' => 7.0,
                'battery_score' => 8.2,
                'description' => 'Smartphone dengan performa tinggi di kelas menengah',
                'image' => null,
            ],
            [
                'name' => 'Vivo V23',
                'price' => 5299000,
                'camera_score' => 8.5,
                'performance_score' => 7.8,
                'design_score' => 8.6,
                'battery_score' => 7.7,
                'description' => 'Smartphone dengan fokus pada kamera selfie dan desain',
                'image' => null,
            ],
        ];

        foreach ($smartphones as $smartphone) {
            Smartphone::updateOrCreate(
                ['name' => $smartphone['name']],
                $smartphone
            );
        }
    }
}