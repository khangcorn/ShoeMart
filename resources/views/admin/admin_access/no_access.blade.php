@extends('admin.layout')

@section('content')
<style>
    .container {
        max-width: 600px;
        background-color: #f8f9fa;
        padding: 40px 30px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        margin: 50px auto;
    }
    h2 {
        color: #dc3545; /* đỏ nổi bật */
        margin-bottom: 20px;
    }
    p {
        font-size: 18px;
        margin-bottom: 30px;
        color: #555;
    }
    button.btn-primary {
        background-color: #007bff;
        border: none;
        padding: 12px 30px;
        font-size: 16px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }
    button.btn-primary:hover {
        background-color: #0056b3;
    }
     .flash-message {
        background-color: #fff; /* nền trắng */
        color: #333;
        border: 1px solid #ccc;
        padding: 15px 20px;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        max-width: 500px;
        margin: 20px auto;
        text-align: center;
        font-weight: 600;
        position: relative;
        animation: fadeOut 1s ease forwards;
        animation-delay: 4s; /* đợi 4s rồi mới fade */
    }

    @keyframes fadeOut {
        to {
            opacity: 0;
            height: 0;
            padding: 0 20px;
            margin: 0 auto;
            overflow: hidden;
        }
    }
</style>

<div class="container text-center mt-5">
    <h2>Bạn không có quyền truy trang Admin</h2>
</div>
@endsection
<script>
    setTimeout(() => {
        const flash = document.getElementById('flash-message');
        if (flash) {
            flash.style.display = 'none';
        }
    }, 5000); // 5 giây
</script>