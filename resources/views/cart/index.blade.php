@extends('layouts.app')

@section('content')
<div class="container">
    <style>
      .table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
}

.table th {
    background-color: #343a40;
    color: white;
}

    </style>
    <h2>Giỏ hàng của bạn</h2>

    @if(isset($message))
        <div class="alert alert-warning">{{ $message }}</div>
    @elseif(isset($cart) && $cart->details->isNotEmpty())
        <table class="table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tổng</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cart->details as $detail)
                    <tr>
                        <td>{{ $detail->product->name ?? 'Sản phẩm không tồn tại' }}</td>
                        <td>{{ number_format($detail->product->price, 0, ',', '.') }} đ</td>
                        <td>{{ $detail->quantity }}</td>
                        <td>{{ number_format($detail->product->price * $detail->quantity, 0, ',', '.') }} đ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p><strong>Tổng tiền: </strong> 
            {{ number_format($cart->details->sum(fn($detail) => $detail->product->price * $detail->quantity), 0, ',', '.') }} đ
        </p>

    @else
        <p>Giỏ hàng trống.</p>
        <a href="{{ route('products.index') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
    @endif
</div>

<form method="GET" action="{{ route('cart.show') }}">
  <label for="user_id">Chọn người dùng:</label>
  <select name="user_id" id="user_id" onchange="this.form.submit()">
      @foreach ($users as $user)
          <option value="{{ $user->user_id }}" {{ $selectedUserId == $user->user_id ? 'selected' : '' }}>
              {{ $user->username }}
          </option>
      @endforeach
  </select>
</form>

@endsection
