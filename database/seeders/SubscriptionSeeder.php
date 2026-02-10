<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Subscription::insert([
            ['name' => 'Basic', 'max_users' => 10, 'max_analyses' => 100, 'price' => 49.99],
            ['name' => 'Pro', 'max_users' => 50, 'max_analyses' => 500, 'price' => 99.99],
            ['name' => 'Enterprise', 'max_users' => 200, 'max_analyses' => 2000, 'price' => 299.99],
        ]);
    }
}
