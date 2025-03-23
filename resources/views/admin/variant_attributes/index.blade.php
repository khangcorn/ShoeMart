@extends('admin.layout')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Thuộc Tính Biến Thể</h1>
        <a href="{{ route('variant_attributes.create') }}" class="btn btn-primary mb-4">Thêm Thuộc Tính</a>

        <table class="table">
            <thead>
                <tr>
                    <th>Attribute Name</th>
                    <th>Attribute Value</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($variantAttributes as $attribute)
                    <tr>
                        <td>{{ $attribute->attribute_name }}</td>
                        <td>{{ $attribute->attribute_value }}</td>
                        <td>
                            <a href="{{ route('variant_attributes.edit', [ $attribute->attribute_id]) }}" class="btn btn-warning">Sửa</a>
                            <form action="{{ route('variant_attributes.destroy', [ $attribute->attribute_id]) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
