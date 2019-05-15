<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\UserModel;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    //注册
    public function register(request $request){
        $str = file_get_contents('php://input');
        $data = json_decode($str,true);
        //验证邮箱
        $user_email = DB::table('user')->where(['user_email'=>$data['user_email']])->first();
        if($user_email){
            $response=[
                'errno'=>'50010',
                'msg'=>'该邮箱已被注册'
            ];
            return $response;
        };
//        $password=password_hash($de_data['password'],PASSWORD_BCRYPT);
        $data = [
            'user_name'=>$data['user_name'],
            'user_email'=>$data['user_email'],
            'password'=>$data['password'],
            'add_time'=>time(),
        ];
        //入库
        $res = UserModel::insertGetId($data);
        if($res){
            $response=[
                'errno'=>'0',
                'msg'=>'注册成功,即将跳转至登录页面'
            ];
            return $response;
        }
    }

    //登录
    public function login(){
        $str = file_get_contents('php://input');
        $data = json_decode($str,true);
        $user_email = $data['user_email'];
        //验证邮箱
        $res = DB::table('user')->where(['user_email'=>$user_email])->first();
        if($res){
            //验证密码
            if($data['password']==$res->password){
                $token =  $this->getLoginToken($res->user_id);//生成token
                $key = "login_token:user_id:".$res->user_id;
                Redis::set($key,$token);                        //存token
                Redis::expire($key,604800);
//                dd(Redis::get($key));//463f5c2331872b0
                $response=[
                    'errno'=>0,
                    'msg'=>'登录成功',
                    'data'=>[
                        'token'=>$token,
                        'user_id'=>$res->user_id
                    ]
                ];
//                dd($response);
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }else{
                $response=[
                    'errno'=>50003,
                    'msg'=>'密码错误'
                ];
                return $response;
            }
        }else{
            $response=[
                'errno'=>50002,
                'msg'=>'该用户不存在,即将为您跳转至注册页面'
            ];
            return $response;
        }
    }

    //获取登录token
    public function getLoginToken($user_id){
        $rand_str = Str::random(10);
        $token = substr(md5($user_id.time().$rand_str),5,15);
        return $token;
    }

    //个人中心
    public function myself(){

        $data = file_get_contents("php://input");
//        $where = [
//            'user_id'=>$user_id
//        ];
//        $obj = DB::table('user')->where($where)->first();
        var_dump($data);die;
        if($obj){
            $response = [
                'errno'=>'0',
                'msg'=>"success",
                'user_name'=>$obj->user_name,
                'user_email'=>$obj->user_email
            ];
            return $response;
        }else{
            $response = [
                'errno'=>'50001',
                'msg'=>'请登陆'
            ];
            return $response;
        }
    }
}
