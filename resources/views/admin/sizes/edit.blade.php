@extends('admin.layout')

@section('content')
    <div class="py-4 px-4">
        <h2 class="text-3xl font-bold mb-6">Chỉnh sửa kích thước</h3>
        <form action="{{ route('sizes.update', $size->attribute_id) }}" method="POST" class="space-y-4" onsubmit="return validateSize()">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-gray-700 font-medium mb-2">Size Value:</label>
                <input type="text" id="attribute_value" name="attribute_value" value="{{ old('attribute_value', $size->attribute_value) }}" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                
                <!-- Hiển thị lỗi nếu có -->
                @if (session('error'))
                    <div class="bg-red-500 text-white p-2 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <div id="sizeError" class="text-red-500 mt-2 hidden"></div>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('sizes.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded ">
                    Quay lại 
                </a>

                <button type="submit"
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                    Cập nhật
                </button>
            </div>
        </form>
    </div>

    <script>
        function validateSize() {
            let sizeInput = document.getElementById("attribute_value").value.trim();
            let errorDiv = document.getElementById("sizeError");

            // Kiểm tra xem input có phải là số không
            let sizeNumber = parseInt(sizeInput);

            if (isNaN(sizeNumber) || sizeNumber < 37 || sizeNumber > 45) {
                errorDiv.textContent = "Size phải là số từ 37 đến 45.";
                errorDiv.classList.remove("hidden");
                return false; // Ngăn form gửi đi
            } else {
                errorDiv.classList.add("hidden");
                return true; // Cho phép gửi form
            }
        }
    </script>
@endsection
