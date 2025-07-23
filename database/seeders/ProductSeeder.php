<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phones = Category::where('slug', 'phones-tablets')->first();
        $laptops = Category::where('slug', 'laptops-computers')->first();
        $men = Category::where('slug', 'men')->first();
        $women = Category::where('slug', 'women')->first();

        Product::create([
            'name' => 'iPhone 13 Pro',
            'slug' => 'iphone-13-pro',
            'category_id' => $phones->id,
            'price' => 120000,
            'thumbnail_url' => 'https://images.unsplash.com/photo-1632661674596-6e75d8b5c4c8?w=400&h=300&fit=crop',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1632661674596-6e75d8b5c4c8?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400&h=300&fit=crop',
            ]),
            'description' => 'Latest Apple iPhone 13 Pro smartphone with A15 Bionic chip and Pro camera system.',
            'attributes' => json_encode([
                'condition' => 'New',
                'storage' => '256GB',
                'color' => 'Graphite',
                'battery_health' => '100%',
                'discount' => 15,
            ]),
            'views' => 100,
        ]);
        
        Product::create([
            'name' => 'Samsung Galaxy S22',
            'slug' => 'samsung-galaxy-s22',
            'category_id' => $phones->id,
            'price' => 95000,
            'thumbnail_url' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=400&h=300&fit=crop',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=400&h=300&fit=crop',
            ]),
            'description' => 'Samsung flagship smartphone with Exynos 2200 processor and 108MP camera.',
            'attributes' => json_encode([
                'condition' => 'New',
                'storage' => '128GB',
                'color' => 'Phantom Black',
                'battery_health' => '100%',
                'discount' => 12,
            ]),
            'views' => 80,
        ]);
        
        Product::create([
            'name' => 'Dell XPS 13',
            'slug' => 'dell-xps-13',
            'category_id' => $laptops->id,
            'price' => 140000,
            'thumbnail_url' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=400&h=300&fit=crop',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=400&h=300&fit=crop',
            ]),
            'description' => 'Premium ultrabook laptop with Intel i7 processor and 13.4" InfinityEdge display.',
            'attributes' => json_encode([
                'condition' => 'Refurbished',
                'storage' => '512GB SSD',
                'color' => 'Silver',
                'ram' => '16GB',
                'processor' => 'Intel i7-1165G7',
            ]),
            'views' => 60,
        ]);
        
        Product::create([
            'name' => 'Men T-Shirt',
            'slug' => 'men-tshirt',
            'category_id' => $men->id,
            'price' => 1500,
            'thumbnail_url' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&h=300&fit=crop',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&h=300&fit=crop',
            ]),
            'description' => 'Comfortable cotton t-shirt for men with modern fit and breathable fabric.',
            'attributes' => json_encode([
                'size' => 'L',
                'color' => 'Blue',
                'material' => 'Cotton',
                'brand' => 'Premium',
            ]),
            'views' => 30,
        ]);
        
        Product::create([
            'name' => 'Women Dress',
            'slug' => 'women-dress',
            'category_id' => $women->id,
            'price' => 2500,
            'thumbnail_url' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400&h=300&fit=crop',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400&h=300&fit=crop',
            ]),
            'description' => 'Elegant dress for women with flattering silhouette and premium fabric.',
            'attributes' => json_encode([
                'size' => 'M',
                'color' => 'Red',
                'material' => 'Silk',
                'brand' => 'Elegance',
            ]),
            'views' => 50,
        ]);
    }
}
