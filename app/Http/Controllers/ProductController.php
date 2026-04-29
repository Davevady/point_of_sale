<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('products.view'), 403);

        $search = $request->search;
        $category = $request->category;

        $products = Product::with('category')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($category, function ($query) use ($category) {
                $query->where('product_category_id', $category);
            })
            ->latest()
            ->get();

        $categories = ProductCategory::orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'search', 'category'));
    }

    public function create()
    {
        abort_unless(auth()->user()->hasPermission('products.create'), 403);

        $categories = ProductCategory::orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('products.create'), 403);

        $validated = $request->validate([
            'product_category_id' => 'required|exists:product_categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        Product::create($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        abort_unless(auth()->user()->hasPermission('products.view'), 403);

        $product->load('category');

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        abort_unless(auth()->user()->hasPermission('products.edit'), 403);

        $categories = ProductCategory::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        abort_unless(auth()->user()->hasPermission('products.edit'), 403);

        $validated = $request->validate([
            'product_category_id' => 'required|exists:product_categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product->update($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil diupdate.');
    }

    public function destroy(Product $product)
    {
        abort_unless(auth()->user()->hasPermission('products.delete'), 403);

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}