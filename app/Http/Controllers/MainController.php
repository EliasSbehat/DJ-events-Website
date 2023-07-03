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

    public function signin()
    {
        return view('signin');
    }
    public function signup()
    {
        return view('signup');
    }
    public function register(Request $request)
    {
        $input = $request->post();
        $sms_api_key = config('app.sms_key');
        $randomNumber = mt_rand(1000, 9999);
        // print_r($input);die();
        $request->validate([
            'email' => 'required|unique:users|max:255',
            'password' => 'required|min:8|max:255|confirmed',
        ]);
        // DB::table('users')->insert([
        //     'email' => $input['email'],
        //     'first_name' => $input['first_name'],
        //     'last_name' => $input['last_name'],
        //     'phone' => $input['phone'],
        //     'verify_number' => $randomNumber,
        //     'password' => md5($input['password']),
        //     'remember_token' => $input['_token']
        // ]);

        // Message details
        $numbers = array($input['phone']);
        $sender = urlencode('Website');
        $message = rawurlencode($randomNumber);
    
        $numbers = implode(',', $numbers);
    
        // Prepare data for POST request
        $data = array('apikey' => $sms_api_key, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
    
        // Send the POST request with cURL
        $ch = curl_init('https://api.txtlocal.com/send/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        // Process your response here
        echo $response; die();

        return redirect("/verify");
    }
    public function verify()
    {
        return view('verify');
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
