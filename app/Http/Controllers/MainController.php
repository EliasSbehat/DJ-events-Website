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
        DB::table('users')->insert([
            'email' => $input['email'],
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'phone' => $input['phone'],
            'verify_number' => $randomNumber,
            'password' => md5($input['password']),
            'remember_token' => $input['_token']
        ]);
        session(['email' => $input['email']]);
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
        // echo $response; 

        return redirect("/verify");
    }
    public function verify()
    {
        return view('verify');
    }
    public function verifyCode(Request $request)
    {
        $input = $request->all();
        $code = $input['code'];
        $email = session('email');
        $data = DB::table('users')
            ->select('*')
            ->where('email', $email)
            ->get();
        if (count($data)) {
            if ($data[0]->verify_number == $code) {
                DB::table('users')->where('email', $email)->update([
                    'verified' => 1
                ]);
                session()->forget('email');
                echo "success";
            } else {
                echo "invalid";
            }
        }
    }
    public function logout()
    {
        session()->forget('email');
        session()->forget('x-t');
        session()->forget('role');
        return redirect('/signin');
    }
    public function check(Request $request)
    {
        $token = $request->input('token');
        $s_token = session('x-t');
        if ($token == $s_token) {
            echo "success";
        } else {
            echo "failed";
        }
    }
    public function checkuser(Request $request)
    {
        $input = $request->post();
        $email = $request->input('n');
        $pwd = $request->input('p');
        $user = DB::table('users')->where('email', $email)->first();
        $token = Hash::make($email . "_@123Col_" . $pwd . time());
        if ($user) {
            if ($user->password == md5($pwd)) {
                if ($user->verified) {
                    DB::table('users')->where('email', $email)->update([
                        'remember_token' => $token
                    ]);
                    session(['x-t' => $token]);
                    session(['email' => $email]);
                    session(['role' => $user->role]);
                    echo $token;
                } else {
                    echo "not verified";
                }
            } else {
                echo "wrong pwd";
            }
        } else {
            echo "wrong user";
        }
    }

    public function songs()
    {
        return view('songs');
    }
    public function songmng()
    {
        return view('songmng');
    }
    public function songAdd(Request $request)
    {
        $requestData = $request->all();
        // $title = $request->input('title');
        // $artist = $request->input('artist');
        $data = json_decode($requestData['data']);
        for ($i = 0; $i < count($data); $i++) {
            DB::table('users')->where('email', $email)->update([
                'verified' => 1
            ]);
        }
        exit("success");
    }
    public function songAddSingle(Request $request)
    {
        $requestData = $request->all();
        $title = $requestData['title'];
        $artist = $requestData['artist'];
        $id = $requestData['id'];
        if ($id) {
            DB::table('songs')->where('id', $id)->update([
                'title' => $title,
                'artist' => $artist
            ]);
        } else {
            DB::table('songs')->insert([
                'title' => $title,
                'artist' => $artist
            ]);
        }
        exit("success");
    }
    public function songGet(Request $request)
    {
        $data = DB::table('songs')
            ->select('*')
            ->get();
        
        print_r(json_encode($data)); 
        exit();
    }
    public function songDelete(Request $request)
    {
        $requestData = $request->all();
        $id = $requestData['id'];
        DB::table('songs')->where('id', '=', $id)->delete();
        exit();
    }
    
}
