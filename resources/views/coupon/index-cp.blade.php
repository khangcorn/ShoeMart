<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coupons Table</title>
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
        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .action-buttons a, .action-buttons button {
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
        }
        .edit-btn {
            background-color: #ffcc00;
            color: white;
        }
        .delete-btn {
            background-color: #ff4c4c;
            color: white;
            border: none;
        }
        .add-btn {
            background-color: #4caf50;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 20px;
        }
        .add-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<h2>Coupons Table</h2>
<div class="container">
    <a href="{{ route('coupon.create') }}" class="add-btn">Add Coupon</a>
    @if (Session::has('thongbao'))
        <div>{{ Session::get('thongbao') }}</div>
    @endif
    <table>
        <thead>
            <tr>
                <th>Coupon ID</th>
                <th>Code</th>
                <th>Discount Type</th>
                <th>Discount Value(%)</th>
                <th>Max Discount Value(VNĐ)</th>
                <th>Expiration Date</th>
                <th>Usage Limit</th>
                <th>Usage Count</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($coupon as $cp)
                <tr>
                    <td>{{ $cp->coupon_id }}</td>
                    <td>{{ $cp->code }}</td>
                    <td>{{ $cp->discount_type }}</td>
                    <td>{{ $cp->discount_value }}</td>
                    <td>{{ $cp->max_discount_value }}</td>
                    <td>{{ $cp->expiration_date }}</td>
                    <td>{{ $cp->usage_limit }}</td>
                    <td>{{ $cp->usage_count }}</td>
                    <td>{{ $cp->status }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('coupon.edit', $cp->coupon_id) }}" class="edit-btn">Edit</a>
                            <form action="{{ route('coupon.destroy', $cp->coupon_id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this coupon?')">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

</body>
</html>
