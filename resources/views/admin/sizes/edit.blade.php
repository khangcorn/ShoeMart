@extends('admin.layout')

@section('content')
    <h3>Edit Size</h3>

    <form action="{{ route('sizes.update', $size->attribute_id) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Size Value:</label>
        <input type="text" name="attribute_value" value="{{ $size->attribute_value }}" required>

        <button type="submit">Update</button>
    </form>

    <a href="{{ route('sizes.index') }}">Back to List</a>
@endsection
