<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');

        // Get categories
        $makananKucing = \App\Models\Category::where('name', 'Makanan Kucing')->first();
        $makananAnjing = \App\Models\Category::where('name', 'Makanan Anjing')->first();
        $vitamin = \App\Models\Category::where('name', 'Vitamin')->first();

        $kucingId = $makananKucing ? $makananKucing->id : 1;
        $anjingId = $makananAnjing ? $makananAnjing->id : 2;
        $vitaminId = $vitamin ? $vitamin->id : 3;

        // Realistic product pool
        $catProducts = [
            'Whiskas Kitten Tuna 400g',
            'Whiskas Adult Salmon 1.2kg',
            'Royal Canin Fit 32 2kg',
            'Royal Canin Kitten 400g',
            'Me-O Salmon Dry Cat Food 1.2kg',
            'Me-O Creamy Treats Salmon & Scallop',
            'Felibite Salmon & Tuna Shape 500g',
            'Pro Plan Adult Housecat 1.3kg',
        ];

        $dogProducts = [
            'Pedigree Puppy Beef & Milk 1.5kg',
            'Pedigree Adult Chicken & Vegetables 3kg',
            'Royal Canin Mini Adult 2kg',
            'Royal Canin Golden Retriever Puppy 3kg',
            'Alpo Beef, Liver & Vegetable 1.5kg',
            'Science Diet Adult Small Paws 1.5kg',
            'Bolt Beef Kibble Dog Food 1kg',
        ];

        $vitaminProducts = [
            'Nutriplus Gel Multivitamin 120.5g',
            'Fish Oil Omega 3 Softgels (50 pcs)',
            'Virbac Megaderm Skin Supplement 280ml',
            'PetTabs Multivitamin Tablets (60 tabs)',
            'Excel Brewer\'s Yeast Skin & Coat',
            'Lactol Milk Replacer Powder 250g',
        ];

        // Seed Cat Products
        foreach ($catProducts as $name) {
            $purchase = $faker->numberBetween(15, 120) * 1000;
            $selling = $purchase + ($faker->numberBetween(3, 30) * 1000);
            
            \App\Models\Product::create([
                'category_id' => $kucingId,
                'name' => $name,
                'description' => "Makanan kucing berkualitas tinggi untuk memenuhi kebutuhan nutrisi harian kesayangan Anda. Varian rasa yang disukai kucing.",
                'stock' => $faker->numberBetween(5, 80),
                'purchase_price' => $purchase,
                'selling_price' => $selling,
            ]);
        }

        // Seed Dog Products
        foreach ($dogProducts as $name) {
            $purchase = $faker->numberBetween(25, 180) * 1000;
            $selling = $purchase + ($faker->numberBetween(5, 45) * 1000);
            
            \App\Models\Product::create([
                'category_id' => $anjingId,
                'name' => $name,
                'description' => "Makanan anjing bernutrisi seimbang untuk menjaga stamina, bulu sehat, dan pencernaan yang optimal pada anjing Anda.",
                'stock' => $faker->numberBetween(5, 50),
                'purchase_price' => $purchase,
                'selling_price' => $selling,
            ]);
        }

        // Seed Vitamin Products
        foreach ($vitaminProducts as $name) {
            $purchase = $faker->numberBetween(30, 220) * 1000;
            $selling = $purchase + ($faker->numberBetween(10, 60) * 1000);
            
            \App\Models\Product::create([
                'category_id' => $vitaminId,
                'name' => $name,
                'description' => "Suplemen dan vitamin tambahan untuk membantu pertumbuhan, memperkuat imun tubuh, serta menjaga kesehatan kulit dan bulu.",
                'stock' => $faker->numberBetween(3, 40),
                'purchase_price' => $purchase,
                'selling_price' => $selling,
            ]);
        }
    }
}
