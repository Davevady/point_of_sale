<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $customers = Customer::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('customers.index', compact('customers', 'search'));
    }

    public function findByNik(Request $request)
    {
        $request->validate([
            'nik' => 'required|string',
        ]);

        $customer = Customer::where('nik', $request->nik)->first();

        if (!$customer) {
            return response()->json([
                'found' => false,
            ]);
        }

        return response()->json([
            'found' => true,
            'customer' => [
                'id' => $customer->id,
                'nik' => $customer->nik,
                'name' => $customer->name,
                'email' => $customer->email,
                'no_tlp' => $customer->no_tlp,
                'address' => $customer->address,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'nik' => 'required|string|max:255|unique:customers,nik',
            'address' => 'nullable|string',
            'no_tlp' => 'nullable|string|max:255',
            'doc_kk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('doc_kk')) {
            $validated['doc_kk'] = $request->file('doc_kk')->store('customers', 'public');
        }

        Customer::create($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'nik' => 'required|string|max:255|unique:customers,nik,' . $customer->id,
            'address' => 'nullable|string',
            'no_tlp' => 'nullable|string|max:255',
            'doc_kk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('doc_kk')) {
            if ($customer->doc_kk && Storage::disk('public')->exists($customer->doc_kk)) {
                Storage::disk('public')->delete($customer->doc_kk);
            }

            $validated['doc_kk'] = $request->file('doc_kk')->store('customers', 'public');
        }

        $customer->update($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        if ($customer->doc_kk && Storage::disk('public')->exists($customer->doc_kk)) {
            Storage::disk('public')->delete($customer->doc_kk);
        }

        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer berhasil dihapus');
    }
}
