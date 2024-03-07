@extends('layout')

@section('content')
<div class="row container" id="wrapper">
    <div class="col-md-12 offset-md-3">
        <h2>Nạp Tiền</h2>
        <span>Số dư hiện tại: <strong style="color: yellow;">{{ number_format($coin, 0, ',', '.') }} vnđ</strong></span>
    </div>
    <div class="col-md-12 offset-md-3" style="display: flex;
    justify-content: center;">
        <form class="mt-2 col-md-4" action="{{route('post.payment')}}" method="post">
            @csrf
            <div class="form-group" style="text-align:center">
                <label for="so-tien">Số Tiền Muốn Nạp:</label>
                <input type="number" min="0" class="form-control" id="so-tien" name="amount" required>
                <button type="submit" class="btn btn-primary" style="margin-top:20px;width:120px;">Nạp Tiền</button>
            </div>
        </form>
    </div>
    @if(session('success'))
        <div class="alert alert-success mt-3" role="alert" style="text-align: center;">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger mt-3" role="alert" style="text-align: center;">
            {{ session('error') }}
        </div>
    @endif
</div>
@endsection
