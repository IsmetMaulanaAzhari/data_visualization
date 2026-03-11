<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SalesDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Create categories
        $categories = [
            ['name' => 'Elektronik', 'description' => 'Perangkat elektronik dan gadget'],
            ['name' => 'Pakaian', 'description' => 'Fashion pria dan wanita'],
            ['name' => 'Makanan & Minuman', 'description' => 'Produk makanan dan minuman'],
            ['name' => 'Kesehatan', 'description' => 'Produk kesehatan dan kecantikan'],
            ['name' => 'Rumah Tangga', 'description' => 'Perlengkapan rumah tangga'],
            ['name' => 'Olahraga', 'description' => 'Peralatan dan perlengkapan olahraga'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Products per category
        $productData = [
            1 => [ // Elektronik
                ['name' => 'Smartphone Android', 'price' => 3500000],
                ['name' => 'Laptop Gaming', 'price' => 12000000],
                ['name' => 'Headphone Wireless', 'price' => 850000],
                ['name' => 'Smartwatch', 'price' => 1500000],
                ['name' => 'Tablet 10 inch', 'price' => 4500000],
                ['name' => 'Power Bank 20000mAh', 'price' => 350000],
                ['name' => 'Wireless Earbuds', 'price' => 500000],
                ['name' => 'Camera Digital', 'price' => 6500000],
            ],
            2 => [ // Pakaian
                ['name' => 'Kaos Polos Premium', 'price' => 125000],
                ['name' => 'Celana Jeans', 'price' => 350000],
                ['name' => 'Kemeja Formal', 'price' => 275000],
                ['name' => 'Jaket Hoodie', 'price' => 225000],
                ['name' => 'Dress Wanita', 'price' => 450000],
                ['name' => 'Sepatu Sneakers', 'price' => 550000],
                ['name' => 'Topi Baseball', 'price' => 85000],
            ],
            3 => [ // Makanan & Minuman
                ['name' => 'Kopi Arabica 250gr', 'price' => 75000],
                ['name' => 'Teh Premium Box', 'price' => 45000],
                ['name' => 'Coklat Batangan', 'price' => 35000],
                ['name' => 'Snack Sehat Mix', 'price' => 55000],
                ['name' => 'Madu Murni 500ml', 'price' => 125000],
                ['name' => 'Susu UHT 1 Liter', 'price' => 18000],
            ],
            4 => [ // Kesehatan
                ['name' => 'Vitamin C 1000mg', 'price' => 85000],
                ['name' => 'Masker Wajah Set', 'price' => 150000],
                ['name' => 'Sunscreen SPF 50', 'price' => 125000],
                ['name' => 'Serum Wajah', 'price' => 200000],
                ['name' => 'Hand Sanitizer 500ml', 'price' => 45000],
                ['name' => 'Multivitamin', 'price' => 175000],
            ],
            5 => [ // Rumah Tangga
                ['name' => 'Set Panci Stainless', 'price' => 450000],
                ['name' => 'Blender Multifungsi', 'price' => 650000],
                ['name' => 'Dispenser Air', 'price' => 350000],
                ['name' => 'Vacuum Cleaner', 'price' => 1200000],
                ['name' => 'Rice Cooker Digital', 'price' => 550000],
                ['name' => 'Set Pisau Dapur', 'price' => 275000],
            ],
            6 => [ // Olahraga
                ['name' => 'Dumbbell Set 20kg', 'price' => 450000],
                ['name' => 'Yoga Mat Premium', 'price' => 175000],
                ['name' => 'Sepeda Statis', 'price' => 2500000],
                ['name' => 'Raket Badminton', 'price' => 350000],
                ['name' => 'Bola Basket', 'price' => 225000],
                ['name' => 'Skipping Rope', 'price' => 65000],
            ],
        ];

        $products = [];
        foreach ($productData as $categoryId => $items) {
            foreach ($items as $item) {
                $products[] = Product::create([
                    'category_id' => $categoryId,
                    'name' => $item['name'],
                    'description' => $faker->sentence(10),
                    'price' => $item['price'],
                    'stock' => $faker->numberBetween(20, 200),
                ]);
            }
        }

        // Create customers
        $cities = ['Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang', 'Makassar', 'Palembang', 'Tangerang', 'Depok', 'Bekasi'];
        
        for ($i = 0; $i < 50; $i++) {
            Customer::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->streetAddress,
                'city' => $faker->randomElement($cities),
            ]);
        }

        // Create orders spanning last 12 months
        $statuses = ['pending', 'processing', 'completed', 'completed', 'completed', 'completed', 'cancelled'];
        $customerIds = Customer::pluck('id')->toArray();
        $productIds = Product::pluck('id')->toArray();
        
        for ($i = 0; $i < 500; $i++) {
            $orderDate = $faker->dateTimeBetween('-12 months', 'now');
            
            $order = Order::create([
                'customer_id' => $faker->randomElement($customerIds),
                'order_number' => 'ORD-' . date('Ymd', $orderDate->getTimestamp()) . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'order_date' => $orderDate,
                'status' => $faker->randomElement($statuses),
                'total_amount' => 0,
            ]);

            // Add 1-5 items per order
            $numItems = $faker->numberBetween(1, 5);
            $totalAmount = 0;
            $usedProducts = [];

            for ($j = 0; $j < $numItems; $j++) {
                $productId = $faker->randomElement(array_diff($productIds, $usedProducts));
                $usedProducts[] = $productId;
                
                $product = Product::find($productId);
                $quantity = $faker->numberBetween(1, 3);
                $subtotal = $product->price * $quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;
            }

            $order->update(['total_amount' => $totalAmount]);
        }
    }
}
