<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('categories.view'), 403);

        $search  = $request->search;
        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 10;

        $productCategories = ProductCategory::query()
            ->withCount('products')
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('product-categories.index', compact('productCategories', 'search', 'perPage'));
    }

    public function create()
    {
        abort_unless(auth()->user()->hasPermission('categories.create'), 403);

        return view('product-categories.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('categories.create'), 403);
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
        abort_unless(auth()->user()->hasPermission('categories.view'), 403);

        return view('product-categories.show', compact('productCategory'));
    }

    public function edit(ProductCategory $productCategory)
    {
        abort_unless(auth()->user()->hasPermission('categories.edit'), 403);

        return view('product-categories.edit', compact('productCategory'));
    }

    public function update(Request $request, ProductCategory $productCategory)
    {
        abort_unless(auth()->user()->hasPermission('categories.edit'), 403);
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
        abort_unless(auth()->user()->hasPermission('categories.delete'), 403);

        $productCategory->delete();

        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Kategori produk berhasil dihapus.');
    }
}
