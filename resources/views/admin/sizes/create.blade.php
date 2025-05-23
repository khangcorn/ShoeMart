@extends('admin.layout')

@section('content')
    <div class="py-4 px-4">
        <h2 class="text-3xl font-bold mb-6">Thêm mới kích thước</h3>

        <form action="{{ route('sizes.store') }}" method="POST" onsubmit="return validateSize()">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-600 font-medium">Giá trị kích cỡ</label>
                <input type="number" id="attribute_value" name="attribute_value" required
                    class="w-full mt-2 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                
                <!-- Hiển thị lỗi nếu có -->
                @if (session('error'))
                    <div class="bg-red-500 text-white p-2 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <div id="sizeError" class="text-red-500 mt-2 hidden"></div>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('sizes.index') }}" class="text-blue-500 hover:underline">
                    Quay lại 
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 transition">
                    Create
                </button>
            </div>
        </form>
    </div>

    <script>
        function validateSize() {
            let sizeInput = document.getElementById("attribute_value").value;
            let errorDiv = document.getElementById("sizeError");

            // Chuyển giá trị sang số nguyên
            let size = parseInt(sizeInput);

            if (isNaN(size) || size < 37 || size > 45) {
                errorDiv.textContent = "Size phải nằm trong khoảng 37 đến 45.";
                errorDiv.classList.remove("hidden");
                return false; // Ngăn form gửi đi
            } else {
                errorDiv.classList.add("hidden");
                return true; // Cho phép gửi form
            }
        }
    </script>
@endsection
