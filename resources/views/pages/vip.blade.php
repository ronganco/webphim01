@extends('layout')

@section('content')
<div class="row container" id="wrapper">
    <div class="col-md-12 offset-md-3">
        <h2>VIP</h2>
        @if($vip->vip != 0 )
            <span>Cấp bậc: <strong style="color: yellow;">VIP</strong></span><br>
            <span>Ngày hết hạn: {{$vip->vip_end}}</span>
        @else
            <span>Cấp bậc: <strong>Thường</strong></span>
        @endif
    </div>
    <div class="col-md-12">
        <div class="vip-options">
            <h3>Chọn gói VIP</h3>
            <div class="card-deck row">
                @foreach($listVip as $vipi)
                <div class="card col-md-3">
                    <div class="card-body" style="background-color:white; padding:8px; border-radius:8px; border: 1px solid #ccc;color:black;">
                        <h5 class="card-title" style="font-weight:bold;color:green">{{$vipi->name}}</h5>
                        <p class="card-text">Giá: {{ number_format($vipi->price, 0, ',', '.') }} vnđ</p>
                        @if($vip->vip == 0 )
                        <a href="#" class="btn btn-primary" onclick="confirmPurchase({{ $vipi->id }}, {{ number_format($vipi->price, 0, ',', '.') }})">Mua Ngay</a>
                        @endif
                    </div>
                </div>
                @endforeach
        
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script>

   function confirmPurchase(vipId, price) {
        var confirmation = confirm("Bạn có chắc chắn muốn mua gói VIP này và trừ " + price + "k từ tài khoản không?");
        var token = $('meta[name="csrf-token"]').attr('content'); // Lấy CSRF token từ meta tag

        if (confirmation) {
            $.ajax({
                type: 'POST',
                url: '/buy-vip/' + vipId,
                data: { 
                    vipId: vipId ,
                    _token: token // Thêm CSRF token vào dữ liệu yêu cầu

                },
                success: function (response) {
                    if (response.status == 1) {
                        toastr.success('Bạn đã mua gói VIP thành công và bị trừ ' + price + 'k từ tài khoản.');

                        setTimeout(() => {
                            document.location.reload();
                        }, 2000);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function () {
                    toastr.error('Có lỗi xảy ra khi thực hiện mua gói VIP.');
                }
            });

        } else {
            toastr.warning("Bạn đã hủy bỏ mua gói VIP.");
        }
    }
</script>
</div>
@endsection
