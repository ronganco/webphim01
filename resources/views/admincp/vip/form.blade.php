@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Quản Lý Gói Vip</div>

                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addVipModal">
  Thêm Gói Vip
</button>
            </div>
            <table class="table" id="tablephim">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Tên Loai Vip</th>
                  <th scope="col">Giá tiền</th>
                  <th scope="col">Thời gian (ngày)</th>
                  <th scope="col">Quản lý</th>
                </tr>
              </thead>
              <tbody>
                @foreach($list as $key => $cate)
                <tr>
                  <th scope="row">{{$key+1}}</th>
                  <td>{{$cate->name}}</td>
                  <td>{{$cate->price}}</td>
                  <td>{{$cate->day}}</td>
                
                  <td>
                 
                      <a href="{{route('admin.deleteVip',['id'=>$cate->id])}}" class="btn btn-danger">Xóa</a>

                      <a href="{{route('admin.editVip',['id'=>$cate->id])}}" class="btn btn-warning">Sửa</a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="addVipModal" tabindex="-1" role="dialog" aria-labelledby="addVipModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addVipModalLabel">Thêm Gói Vip Mới</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <div class="modal-body">
            <!-- Thêm form thêm mới ở đây -->
            {!! Form::open(['id' => 'addVipForm', 'url' => route('admin.vip.create'), 'method' => 'post']) !!}
            <div class="form-group">
                {!! Form::label('name', 'Tên Loại Vip') !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Nhập tên loại Vip']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('price', 'Giá tiền') !!}
                {!! Form::number('price', null, ['class' => 'form-control', 'placeholder' => 'Nhập giá tiền']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('day', 'Thời gian (ngày)') !!}
                {!! Form::number('day', null, ['class' => 'form-control', 'placeholder' => 'Nhập thời gian']) !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary" id="addVipBtn">Lưu</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
  </div>
</div>