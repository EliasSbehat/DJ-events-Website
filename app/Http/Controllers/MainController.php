<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Http;
use DataTables;

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
    public function verifyLink($token)
    {
        session(['x-t' => $token]);
        $data = DB::table('users')
            ->select('*')
            ->where('remember_token', $token)
            ->get();
        if (count($data)) {
            echo "success";
            session(['user-id' => $data[0]->id]);
            session(['phone' => $data[0]->phone]);
            session(['name' => $data[0]->first_name.' '.$data[0]->last_name]);
            session(['role' => $data[0]->role]);
            return redirect('/songlist');
        } else {
            echo "invalid";
            return redirect('/signin');
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
    public function check()
    {
        $s_token = session('x-t');
        if ($s_token) {
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
        if (!$user) {
            DB::table('users')->insert([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'phone' => $phone
            ]);
        }
        $user = DB::table('users')->where('phone', $phone)->first();

        $token = md5($phone . "_@123Col_" . $first_name . time());
        $host = request()->getSchemeAndHttpHost();
        $link = 'Click here to request karaoke. ' . $host . '/auth'. '/'. $token;
        $sms_api_key = config('app.sms_key');
        if ($user) {
            // if ($user->verified) {
            DB::table('users')->where('phone', $phone)->update([
                'remember_token' => $token
            ]);
            session(['x-t' => $token]);
            session(['user-id' => $user->id]);
            session(['phone' => $phone]);
            session(['name' => $user->first_name.' '.$user->last_name]);
            session(['role' => $user->role]);


            $numbers = array($phone);
            $sender = urlencode('karaokedj');
            $message = rawurlencode($link);
        
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
            // } else {
            //     echo "not verified";
            // }
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
    public function received()
    {
        return view('received');
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
            $phone = session('phone'),
            $body = session('name') .": ". $requestData['singer'],
            $song = $requestData['artist'] . "  " . $requestData['title'],
            $msg = $requestData['dj'],
            $date = $currentDate
        );

        // When: we receive that e-mail
        // Mail::to('speedjudy928@gmail.com')->send($email);
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
    public function songGetS()
    {
        $data = DB::table('songs')
            ->select('*')
            ->get();
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $actionBtn = '<button type="button" class="btn btn-primary request-btn" id="'.$row->id.'">Request</button>';
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function songGetMS()
    {
        $data = DB::table('songs')
            ->select('*')
            ->get();
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $actionBtn = '<button type="button" class="btn btn-secondary delete-btn" id="'.$row->id.'"><i class="fas fa-trash-can"></i></button><button type="button" class="btn btn-primary edit-btn" id="'.$row->id.'"><i class="fas fa-pen-to-square"></i></button>';
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
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
    public function songGetByUserS(Request $request) 
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
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $actionBtn = '<button type="button" class="btn btn-primary request-btn" id="'.$row->id.'">Request</button>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        } else {
            $data = DB::table('request')
                ->join('songs', 'request.song_id', '=', 'songs.id')
                ->where('request.requester_id', '=', $userId)
                ->where('request.date', 'not like', '%'.$currentDate.'%')
                ->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $actionBtn = '<button type="button" class="btn btn-primary request-btn" id="'.$row->id.'">Request</button>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
    public function getRequestedSongs() 
    {
        $data = DB::table('request')
            ->join('songs', 'request.song_id', '=', 'songs.id')
            ->join('users', 'request.requester_id', '=', 'users.id')
            ->select('request.*', 'songs.title', 'songs.artist', 'users.first_name', 'users.last_name')
            ->orderBy('request.date', 'asc')
            ->get();
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('first_column', function($row){
                $column1 = '<span id="singer_column" data-clipboard-text="'. $row->first_name . ' ' . $row->last_name . ': ' . $row->singer .'" data-bs-toggle="tooltip" data-bs-placement="top" title="Copy to clipboard" style="cursor:pointer;">' . $row->first_name . ' ' . $row->last_name . ': ' . $row->singer . '</span>';
                return $column1;
            })
            ->addColumn('second_column', function($row){
                $column1 = '<span id="artist_column" data-clipboard-text="'. $row->artist . ' ' . $row->title .'" data-bs-toggle="tooltip" data-bs-placement="top" title="Copy to clipboard" style="cursor:pointer;">' . $row->artist . ' ' . $row->title . '</span>';
                return $column1;
            })
            ->addColumn('action', function($row){
                $checked = ($row->checked)?"checked":"";
                $actionBtn = '<input type="checkbox" class="read_check" '.$checked.' id="'.$row->id.'" />';
                return $actionBtn;
            })
            ->rawColumns(['action', 'first_column', 'second_column'])
            ->make(true);
    }
    public function songDelete(Request $request)
    {
        $requestData = $request->all();
        $id = $requestData['id'];
        DB::table('songs')->where('id', '=', $id)->delete();
        DB::table('request')->where('song_id', '=', $id)->delete();
        exit();
    }
    public function setRead(Request $request)
    {
        $requestData = $request->all();
        $id = $requestData['id'];
        $checked = $requestData['checked'];
        $flag = ($checked=="true")?1:0;
        DB::table('request')->where('id', $id)->update([
            'checked' => $flag
        ]);
        exit();
    }
}
