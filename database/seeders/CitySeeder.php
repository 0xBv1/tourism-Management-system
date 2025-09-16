<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            [
                'name' => 'Cairo',
                'slug' => 'cairo',
            ],
            [
                'name' => 'Alexandria',
                'slug' => 'alexandria',
            ],
            [
                'name' => 'Luxor',
                'slug' => 'luxor',
            ],
            [
                'name' => 'Aswan',
                'slug' => 'aswan',
            ],
            [
                'name' => 'Hurghada',
                'slug' => 'hurghada',
            ],
            [
                'name' => 'Sharm El Sheikh',
                'slug' => 'sharm-el-sheikh',
            ],
            [
                'name' => 'Dahab',
                'slug' => 'dahab',
            ],
            [
                'name' => 'Marsa Alam',
                'slug' => 'marsa-alam',
            ],
            [
                'name' => 'El Gouna',
                'slug' => 'el-gouna',
            ],
            [
                'name' => 'Port Said',
                'slug' => 'port-said',
            ],
            [
                'name' => 'Siwa',
                'slug' => 'siwa',
            ],
            [
                'name' => 'Fayoum',
                'slug' => 'fayoum',
            ],
            [
                'name' => 'Giza',
                'slug' => 'giza',
            ],
            [
                'name' => 'Suez',
                'slug' => 'suez',
            ],
            [
                'name' => 'Ismailia',
                'slug' => 'ismailia',
            ],
            [
                'name' => 'Mansoura',
                'slug' => 'mansoura',
            ],
            [
                'name' => 'Tanta',
                'slug' => 'tanta',
            ],
            [
                'name' => 'Zagazig',
                'slug' => 'zagazig',
            ],
            [
                'name' => 'Assiut',
                'slug' => 'assiut',
            ],
            [
                'name' => 'Sohag',
                'slug' => 'sohag',
            ],
            [
                'name' => 'Qena',
                'slug' => 'qena',
            ],
            [
                'name' => 'Minya',
                'slug' => 'minya',
            ],
            [
                'name' => 'Beni Suef',
                'slug' => 'beni-suef',
            ],
            [
                'name' => 'El Arish',
                'slug' => 'el-arish',
            ],
            [
                'name' => 'Ras Gharib',
                'slug' => 'ras-gharib',
            ],
            [
                'name' => 'Safaga',
                'slug' => 'safaga',
            ],
            [
                'name' => 'El Quseir',
                'slug' => 'el-quseir',
            ],
            [
                'name' => 'Berenice',
                'slug' => 'berenice',
            ],
            [
                'name' => 'Abu Simbel',
                'slug' => 'abu-simbel',
            ],
            [
                'name' => 'Kom Ombo',
                'slug' => 'kom-ombo',
            ],
            [
                'name' => 'Edfu',
                'slug' => 'edfu',
            ],
            [
                'name' => 'Esna',
                'slug' => 'esna',
            ],
            [
                'name' => 'Dendera',
                'slug' => 'dendera',
            ],
            [
                'name' => 'Abydos',
                'slug' => 'abydos',
            ],
            [
                'name' => 'El Minya',
                'slug' => 'el-minya',
            ],
            [
                'name' => 'El Faiyum',
                'slug' => 'el-faiyum',
            ],
            [
                'name' => 'El Kharga',
                'slug' => 'el-kharga',
            ],
            [
                'name' => 'El Dakhla',
                'slug' => 'el-dakhla',
            ],
            [
                'name' => 'El Bahariya',
                'slug' => 'el-bahariya',
            ],
            [
                'name' => 'El Farafra',
                'slug' => 'el-farafra',
            ],
        ];

        foreach ($cities as $cityData) {
            // Check if city already exists
            $existingCity = City::where('name', $cityData['name'])->first();
            
            if (!$existingCity) {
                City::create($cityData);
            }
        }

        $this->command->info('Cities seeded successfully!');
    }
}
