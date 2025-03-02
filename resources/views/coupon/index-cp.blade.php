<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coupons Table</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-buttons button {
            padding: 5px 10px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }

        .edit-btn {
            background-color: #ffcc00;
        }

        .delete-btn {
            background-color: #ff4c4c;
            color: white;
        }

        .add-btn {
            background-color: #4caf50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>

    <h2>Coupons Table</h2>

    <!-- Button to Add Coupon -->
    <a class="add-btn" href="{{ route('coupon.create') }}">Add Coupon</a>
    @if (Session::has('thongbao'))
    <div>
        {{ Session::get('thongbao') }}
    </div>
    @endif
    <br>
    <br>
    <br>
    <table>
        <thead>
            <tr>
                <th>Coupon ID</th>
                <th>Code</th>
                <th>Discount Type</th>
                <th>Discount Value</th>
                <th>Max Discount Value</th>
                <th>Expiration Date</th>
                <th>Usage Limit</th>
                <th>Usage Count</th>
                <th>Status</th>
                <th>Actions</th> <!-- Thêm cột Actions -->
            </tr>
        </thead>
        <tbody>
            @foreach($coupon as $cp)
            <tr>
                <td>{{ ++$i }}</td> <!-- Đảm bảo biến $i được truyền từ controller -->
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
                        <!-- Nút Sửa -->


                        <!-- Nút Xóa -->
                        <form action="{{ route('coupon.destroy', $cp->id) }}" method="POST">
                            <a href="{{ route('coupon.edit', $cp->id) }}" class="edit-btn">Edit</a>
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

</body>

</html>