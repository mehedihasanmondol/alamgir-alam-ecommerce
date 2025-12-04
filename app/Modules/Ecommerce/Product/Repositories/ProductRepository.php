<?php

namespace App\Modules\Ecommerce\Product\Repositories;

use App\Modules\Ecommerce\Product\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
    public function all(): Collection
    {
        return Product::with(['categories', 'brand', 'defaultVariant', 'images'])->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Product::with(['categories', 'brand', 'variants', 'images']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['category_id'])) {
            $query->whereHas('categories', function ($subQuery) use ($filters) {
                $subQuery->where('categories.id', $filters['category_id']);
            });
        }

        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (!empty($filters['product_type'])) {
            $query->where('product_type', $filters['product_type']);
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        // Filter by status (draft, published, etc.)
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        } else {
            // By default, hide draft products in admin panel
            $query->where('status', '!=', 'draft');
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    public function find(int $id): ?Product
    {
        return Product::with(['categories', 'brand', 'variants', 'images'])->find($id);
    }

    public function findBySlug(string $slug): ?Product
    {
        return Product::with(['categories', 'brand', 'variants', 'images'])
            ->where('slug', $slug)
            ->first();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): bool
    {
        return $product->update($data);
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    public function getFeatured(int $limit = 10): Collection
    {
        return Product::with(['defaultVariant', 'primaryImage'])
            ->featured()
            ->active()
            ->limit($limit)
            ->get();
    }

    public function getByCategory(int $categoryId, int $perPage = 12): LengthAwarePaginator
    {
        return Product::with(['defaultVariant', 'primaryImage', 'categories'])
            ->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('categories.id', $categoryId);
            })
            ->active()
            ->paginate($perPage);
    }

    public function getByBrand(int $brandId, int $perPage = 12): LengthAwarePaginator
    {
        return Product::with(['defaultVariant', 'primaryImage'])
            ->where('brand_id', $brandId)
            ->active()
            ->paginate($perPage);
    }

    public function search(string $query, int $perPage = 12): LengthAwarePaginator
    {
        return Product::with(['defaultVariant', 'primaryImage'])
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('description', 'like', '%' . $query . '%')
                  ->orWhere('short_description', 'like', '%' . $query . '%');
            })
            ->active()
            ->paginate($perPage);
    }

    public function getAllActive(): Collection
    {
        return Product::with(['defaultVariant'])
            ->active()
            ->orderBy('name')
            ->get();
    }
}
