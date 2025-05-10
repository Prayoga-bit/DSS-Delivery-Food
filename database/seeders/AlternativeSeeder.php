<?php

namespace Database\Seeders;

use App\Models\Alternative;
use Illuminate\Database\Seeder;

class AlternativeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define common food delivery alternatives
        $alternatives = [
            [
                'name' => 'GoFood',
                'code' => 'A1',
                'description' => 'Layanan delivery dari Gojek',
            ],
            [
                'name' => 'GrabFood',
                'code' => 'A2',
                'description' => 'Layanan delivery dari Grab',
            ],
            [
                'name' => 'ShopeeFood',
                'code' => 'A3',
                'description' => 'Layanan delivery dari Shopee',
            ],
        ];
        
        foreach ($alternatives as $alternative) {
            Alternative::create($alternative);
        }
    }
}