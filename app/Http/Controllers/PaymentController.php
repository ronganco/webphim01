<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user() == null){
            return redirect()->route('home');
        }
        $coin = DB::table('users')->where('id', '=', Auth::user()->id)->first()->coin;

        return view('pages.payment',[
            'coin' => $coin,
        ]);

        
    }

    public function vip()
    {
        if(Auth::user() == null){
            return redirect()->route('home');
        }
        $vip = DB::table('users')->where('id', '=', Auth::user()->id)->first();
        $listVip = DB::table("vip_service")->get();
        return view('pages.vip',[
            'vip' => $vip,
            'listVip' => $listVip
        ]);


    }

    public function editVip(Request $request)
    {
        if(Auth::user() == null){
            return redirect()->route('home');
        }
        $id = $request->id;
        $vip = DB::table('vip_service')->where('id', '=', $id)->first();
        return view('admincp.vip.edit',[
            'vip' => $vip,
        ]);

    }

    public function storeVip(Request $request)
    {
       $name = $request->name;
       $price = $request->price;
       $day = $request->day;

       DB::table('vip_service')->where('id',$request->id)->update([
           'name'=>$name,
           'price' => $price,
           'day'=>$day,
       ]);

       return redirect()->route('admin.vip');
    }

    public function deleteVip(Request $request)
    {
     
       DB::table('vip_service')->where('id',$request->id)->delete();

       return redirect()->route('admin.vip');
    }



    public function adminVipCreate(Request $request){
        $name = $request->name;
        $price = $request->price;
        $day = $request->day;

        DB::table('vip_service')->insert([
            'name'=>$name,
            'price' => $price,
            'day'=>$day,
            'created_at'=>now(),
            'updated_at'=>now()
        ]);


        return redirect()->back();
    }


    public function adminVip(){
        if(Auth::user() == null){
            return redirect()->route('home');
        }
        $listVip = DB::table("vip_service")->get();
        return view('admincp.vip.form',[
            'list' => $listVip
        ]);
    }
    public function buyVip(Request $request, $id){
        $vipId = $request->input('vipId');
        $vip = DB::table('vip_service')->where('id',$vipId)->first();
        $coin = DB::table('users')->where('id', '=', Auth::user()->id)->first()->coin;
        if($coin < $vip->price){
            return response()->json(-1,['message' => 'Không đủ tiền để mua gói vip này!']);
        }
        $time = $vip->day;
        $newCoin = $coin - $vip->price;
        $vipEndDate = Carbon::now()->addDays($time)->toDateString();

        DB::table('users')->where('id', Auth::user()->id)->update([
            'coin' => $newCoin,
            'vip' => 1,
            'vip_end' => $vipEndDate,
        ]);

        return response()->json(['status' => 1, 'message' => 'Mua gói VIP thành công!']);

    }


 
    public function process(Request $request)
    {
        $order = 'order_'.random_int(10000, 99999);


        DB::table("payment")->insert([
            'user_id' => Auth::user()->id,
            'order_name' => $order,
            'coin'=>$request->amount
        ]);
        $username =  Auth::user()->name;
        $vnp_Url = "http://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('return.payment')."?code=".$order;
        $vnp_TmnCode = "K2XRZGNM"; //Mã website tại VNPAY
        $vnp_HashSecret = "JXAQZQHSGTPLTFOEGZSGMKXBWUAUCNQL"; //Chuỗi bí mật
        $vnp_TxnRef = $order; //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = "Nạp tiền vào ví";
        $vnp_OrderType = "Nạp tiền vào ví";
        $vnp_Amount = $request->amount * 100;
        $vnp_Locale = "VN";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );
        // dd($inputData);

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }


        $vnp_Url = $vnp_Url . "?" . $query;
        
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return redirect($vnp_Url);
    }

  
    public function response(Request $request)
    {
        $code = $request->code;
        $order =DB::table("payment")->where("order_name",$code)->first();
        if($order != null) {
            DB::table("users")->where('id',$order->user_id)->increment(
                'coin',$order->coin,
            );
        }
        $request->session()->flash('success', 'Nạp tiền thành công!');

        return redirect()->route('get.payment');
    }

  

   

  
}
