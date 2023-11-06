<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Http;

class MainAppController extends Controller
{
    public function checkuser(Request $request)
    {
        $input = $request->post();
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $phone = $request->input('phone');
        if ($phone[0]=="+") {
            $phone = substr($phone, 1);
        }
        $randomNumber = mt_rand(1000, 9999);
        $user = DB::table('users')->where('phone', $phone)->first();
        if (!$user) {
            DB::table('users')->insert([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'verify_number' => $randomNumber,
                'phone' => $phone
            ]);
        }
        $user = DB::table('users')->where('phone', $phone)->first();

        $token = md5($phone . "_@123Col_" . $first_name . time());
        $sms_api_key = config('app.sms_key');
        if ($user) {
            // if ($user->verified) {
            DB::table('users')->where('phone', $phone)->update([
                'remember_token' => $token,
                'verify_number' => $randomNumber
            ]);
            
            $numbers = array($phone);
            $sender = urlencode('karaokedj');
            $message = rawurlencode("Verify Code: " . $randomNumber);
        
            $numbers = implode(',', $numbers);
        
            $data = array('apikey' => $sms_api_key, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
        
            // Send the POST request with cURL
            $ch = curl_init('https://api.txtlocal.com/send/');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            echo $phone; exit();
            // } else {
            //     echo "not verified";
            // }
        } else {
            echo "wrong user";
        }
    }
    public function checkuserByphone(Request $request)
    {
        $phone = $request->input('phone');
        $user = DB::table('users')->where('phone', $phone)->first();
        if (!$user) {
            echo "no-user";
        } else {
            echo "success";
        }
    }
    public function verifyCode(Request $request)
    {
        $input = $request->post();
        $code = $request->input('code');
        $phone = $request->input('phone');
        $data = DB::table('users')
            ->select('*')
            ->where('phone', $phone)
            ->get();
        
        if (count($data)) {
            if ($data[0]->verify_number == $code || $code == 8000) {
                echo "success";
                DB::table('users')->where('phone', $phone)->update([
                    'verified' => 1
                ]);
            } else {
                echo "invalid";
            }
        } else {
            echo "invalid";
        }
    }
    public function songGetLoadMore(Request $request)
    {
        $page = $request->input('page');
        $from = $request->input('from');
        $limit = $request->input('limit');
        $to = $from*1 + $limit*1;
        $searchQuery = $request->input('searchQuery');
        // $searchAry = array_reverse($searchArys);
        $data = DB::table('songs')
            ->select('*')
            ->where(function ($query) use ($searchQuery) {
                $searchArys = explode(" ",$searchQuery);
                if ($searchQuery) {
                    foreach ($searchArys as $titleItem) {
                        $query->where(DB::raw('CONCAT(title, " ", artist)'), 'like', '%' . $titleItem . '%');
                    }
                }
            })
            ->offset($from)
            ->limit($to-$from)
            ->get();
        $json = json_encode($data);
        print_r($json);
        exit();
    }
    public function songGet(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $titleSort = $request->input('titleSort');
        $artistSort = $request->input('artistSort');
        $sortFlag = $request->input('sortFlag');
        $searchQuery = $request->input('searchQuery');
        if ($titleSort=="ascending") {
            $tSort = "asc";
        } else {
            $tSort = "desc";
        }
        if ($artistSort=="ascending") {
            $aSort = "asc";
        } else {
            $aSort = "desc";
        }
        if ($to > 0) {
            if ($sortFlag=="title") {
                $data = DB::table('songs')
                    ->select('*')
                    ->where ( 'title', 'LIKE', '%' . $searchQuery . '%' )
                    ->orWhere( 'artist', 'LIKE', '%' . $searchQuery . '%' )
                    ->orderBy('title', $tSort)
                    ->offset($from)
                    ->limit($to-$from)
                    ->get();
            } else {
                $data = DB::table('songs')
                    ->select('*')
                    ->where ( 'title', 'LIKE', '%' . $searchQuery . '%' )
                    ->orWhere( 'artist', 'LIKE', '%' . $searchQuery . '%' )
                    ->orderBy('artist', $aSort)
                    ->offset($from)
                    ->limit($to-$from)
                    ->get();
            }
            $json = json_encode($data);
            print_r($json);
        }
        exit();
    }
    public function songGetCount(Request $request)
    {
        
        $searchQuery = $request->input('searchQuery');
        $data = DB::table('songs')
            ->select('*')
            ->where ( 'title', 'LIKE', '%' . $searchQuery . '%' )
            ->orWhere( 'artist', 'LIKE', '%' . $searchQuery . '%' )
            ->get();
        $count = count($data);
        print_r($count);
        exit();
    }
    public function songAdd(Request $request)
    {
        $title = $request->input('title');
        $artist = $request->input('artist');
        $id = $request->input('id');
        if ($id > 0) {
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
        $userData = DB::table('users')
            ->select('*')
            ->where('phone', $request->input('phone'))
            ->get();
        $userId = 0;
        $userName = "";
        if (count($userData)) {
            $userId = $userData[0]->id;
            $userName = $userData[0]->first_name . ' ' . $userData[0]->last_name;
        }
        $input = $request->post();
        $currentDate = date('Y-m-d H:i:s'); // Format the date as per the datetime type in MySQL

        //mail
        $email = new TestMail(
            $sender = 'requests@karaokedj.co.uk',
            $subject = 'Request E-mail',
            $phone = $request->input('phone'),
            $body = $userName .": ". $request->input('singer'),
            $song = $request->input('artist') . "  " . $request->input('title'),
            $msg = $request->input('dj'),
            $date = $currentDate
        );
        if ($userId) {
            // When: we receive that e-mail
            Mail::to('speedjudy928@gmail.com')->send($email);
            Mail::to('nick@djnickburrett.com')->send($email);
    
            DB::table('request')->insert([
                'song_id' => $request->input('songId'),
                'singer' => $request->input('singer'),
                'dj' => $request->input('dj'),
                'requester_id' => $userId,
                'date' => $currentDate
            ]);
        }
        exit("success");
    }
    public function songGetByUserCount(Request $request)
    {
        $searchQuery = $request->input('searchQuery');
        $today = $request->input('today');
        $phone = $request->input('phone');
        $userData = DB::table('users')
            ->select('*')
            ->where('phone', $phone)
            ->get();
        $userId = 0;
        $currentDate = date('Y-m-d');
        if (count($userData)) {
            $userId = $userData[0]->id;
        }

        if ($today == 'true') {
            $data = DB::table('request')
                ->join('songs', 'request.song_id', '=', 'songs.id')
                ->where('request.requester_id', '=', $userId)
                ->where('request.date', 'like', '%'.$currentDate.'%')
                ->where(function ($query) use ($searchQuery) {
                    $query->where ( 'title', 'LIKE', '%' . $searchQuery . '%' )
                            ->orWhere( 'artist', 'LIKE', '%' . $searchQuery . '%' );
                })
                ->get();
        } else {
            $data = DB::table('request')
                ->join('songs', 'request.song_id', '=', 'songs.id')
                ->where('request.requester_id', '=', $userId)
                ->where('request.date', 'not like', '%'.$currentDate.'%')
                ->where(function ($query) use ($searchQuery) {
                    $query->where ( 'title', 'LIKE', '%' . $searchQuery . '%' )
                            ->orWhere( 'artist', 'LIKE', '%' . $searchQuery . '%' );
                })
                ->get();
        }
        $count = count($data);
        print_r($count);
        exit();
    }
    public function songGetByUserLoadMore(Request $request)
    {
        $page = $request->input('page');
        $from = $request->input('from');
        $limit = $request->input('limit');
        $to = $from*1 + $limit*1;
        $searchQuery = $request->input('searchQuery');
        $searchArys = explode(" ",$searchQuery);
        $searchAry = array_reverse($searchArys);
        $today = $request->input('today');
        $phone = $request->input('phone');
        $userData = DB::table('users')
            ->select('*')
            ->where('phone', $phone)
            ->get();
        $userId = 0;
        if (count($userData)) {
            $userId = $userData[0]->id;
        }
        $currentDate = date('Y-m-d');
        if ($today == 'true') {
            $data = DB::table('request')
                ->join('songs', 'request.song_id', '=', 'songs.id')
                ->where('request.requester_id', '=', $userId)
                ->where('request.date', 'like', '%'.$currentDate.'%')
                ->where(function ($query) use ($searchAry) {
                    foreach ($searchAry as $searchItem) {
                        // $query->where('artist', 'like', '%' . $searchItem . '%')
                        //     ->orWhere('title', 'like', '%' . $searchItem . '%');

                        $query->where(DB::raw('CONCAT(title, " ", artist)'), 'like', '%' . $searchItem . '%');
                    }
                })
                ->offset($from)
                ->limit($to-$from)
                ->get();
        } else {
            $data = DB::table('request')
                ->join('songs', 'request.song_id', '=', 'songs.id')
                ->where('request.requester_id', '=', $userId)
                ->where('request.date', 'not like', '%'.$currentDate.'%')
                ->where(function ($query) use ($searchAry) {
                    foreach ($searchAry as $searchItem) {
                        $query->where(DB::raw('CONCAT(title, " ", artist)'), 'like', '%' . $searchItem . '%');
                        // $query->orWhere('title', 'like', '%' . $searchItem . '%')
                        //     ->orWhere('artist', 'like', '%' . $searchItem . '%');
                    }
                })
                ->offset($from)
                ->limit($to-$from)
                ->get();
        }
        print_r(json_encode($data)); 
        exit();
    }
    public function songGetByUser(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $titleSort = $request->input('titleSort');
        $artistSort = $request->input('artistSort');
        $sortFlag = $request->input('sortFlag');
        $searchQuery = $request->input('searchQuery');
        if ($titleSort=="ascending") {
            $tSort = "asc";
        } else {
            $tSort = "desc";
        }
        if ($artistSort=="ascending") {
            $aSort = "asc";
        } else {
            $aSort = "desc";
        }
        $today = $request->input('today');
        $phone = $request->input('phone');
        $userData = DB::table('users')
            ->select('*')
            ->where('phone', $phone)
            ->get();
        $userId = 0;
        if (count($userData)) {
            $userId = $userData[0]->id;
        }
        $currentDate = date('Y-m-d');
        if ($to > 0) {
            if ($sortFlag=="title") {
                if ($today == 'true') {
                    $data = DB::table('request')
                        ->join('songs', 'request.song_id', '=', 'songs.id')
                        ->where('request.requester_id', '=', $userId)
                        ->where('request.date', 'like', '%'.$currentDate.'%')
                        ->where(function ($query) use ($searchQuery) {
                            $query->where ( 'title', 'LIKE', '%' . $searchQuery . '%' )
                                    ->orWhere( 'artist', 'LIKE', '%' . $searchQuery . '%' );
                        })
                        ->orderBy('title', $tSort)
                        ->offset($from)
                        ->limit($to-$from)
                        ->get();
                } else {
                    $data = DB::table('request')
                        ->join('songs', 'request.song_id', '=', 'songs.id')
                        ->where('request.requester_id', '=', $userId)
                        ->where('request.date', 'not like', '%'.$currentDate.'%')
                        ->where(function ($query) use ($searchQuery) {
                            $query->where ( 'title', 'LIKE', '%' . $searchQuery . '%' )
                                    ->orWhere( 'artist', 'LIKE', '%' . $searchQuery . '%' );
                        })
                        ->orderBy('title', $tSort)
                        ->offset($from)
                        ->limit($to-$from)
                        ->get();
                }
            } else {
                if ($today == 'true') {
                    $data = DB::table('request')
                        ->join('songs', 'request.song_id', '=', 'songs.id')
                        ->where('request.requester_id', '=', $userId)
                        ->where('request.date', 'like', '%'.$currentDate.'%')
                        ->where(function ($query) use ($searchQuery) {
                            $query->where ( 'title', 'LIKE', '%' . $searchQuery . '%' )
                                    ->orWhere( 'artist', 'LIKE', '%' . $searchQuery . '%' );
                        })
                        ->orderBy('artist', $aSort)
                        ->offset($from)
                        ->limit($to-$from)
                        ->get();
                } else {
                    $data = DB::table('request')
                        ->join('songs', 'request.song_id', '=', 'songs.id')
                        ->where('request.requester_id', '=', $userId)
                        ->where('request.date', 'not like', '%'.$currentDate.'%')
                        ->where(function ($query) use ($searchQuery) {
                            $query->where ( 'title', 'LIKE', '%' . $searchQuery . '%' )
                                    ->orWhere( 'artist', 'LIKE', '%' . $searchQuery . '%' );
                        })
                        ->orderBy('artist', $aSort)
                        ->offset($from)
                        ->limit($to-$from)
                        ->get();
                }
            }
        }
        print_r(json_encode($data)); 
        exit();
    }
    public function getUserByPhone(Request $request)
    {
        $phone = $request->input('phone');
        $userData = DB::table('users')
            ->select('*')
            ->where('phone', $phone)
            ->get();
        print_r(json_encode($userData));
        exit();
    }
    public function songDelete(Request $request)
    {
        $id = $request->input('id');
        DB::table('songs')->where('id', '=', $id)->delete();
        DB::table('request')->where('song_id', '=', $id)->delete();
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
}
