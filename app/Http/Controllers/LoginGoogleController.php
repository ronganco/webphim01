<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginGoogleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect(); // mạng xã hội là google
    }

    public function handleGoogleCallback()
    {
        try {
                $user = Socialite::driver('google')->user(); // mạng xã hội là google
                $finduser = User::where('email', $user->email)->first(); // tìm kiếm xem tài khoản đã có trong data chưa
                if($finduser){ // nếu có
                    Auth::login($finduser); // login ngay lập tức
                    return redirect()->route('homepage');
                }else{ // nếu ko có 
                    $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id'=> $user->id,
                    'password' => encrypt('123456789'),
                    'coin' => 0,
                    'vip' => 0,
                    ]);
                    // login vào với acc mới
                    Auth::login($newUser);
                    return redirect()->route('homepage');
                }
                } catch (Exception $e) {
                dd($e->getMessage());
            }
    }
    public function logout_home(){
        Auth::logout();
        return redirect()->back();
    }
}
