<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display all customers (Manager only)
     */
    public function index()
    {
        $customers = Customer::with('user', 'machines', 'serviceRequests')
            ->paginate(15);

        return view('customers.index', ['customers' => $customers]);
    }

    /**
     * Show a specific customer's details
     */
    public function show($id)
    {
        $customer = Customer::with('user', 'machines', 'serviceRequests', 'invoices')
            ->findOrFail($id);

        return view('customers.show', ['customer' => $customer]);
    }

    /**
     * Show customer edit form
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.edit', ['customer' => $customer]);
    }

    /**
     * Update customer information
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.show', $customer->id)
            ->with('success', 'Customer updated successfully');
    }
}