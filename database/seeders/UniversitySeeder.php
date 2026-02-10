<?php

namespace Database\Seeders;

use App\Models\University;
use Illuminate\Database\Seeder;

class UniversitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        University::insert([

            [
                'name' => 'ISIPA',
                'slug' => 'isipa',
                'description' => 'Grande université privee',
                'address' => 'Kinshasa, RDC',
                'phone' => '899765677',
                'admin_id' => 1, // On relie à l'admin déjà créé
                // 'subscription_id' => 1
            ],
            [
                'name' => 'Université de Kinshasa',
                'slug' => 'universite-de-kinshasa',
                'description' => 'Grande université publique',
                'address' => 'Kinshasa, RDC',
                'phone' => '123456789',
                'admin_id' => 1, // On relie à l'admin déjà créé
                // 'subscription_id' => 1
            ],
            [
                'name' => 'Université de Lubumbashi',
                'slug' => 'universite-de-lubumbashi',
                'description' => 'Université dans le Katanga',
                'address' => 'Lubumbashi, RDC',
                'phone' => '987654321',
                'admin_id' => 2,
                // 'subscription_id' => 2
            ],
        ]);
    }
}
