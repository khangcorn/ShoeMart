@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Shipping Fee</h2>
    <form action="{{ route('shipping_fees.update', $shippingFee->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Province</label>
            <input type="text" name="province" class="form-control" value="{{ $shippingFee->province }}" required>
        </div>
        <div class="mb-3">
            <label>District</label>
            <input type="text" name="district" class="form-control" value="{{ $shippingFee->district }}">
        </div>
        <div class="mb-3">
            <label>Ward</label>
            <input type="text" name="ward" class="form-control" value="{{ $shippingFee->ward }}">
        </div>
        <div class="mb-3">
            <label>Fee</label>
            <input type="number" step="0.01" name="fee" class="form-control" value="{{ $shippingFee->fee }}" required>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
    </form>
</div>
@endsection