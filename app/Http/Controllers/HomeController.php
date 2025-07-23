<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')
            ->with(['children' => function($query) {
                $query->withCount('products');
            }])
            ->whereNull('parent_id')
            ->get();

        $featuredProducts = Product::with('category')
            ->where('is_featured', true)
            ->where('is_active', true)
            ->latest()
            ->take(5)
            ->get();

        $recentProducts = Product::with('category')
            ->where('is_active', true)
            ->latest()
            ->take(6)
            ->get();

        return view('home', compact('categories', 'featuredProducts', 'recentProducts'));
    }
} 