<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'name' => 'Egypt',
                'code' => 'EG',
                'phone_code' => '+20',
                'flag' => 'EG',
                'active' => true,
            ],
            [
                'name' => 'Saudi Arabia',
                'code' => 'SA',
                'phone_code' => '+966',
                'flag' => 'SA',
                'active' => true,
            ],
            [
                'name' => 'United Arab Emirates',
                'code' => 'AE',
                'phone_code' => '+971',
                'flag' => 'AE',
                'active' => true,
            ],
            [
                'name' => 'Kuwait',
                'code' => 'KW',
                'phone_code' => '+965',
                'flag' => 'KW',
                'active' => true,
            ],
            [
                'name' => 'Qatar',
                'code' => 'QA',
                'phone_code' => '+974',
                'flag' => 'QA',
                'active' => true,
            ],
            [
                'name' => 'Bahrain',
                'code' => 'BH',
                'phone_code' => '+973',
                'flag' => 'BH',
                'active' => true,
            ],
            [
                'name' => 'Oman',
                'code' => 'OM',
                'phone_code' => '+968',
                'flag' => 'OM',
                'active' => true,
            ],
            [
                'name' => 'Jordan',
                'code' => 'JO',
                'phone_code' => '+962',
                'flag' => 'JO',
                'active' => true,
            ],
            [
                'name' => 'Lebanon',
                'code' => 'LB',
                'phone_code' => '+961',
                'flag' => 'LB',
                'active' => true,
            ],
            [
                'name' => 'Iraq',
                'code' => 'IQ',
                'phone_code' => '+964',
                'flag' => 'IQ',
                'active' => true,
            ],
            [
                'name' => 'Syria',
                'code' => 'SY',
                'phone_code' => '+963',
                'flag' => 'SY',
                'active' => true,
            ],
            [
                'name' => 'Palestine',
                'code' => 'PS',
                'phone_code' => '+970',
                'flag' => 'PS',
                'active' => true,
            ],
            [
                'name' => 'Yemen',
                'code' => 'YE',
                'phone_code' => '+967',
                'flag' => 'YE',
                'active' => true,
            ],
            [
                'name' => 'Turkey',
                'code' => 'TR',
                'phone_code' => '+90',
                'flag' => 'TR',
                'active' => true,
            ],
            [
                'name' => 'Iran',
                'code' => 'IR',
                'phone_code' => '+98',
                'flag' => 'IR',
                'active' => true,
            ],
            [
                'name' => 'Pakistan',
                'code' => 'PK',
                'phone_code' => '+92',
                'flag' => 'PK',
                'active' => true,
            ],
            [
                'name' => 'India',
                'code' => 'IN',
                'phone_code' => '+91',
                'flag' => 'IN',
                'active' => true,
            ],
            [
                'name' => 'United States',
                'code' => 'US',
                'phone_code' => '+1',
                'flag' => 'US',
                'active' => true,
            ],
            [
                'name' => 'United Kingdom',
                'code' => 'GB',
                'phone_code' => '+44',
                'flag' => 'GB',
                'active' => true,
            ],
            [
                'name' => 'Germany',
                'code' => 'DE',
                'phone_code' => '+49',
                'flag' => 'DE',
                'active' => true,
            ],
            [
                'name' => 'France',
                'code' => 'FR',
                'phone_code' => '+33',
                'flag' => 'FR',
                'active' => true,
            ],
            [
                'name' => 'Italy',
                'code' => 'IT',
                'phone_code' => '+39',
                'flag' => 'IT',
                'active' => true,
            ],
            [
                'name' => 'Spain',
                'code' => 'ES',
                'phone_code' => '+34',
                'flag' => 'ES',
                'active' => true,
            ],
            [
                'name' => 'Canada',
                'code' => 'CA',
                'phone_code' => '+1',
                'flag' => 'CA',
                'active' => true,
            ],
            [
                'name' => 'Australia',
                'code' => 'AU',
                'phone_code' => '+61',
                'flag' => 'AU',
                'active' => true,
            ],
            [
                'name' => 'China',
                'code' => 'CN',
                'phone_code' => '+86',
                'flag' => 'CN',
                'active' => true,
            ],
            [
                'name' => 'Japan',
                'code' => 'JP',
                'phone_code' => '+81',
                'flag' => 'JP',
                'active' => true,
            ],
            [
                'name' => 'South Korea',
                'code' => 'KR',
                'phone_code' => '+82',
                'flag' => 'KR',
                'active' => true,
            ],
            [
                'name' => 'Russia',
                'code' => 'RU',
                'phone_code' => '+7',
                'flag' => 'RU',
                'active' => true,
            ],
            [
                'name' => 'Brazil',
                'code' => 'BR',
                'phone_code' => '+55',
                'flag' => 'BR',
                'active' => true,
            ],
            [
                'name' => 'Argentina',
                'code' => 'AR',
                'phone_code' => '+54',
                'flag' => 'AR',
                'active' => true,
            ],
            [
                'name' => 'Mexico',
                'code' => 'MX',
                'phone_code' => '+52',
                'flag' => 'MX',
                'active' => true,
            ],
            [
                'name' => 'South Africa',
                'code' => 'ZA',
                'phone_code' => '+27',
                'flag' => 'ZA',
                'active' => true,
            ],
            [
                'name' => 'Nigeria',
                'code' => 'NG',
                'phone_code' => '+234',
                'flag' => 'NG',
                'active' => true,
            ],
            [
                'name' => 'Kenya',
                'code' => 'KE',
                'phone_code' => '+254',
                'flag' => 'KE',
                'active' => true,
            ],
            [
                'name' => 'Morocco',
                'code' => 'MA',
                'phone_code' => '+212',
                'flag' => 'MA',
                'active' => true,
            ],
            [
                'name' => 'Algeria',
                'code' => 'DZ',
                'phone_code' => '+213',
                'flag' => 'DZ',
                'active' => true,
            ],
            [
                'name' => 'Tunisia',
                'code' => 'TN',
                'phone_code' => '+216',
                'flag' => 'TN',
                'active' => true,
            ],
            [
                'name' => 'Libya',
                'code' => 'LY',
                'phone_code' => '+218',
                'flag' => 'LY',
                'active' => true,
            ],
            [
                'name' => 'Sudan',
                'code' => 'SD',
                'phone_code' => '+249',
                'flag' => 'SD',
                'active' => true,
            ],
            [
                'name' => 'Ethiopia',
                'code' => 'ET',
                'phone_code' => '+251',
                'flag' => 'ET',
                'active' => true,
            ],
        ];

        foreach ($countries as $countryData) {
            Country::updateOrCreate(
                ['code' => $countryData['code']],
                $countryData
            );
        }

        $this->command->info('Countries seeded successfully!');
    }
}
