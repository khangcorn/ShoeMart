<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>edit Coupon</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #4caf50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #45a049;
        }
        .form-group {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<h2>Edit Coupon</h2>
<a href="{{ route('coupon.index') }}">Quay về trang Coupons Table</a>
<div class="form-container">
    <form action="{{ route('coupon.update',$coupon->id) }}" method="POST">
        @csrf
        @method('PUT')
        <!-- Coupon Code -->
        <div class="form-group">
            <label for="code">Coupon Code:</label>
            <input type="text" value="{{ $coupon->code }}" id="code" name="code" placeholder="nhập code" required>
        </div>

        <!-- Discount Type -->
        <div class="form-group">
            <label for="discount_type">Discount Type:</label>
            <select id="discount_type" name="discount_type" required >
                <option value="fixed"{{ $coupon->discount_type == "fixed"? 'selected':'' }}>Fixed (đơn giá giảm cố định)</option>
                <option value="percentage"{{ $coupon->discount_type == "percentage"? 'selected':'' }}>Percentage (giảm theo phần trăm)</option>
            </select>
        </div>

        <!-- Discount Value -->
        <div class="form-group">
            <label for="discount_value">Discount Value(%):</label>
            <input placeholder="Discount Value:" type="number" id="discount_value" name="discount_value" step="1" required min="1" 
            max="100" >
        </div>

        <!-- Max Discount Value (optional) -->
        <div class="form-group">
            <label for="max_discount_value">Max Discount Value (optional):</label>
            <input placeholder="Max Discount Value (optional):" type="number" id="max_discount_value" name="max_discount_value"  step="1" required min="1" 
            max="100">
        </div>

        <!-- Expiration Date -->
        <div class="form-group">
            <label for="expiration_date">Expiration Date:</label>
            <input value="{{ $coupon->expiration_date }}" type="date" id="expiration_date" name="expiration_date" required>
        </div>

        <!-- Usage Limit -->
        <div class="form-group">
            <label for="usage_limit">Usage Limit:</label>
            <input value="{{ $coupon-> usage_limit}}" placeholder="Usage Limit:" type="number" id="usage_limit" name="usage_limit" required min="0" >
        </div>

        <!-- Usage Count (default is 0) -->
        <div class="form-group">
            <label for="usage_count">Usage Count (default: 0):</label>
            <input value="{{ $coupon-> usage_count}}" type="number" id="usage_count" name="usage_count" value="0" disabled>
        </div>

        <!-- Status -->
        <div class="form-group">
            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="active"{{ $coupon->status == "active"? 'selected':'' }}>Active</option>
                <option value="expired"{{ $coupon->status == "expired"? 'selected':'' }}>Expired</option>
                <option value="disabled"{{ $coupon->status == "disabled"? 'selected':'' }}>Disabled</option>
            </select>
        </div>

        <!-- Submit Button -->
        <button type="submit">update Coupon</button>
    </form>
</div>

</body>
</html>
