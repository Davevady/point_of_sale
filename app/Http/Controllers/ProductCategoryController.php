<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $productCategories = ProductCategory::query()
            ->withCount('products')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        return view('product-categories.index', compact('productCategories', 'search'));
    }

    public function create()
    {
        return view('product-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name',
        ]);

        ProductCategory::create($validated);

        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Kategori produk berhasil ditambahkan.');
    }

    public function show(ProductCategory $productCategory)
    {
        return view('product-categories.show', compact('productCategory'));
    }

    public function edit(ProductCategory $productCategory)
    {
        return view('product-categories.edit', compact('productCategory'));
    }

    public function update(Request $request, ProductCategory $productCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name,' . $productCategory->id,
        ]);

        $productCategory->update($validated);

        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Kategori produk berhasil diupdate.');
    }

    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();

        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Kategori produk berhasil dihapus.');
    }
}
