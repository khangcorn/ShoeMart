@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Shipping Fees</h2>
    <a href="{{ route('shipping_fees.create') }}" class="btn btn-primary">Add New</a>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Province</th>
                <th>District</th>
                <th>Ward</th>
                <th>Fee</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($shippingFees as $fee)
                <tr>
                    <td>{{ $fee->id }}</td>
                    <td>{{ $fee->province }}</td>
                    <td>{{ $fee->district }}</td>
                    <td>{{ $fee->ward }}</td>
                    <td>{{ number_format($fee->fee, 2) }}</td>
                    <td>
                        <a href="{{ route('shipping_fees.edit', $fee->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('shipping_fees.destroy', $fee->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure?');">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection