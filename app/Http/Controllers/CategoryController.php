<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function show($slug, Request $request)
    {
        $category = Category::with(['subcategories'])->where('slug', $slug)->firstOrFail();
        $sort = $request->input('sort', 'recommended');
        $query = $category->products()->newQuery();

        // Sorting
        switch ($sort) {
            case 'newest':
                $query->orderByDesc('created_at');
                break;
            case 'lowest':
                $query->orderBy('price');
                break;
            case 'highest':
                $query->orderByDesc('price');
                break;
            default:
                $query->orderByDesc('views'); // recommended/trending
        }

        // Filtering by attributes (multi-select)
        $activeFilters = collect($request->except(['sort', 'min_price', 'max_price']))->filter();
        if ($activeFilters->count()) {
            $query->where(function($q) use ($activeFilters) {
                foreach ($activeFilters as $key => $value) {
                    if (is_array($value)) {
                        $q->where(function($subQ) use ($key, $value) {
                            foreach ($value as $v) {
                                $subQ->orWhereJsonContains('attributes->' . $key, $v);
                            }
                        });
                    } else {
                        $q->whereJsonContains('attributes->' . $key, $value);
                    }
                }
            });
        }
        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        // Gather all attribute keys for filters (add price)
        $allAttributes = $category->products()->pluck('attributes')->filter()->map(function($attr) {
            // If $attr is already an array (due to model casting), use it directly
            if (is_array($attr)) {
                return array_keys($attr);
            }
            // If it's a JSON string, decode it
            return array_keys(json_decode($attr, true));
        })->flatten()->unique();
        $filters = $allAttributes->values();
        $filters = $filters->merge(['price']);

        $products = $query->get();
        
        // Get all categories for sidebar
        $categories = Category::with(['subcategories', 'products'])
            ->withCount('products')
            ->whereNull('parent_id')
            ->get();

        return view('category', [
            'category' => $category,
            'products' => $products,
            'filters' => $filters,
            'sort' => $sort,
            'activeFilters' => $activeFilters,
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'categories' => $categories,
        ]);
    }
}
