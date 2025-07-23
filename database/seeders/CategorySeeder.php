<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $electronics = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Electronic devices and gadgets',
        ]);
        $phones = Category::create([
            'name' => 'Phones & Tablets',
            'slug' => 'phones-tablets',
            'description' => 'Smartphones and tablets',
            'parent_id' => $electronics->id,
        ]);
        $laptops = Category::create([
            'name' => 'Laptops & Computers',
            'slug' => 'laptops-computers',
            'description' => 'Laptops and computers',
            'parent_id' => $electronics->id,
        ]);
        $fashion = Category::create([
            'name' => 'Fashion',
            'slug' => 'fashion',
            'description' => 'Clothing and accessories',
        ]);
        $men = Category::create([
            'name' => 'Men',
            'slug' => 'men',
            'description' => 'Men clothing',
            'parent_id' => $fashion->id,
        ]);
        $women = Category::create([
            'name' => 'Women',
            'slug' => 'women',
            'description' => 'Women clothing',
            'parent_id' => $fashion->id,
        ]);
    }
}
