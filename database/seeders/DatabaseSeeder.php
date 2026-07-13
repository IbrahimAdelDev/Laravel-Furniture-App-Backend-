<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ar_SA');

        User::create([
            'name' => 'إبراهيم عادل',
            'phone' => '01028089643',
            'password' => Hash::make('88888888'),
            'role' => 'super_admin',
        ]);

        $admin1 = User::create([
            'name' => $faker->name,
            'phone' => '011' . $faker->numerify('########'), 
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => $faker->name,
            'phone' => '012' . $faker->numerify('########'),
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);

        Setting::updateOrCreate(
            ['key' => 'whatsapp_number'],
            ['value' => $admin1->phone]
        );

        for ($i = 0; $i < 7; $i++) {
            User::create([
                'name' => $faker->name,
                'phone' => '015' . $faker->numerify('########'),
                'password' => Hash::make('12345678'),
                'role' => 'user',
            ]);
        }

        $productNames = [
            'طقم أنتريه مودرن قطيفة', 'غرفة نوم ماستر كلاسيك', 'طقم سفرة خشب زان', 
            'ركنة حرف L رمادي', 'نيش زجاجي 3 درفة', 'سرير أطفال دورين', 
            'دولاب جرار 2.5 متر', 'كرسي هزاز خشب طبيعي', 'مكتب دراسة زان', 'ترابيزة تلفزيون سمارت'
        ];
        
        foreach ($productNames as $name) {
            $product = Product::create([
                'name' => $name,
                'description' => 'هذا المنتج مصنوع من أجود أنواع الأخشاب الطبيعية، تصميم عصري يناسب كافة الأذواق، ويتميز بمتانة عالية تعيش لسنوات طويلة. ' . $faker->realText(50),
                'price' => $faker->numberBetween(5000, 35000), 
            ]);

            $imageCount = rand(3, 8);
            for ($j = 1; $j <= $imageCount; $j++) {
                $randomImageName = rand(1, 15) . '.webp';
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => 'products/' . $randomImageName
                ]);
            }
        }

        $this->command->info('Database seeded successfully with super admin, admins, users, products, and product images.');
    }
}