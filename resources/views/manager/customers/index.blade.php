@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Customers</h1>

    <div class="card">
        <div class="card-body">
            @if(isset($customers) && $customers->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                            <tr>
                                <td>{{ $customer->id }}</td>
                                <td>{{ $customer->name ?? 'N/A' }}</td>
                                <td>{{ $customer->email ?? 'N/A' }}</td>
                                <td>{{ $customer->phone ?? 'N/A' }}</td>
                                <td>
                                    <a href="/manager/customers/{{ $customer->id }}" class="btn btn-sm btn-primary">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $customers->links() }}
            @else
                <p>No customers found.</p>
            @endif
        </div>
    </div>
</div>
@endsection
