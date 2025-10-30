@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Customers</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            @if(isset($customers) && $customers->count() > 0)
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Company</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                            <tr>
                                <td>#{{ $customer->id }}</td>
                                <td>{{ $customer->name ?? $customer->customer_name ?? 'N/A' }}</td>
                                <td>{{ $customer->email ?? 'N/A' }}</td>
                                <td>{{ $customer->phone ?? $customer->phone_number ?? 'N/A' }}</td>
                                <td>{{ $customer->company ?? $customer->company_name ?? 'N/A' }}</td>
                                <td>
                                    <a href="/manager/customers/{{ $customer->id }}" class="btn btn-sm btn-info">
                                        View
                                    </a>
                                    <a href="/manager/customers/{{ $customer->id }}/edit" class="btn btn-sm btn-warning">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $customers->links() }}
                </div>
            @else
                <p class="text-center py-4">No customers found.</p>
            @endif
        </div>
    </div>
</div>
@endsection
