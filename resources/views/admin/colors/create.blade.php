@extends('admin.layout')

@section('content')
    <div class="max-w-lg mx-auto bg-white shadow-lg rounded-lg p-6 mt-10">
        <h3 class="text-2xl font-semibold text-gray-700 mb-4 text-center">Create Color</h3>

        <form action="{{ route('colors.store') }}" method="POST" onsubmit="return validateColor()">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-600 font-medium">Color Value:</label>
                <input type="text" id="attribute_value" name="attribute_value" required
                    class="w-full mt-2 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none">
                
                <!-- Hiển thị lỗi nếu có -->
                @if (session('error'))
                    <div class="bg-red-500 text-white p-2 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <div id="colorError" class="text-red-500 mt-2 hidden"></div>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('colors.index') }}" class="text-green-500 hover:underline">
                    ← Back to List
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition">
                    Create
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
