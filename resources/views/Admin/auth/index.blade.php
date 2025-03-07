<table class="table-auto">
  
    <thead>
      <tr>
        <th>ID</th>
        <th> Tên tài khoản</th>
        <th>Email</th>
        <th> Số điện thoại</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($users as $user )
   
      <tr>
        <td> {{$user->user_id}}</td>
        <td>{{$user->username}}</td>
        <td>{{$user->email}}</td>
        <td>{{$user->phone}}</td>

      </tr>
           
      @endforeach
      
    </tbody>
  </table>