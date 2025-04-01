@extends('admin.layout')

@section('content')
    <h3>Edit Color</h3>

    <form action="{{ route('colors.update', $size->attribute_id) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Size Value:</label>
        <input type="text" name="attribute_value" value="{{ $size->attribute_value }}" required>

        <button type="submit">Update</button>
    </form>

    <a href="{{ route(colors.index') }}">Back to List</a>
@endsection
