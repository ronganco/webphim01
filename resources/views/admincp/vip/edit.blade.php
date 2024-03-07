@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Sửa Gói Vip</div>

                <div class="card-body">
                    {!! Form::model($vip, ['route' => ['admin.updateVip',['id' =>$vip->id]], 'method' => 'post']) !!}
                        <div class="form-group">
                            {!! Form::label('name', 'Tên Loại Vip') !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Nhập tên loại Vip']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('price', 'Giá tiền') !!}
                            {!! Form::text('price', null, ['class' => 'form-control', 'placeholder' => 'Nhập giá tiền']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('day', 'Thời gian (ngày)') !!}
                            {!! Form::text('day', null, ['class' => 'form-control', 'placeholder' => 'Nhập thời gian']) !!}
                        </div>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
