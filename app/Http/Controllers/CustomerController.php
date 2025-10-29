<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // ============================================================
    // MANAGER METHODS
    // ============================================================

    /**
     * List all customers (Manager view)
     */
    public function index()
    {
        $customers = Customer::with('user')
            ->paginate(15);

        return view('customers.index', ['customers' => $customers]);
    }

    /**
     * Show customer details (Manager view)
     */
    public function show($id)
    {
        $customer = Customer::with('user', 'machines', 'serviceRequests')
            ->findOrFail($id);

        return view('customers.show', ['customer' => $customer]);
    }

    /**
     * Show edit customer form (Manager)
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);

        return view('customers.edit', ['customer' => $customer]);
    }

    /**
     * Update customer (Manager)
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:2000',
        ]);

        $customer->update($validated);

        return redirect()->route('manager.customers.show', $customer->id)
            ->with('success', 'Customer updated successfully');
    }

    // ============================================================
    // DATA CAPTURER METHODS - NEW!
    // ============================================================

    /**
     * List all customers (Data Capturer view)
     */
    public function capturerIndex()
    {
        $customers = Customer::with('user')
            ->orderBy('company_name', 'asc')
            ->paginate(15);

        return view('data-capturer.customers.index', ['customers' => $customers]);
    }

    /**
     * Show customer details (Data Capturer view)
     */
    public function capturerShow($id)
    {
        $customer = Customer::with('user', 'machines', 'serviceRequests')
            ->findOrFail($id);

        return view('data-capturer.customers.show', ['customer' => $customer]);
    }
}
