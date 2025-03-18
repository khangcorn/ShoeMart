<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Coupon</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        h2 {
            text-align: center;
            margin-top: 20px;
            font-size: 28px;
            color: #333;
        }
        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 30px auto;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-weight: 500;
            color: #555;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 16px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            text-align: center;
            color: #555;
            text-decoration: none;
            font-size: 16px;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h2>Edit Coupon</h2>
<a href="{{ route('coupon.index') }}">Back to Coupons Table</a>

<div class="form-container">
    <form action="{{ route('coupon.update', $coupon->coupon_id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="code">Coupon Code:</label>
            <input type="text" id="code" name="code" value="{{ $coupon->code }}" placeholder="Enter coupon code" required>
        </div>
        <div class="form-group">
            <label for="discount_type">Discount Type:</label>
            <select id="discount_type" name="discount_type" required>
                <option value="fixed" {{ $coupon->discount_type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                <option value="percentage" {{ $coupon->discount_type == 'percentage' ? 'selected' : '' }}>Percentage</option>
            </select>
        </div>
        <div class="form-group">
            <label for="discount_value">Discount Value (%):</label>
            <input type="number" id="discount_value" name="discount_value" value="{{ $coupon->discount_value }}" required min="1" max="100">
        </div>
        <div class="form-group">
            <label for="max_discount_value">Max Discount Value:</label>
            <input type="number" id="max_discount_value" name="max_discount_value" value="{{ $coupon->max_discount_value }}" required>
        </div>
        <div class="form-group">
            <label for="expiration_date">Expiration Date:</label>
            <input type="date" id="expiration_date" name="expiration_date" value="{{ $coupon->expiration_date }}" required>
        </div>
        <div class="form-group">
            <label for="usage_limit">Usage Limit:</label>
            <input type="number" id="usage_limit" name="usage_limit" value="{{ $coupon->usage_limit }}" required min="0">
        </div>
        <div class="form-group">
            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="active" {{ $coupon->status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="expired" {{ $coupon->status == 'expired' ? 'selected' : '' }}>Expired</option>
                <option value="disabled" {{ $coupon->status == 'disabled' ? 'selected' : '' }}>Disabled</option>
            </select>
        </div>
        <button type="submit">Update Coupon</button>
    </form>
</div>

</body>
</html>
