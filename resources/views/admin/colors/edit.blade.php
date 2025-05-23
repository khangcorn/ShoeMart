@extends('admin.layout')

@section('content')
    <div class="px-4 py-4">
        <h3 class="text-3xl font-bold mb-6">Chỉnh sửa màu sắc</h3>

        <form action="{{ route('colors.update', $color->attribute_id) }}" method="POST" class="space-y-4" onsubmit="return validateColor()">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-gray-700 font-medium mb-2">Giá trị màu sắc:</label>
                <input type="text" id="attribute_value" name="attribute_value" value="{{ $color->attribute_value }}" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                
                <!-- Hiển thị lỗi nếu có -->
                @if (session('error'))
                    <div class="bg-red-500 text-white p-2 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <div id="colorError" class="text-red-500 mt-2 hidden"></div>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('colors.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded ">
                    Quay lại 
                </a>

                <button type="submit"
                class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 transition">
                    Cập nhật
                </button>
            </div>
        </form>
    </div>

    <script>
        function validateColor() {
            let colorInput = document.getElementById("attribute_value").value.trim().toLowerCase();
            let errorDiv = document.getElementById("colorError");

            // Danh sách các màu hợp lệ (bạn có thể cập nhật danh sách này)
            let validColors = ["red", "blue", "green", "yellow", "black", "white", "orange", "pink", "purple", "brown", "gray"];

            if (!validColors.includes(colorInput)) {
                errorDiv.textContent = "Chỉ được nhập các màu hợp lệ: " + validColors.join(", ");
                errorDiv.classList.remove("hidden");
                return false; // Ngăn form gửi đi
            } else {
                errorDiv.classList.add("hidden");
                return true; // Cho phép gửi form
            }
        }
    </script>
@endsection
