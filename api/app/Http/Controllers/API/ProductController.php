<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use App\Traits\RequestResponse;
use Illuminate\Http\Request;
use Exception;

class ProductController
{
    use RequestResponse;

    public function getAllProducts(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);
            $search = $request->query('search');
            $category = $request->query('category');
            $sortBy = $request->query('sort_by', 'name');
            $sortDir = $request->query('sort_dir', 'asc');

            $query = Product::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                    ->orWhere('sku', 'like', "%$search%");
                });
            }

            if ($category) {
                $query->where('category', $category);
            }

            $allowedSorts = ['name', 'sku', 'category', 'quantity', 'price'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortDir);
            }

            return $query->paginate($perPage);
        } catch (Exception $e) {
            return $this->internalServerError("Failed to retrieve products: " . $e->getMessage());
        }
    }

    public function getCategories()
    {
        try {
            $categories = Product::select('category')->distinct()->pluck('category');
            return $this->success("Categories retrieved successfully", ['categories' => $categories]);
        } catch (Exception $e) {
            return $this->internalServerError("Failed to retrieve categories: " . $e->getMessage());
        }
    }

    public function filterProducts(Request $request)
    {
        try {
            $query = Product::query();

            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            return $query->get();
        } catch (Exception $e) {
            return $this->internalServerError("Failed to filter products: " . $e->getMessage());
        }
    }
}
