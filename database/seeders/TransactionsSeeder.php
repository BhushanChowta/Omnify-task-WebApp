<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // DB::table('customers')->insert([
        //     ['id' => 1, 'family_id' => 1, 'email' => 'customer1@example.com', 'name' => 'John'],
        //     ['id' => 2, 'family_id' => 2, 'email' => 'customer2@example.com', 'name' => 'Jane'],
        //     ['id' => 3, 'family_id' => 1, 'email' => 'customer3@example.com', 'name' => 'Alice'],
        //     ['id' => 4, 'family_id' => 3, 'email' => 'customer4@example.com', 'name' => 'Bob'],
        //     ['id' => 5, 'family_id' => 2, 'email' => 'customer5@example.com', 'name' => 'Charlie'],
        // ]);

        DB::table('transactions')->insert([
            ['id' => 1, 'customer_id' => 2, 'services' => '{"event":"456"}','finalAmount' => 200.00, 'discountAmount' => 10.00, 'status' => 'SUCCESS', 'discount_id' => 2],
            ['id' => 2, 'customer_id' => 1, 'services' => '{"class":"789"}','finalAmount' => 200.00, 'discountAmount' => 10.00,  'status' => 'SUCCESS', 'discount_id' => 1],
            ['id' => 3, 'customer_id' => 5, 'services' => '{"appointments":"101"}','finalAmount' => 200.00, 'discountAmount' => 10.00,  'status' => 'SUCCESS', 'discount_id' => 3],
            ['id' => 4, 'customer_id' => 4, 'services' => '{"classpack":"202"}','finalAmount' => 200.00, 'discountAmount' => 10.00,  'status' => 'SUCCESS', 'discount_id' => 3],
            ['id' => 5, 'customer_id' => 3, 'services' => '{"facilities":"303"}','finalAmount' => 200.00, 'discountAmount' => 10.00,  'status' => 'SUCCESS', 'discount_id' => 3],
        ]);
    }
}
