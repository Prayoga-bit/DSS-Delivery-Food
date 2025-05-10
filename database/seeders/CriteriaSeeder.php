<?php

namespace Database\Seeders;

use App\Models\Criteria;
use Illuminate\Database\Seeder;

class CriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define the 6 fixed criteria for food delivery apps
        $criterias = [
            [
                'name' => 'Pelayanan',
                'code' => 'C1',
                'description' => 'Kualitas layanan terhadap pelanggan',
                'is_cost' => false, // Higher is better
            ],
            [
                'name' => 'Promo',
                'code' => 'C2',
                'description' => 'Penawaran diskon dan promo',
                'is_cost' => false, // Higher is better
            ],
            [
                'name' => 'Fitur',
                'code' => 'C3',
                'description' => 'Kelengkapan fitur aplikasi',
                'is_cost' => false, // Higher is better
            ],
            [
                'name' => 'Biaya Antar',
                'code' => 'C4',
                'description' => 'Biaya pengiriman makanan',
                'is_cost' => true, // Lower is better
            ],
            [
                'name' => 'Kecepatan',
                'code' => 'C5',
                'description' => 'Kecepatan pengantaran makanan',
                'is_cost' => false, // Higher is better
            ],
            [
                'name' => 'Keamanan Aplikasi',
                'code' => 'C6',
                'description' => 'Keamanan dan privasi pengguna',
                'is_cost' => false, // Higher is better
            ],
        ];
        
        foreach ($criterias as $criteria) {
            Criteria::create($criteria);
        }
    }
}