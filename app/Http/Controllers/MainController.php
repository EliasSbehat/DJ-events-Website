<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Http;



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
            'phone' => 'required|unique:users',
        ]);
        if ($input['phone'][0]=="+") {
            $input['phone'] = substr($input['phone'], 1);
        }
        DB::table('users')->insert([
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'phone' => $input['phone'],
            'verify_number' => $randomNumber,
            'remember_token' => $input['_token']
        ]);
        session(['phone' => $input['phone']]);
        // Message details
        $numbers = array($input['phone']);
        $sender = urlencode('karaokedj');
        $message = rawurlencode($randomNumber);
    
        $numbers = implode(',', $numbers);
    
        $data = array('apikey' => $sms_api_key, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
    
        // Send the POST request with cURL
        $ch = curl_init('https://api.txtlocal.com/send/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
		
        // Send the POST request with cURL
        
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
        $phone = session('phone');
        $data = DB::table('users')
            ->select('*')
            ->where('phone', $phone)
            ->get();
        
        if (count($data)) {
            if ($data[0]->verify_number == $code) {
                DB::table('users')->where('phone', $phone)->update([
                    'verified' => 1
                ]);
                session()->forget('phone');
                echo "success";
            } else {
                echo "invalid";
            }
        }
    }
    public function logout()
    {
        session()->forget('phone');
        session()->forget('name');
        session()->forget('x-t');
        session()->forget('role');
        session()->forget('user-id');
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
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $phone = $request->input('phone');
        if ($phone[0]=="+") {
            $phone = substr($phone, 1);
        }
        $user = DB::table('users')->where('phone', $phone)->first();
        $token = Hash::make($phone . "_@123Col_" . $first_name . time());
        $randomNumber = mt_rand(1000, 9999);
        $sms_api_key = config('app.sms_key');
        if ($user) {
            if ($user->verified) {
                DB::table('users')->where('phone', $phone)->update([
                    'remember_token' => $token,
                    'verify_number' => $randomNumber
                ]);
                session(['x-t' => $token]);
                session(['user-id' => $user->id]);
                session(['phone' => $phone]);
                session(['name' => $user->first_name.' '.$user->last_name]);
                session(['role' => $user->role]);


                $numbers = array($phone);
                $sender = urlencode('karaokedj');
                $message = rawurlencode($randomNumber);
            
                $numbers = implode(',', $numbers);
            
                $data = array('apikey' => $sms_api_key, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
            
                // Send the POST request with cURL
                $ch = curl_init('https://api.txtlocal.com/send/');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);

                echo $token;
            } else {
                echo "not verified";
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
    public function songlist()
    {
        return view('songlist');
    }
    public function requested()
    {
        return view('requested');
    }
    public function songAdd(Request $request)
    {
        $requestData = $request->all();
        $data = json_decode($requestData['data']);
        for ($i = 0; $i < count($data); $i++) {
            DB::table('songs')->insert([
                'title' => $data[$i]->title,
                'artist' => $data[$i]->artist
            ]);
        }
        exit("success");
    }
    public function songRequest(Request $request)
    {

        $setData = DB::table('request_setting')
            ->select('*')
            ->get();
        if (count($setData)) {
            if ($setData[0]->turn_on==0) {
                exit("turnoff");
            }
        }

        $requestData = $request->all();
        $currentDate = date('Y-m-d H:i:s'); // Format the date as per the datetime type in MySQL

        //mail
        $email = new TestMail(
            $sender = 'requests@karaokedj.co.uk',
            $subject = 'Request E-mail',
            $body = session('name') ." - ". $requestData['singer'] .  "\n has requested:" . $requestData['artist'] . " - " . $requestData['title'] ."\n" . $requestData['dj'] ."\n" .$currentDate
        );

        // When: we receive that e-mail
        Mail::to('nick@djnickburrett.com')->send($email);

        DB::table('request')->insert([
            'song_id' => $requestData['id'],
            'singer' => $requestData['singer'],
            'dj' => $requestData['dj'],
            'requester_id' => session('user-id'),
            'date' => $currentDate
        ]);
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
    public function songGet()
    {
        $data = DB::table('songs')
            ->select('*')
            ->get();
        
        print_r(json_encode($data)); 
        exit();
    }
    public function getRequestSetting()
    {
        $data = DB::table('request_setting')
            ->select('*')
            ->get();
        
        print_r(json_encode($data)); 
        exit();
    }
    public function getRequestSettingSet(Request $request)
    {
        $turn = $request->input('turn');
        $set = ($turn=='true')?1:0;
        DB::table('request_setting')->update([
            'turn_on' => $set
        ]);
        exit();
    }
    
    
    public function songGetByUser(Request $request)
    {
        $userId = session('user-id');
        $today = $request->input('today');
        $currentDate = date('Y-m-d');
        if ($today == 1) {
            $data = DB::table('request')
                ->join('songs', 'request.song_id', '=', 'songs.id')
                ->where('request.requester_id', '=', $userId)
                ->where('request.date', 'like', '%'.$currentDate.'%')
                ->get();
        } else {
            $data = DB::table('request')
                ->join('songs', 'request.song_id', '=', 'songs.id')
                ->where('request.requester_id', '=', $userId)
                ->where('request.date', 'not like', '%'.$currentDate.'%')
                ->get();
        }
        
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
