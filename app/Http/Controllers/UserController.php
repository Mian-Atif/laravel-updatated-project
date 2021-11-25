<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\signuprequest;
use App\Http\Requests\signinrequest;
use App\Http\Resources\UserResourse;
use App\Service\JwtAuth;


class UserController extends Controller
{
    function signUp(signuprequest $request){
        //dd($request);
        try{
        $auth=$request->validated();

        $date=date('Y-m-d h:i:s');
        $result=DB::table('users')->insert([
            'name'           =>  $auth['name'],
            'email'          => $auth['email'],
            'password'       => hash::make($auth['password']),
            'phone_no'       => $auth['phone_no'],
            'profile'        => $request->profile,
            'favorite_animal'=> $auth['favorite_animal'],
            'created_at'     =>$date,
        ]);

        if($result){
            // data creation for email
            $details['link']=url('api/emailConfirmation/'.$auth['email']);
            $details['user_name']= $auth['name'];
            $details['email']=$auth['email'];

            //send verification mail
            Mail::to($auth['email'])->send(new \App\Mail\EmailVerification($details));

            return response()->success(["result"=>"user is successfully signup"],200);
        }
        else{
            return response()->error(["result"=>"opration faild"],401);
        }
     }
       catch(Exception $ex){
        return response()->error($ex->getMessage(),400);

    }
}
    function emailConfirmation($email)
    {
        try{
            $user = User::where('email',$email)->first();
            if (!empty($user['id'])) {
                if (empty($user['email_verified_at'])) {
                    $user->email_verified_at=date('Y-m-d h:i:s');
                    try{
                        $user->update();
                        return response()->success(['data'=>"Your Email Verified Sucessfully!!!"],200);
                    }catch(Exception $ex){
                        return response()->error(['Error'=>"Something Went Wrong".$ex->getMessage()],400);
                    }
                }else{
                    return response()->json(['data'=>"Already Verified"],202);
                }
            }else{
                return response()->json(['data'=>"Linked Expired"],404);
            }
        }
        catch(Exception $ex){
            return response()->error($ex->getMessage(),400);
                 }
    }


    function signIn(signinrequest $request){
        try{
            $auth=$request->validated();
        $user = DB::table('users')->where('email',$auth['email'])->first();
        //dd($user->remember_token);
        if (Hash::check($auth['password'],$user->password))
        {
            if(($user->remember_token==null)){
                $jwt=(new JwtAuth)->createToken($user);
                //save token in
                User::where('email', $user->email)
                ->update(['remember_token' => $jwt]);
            }
            else{
                return response()->error(['data'=>"user alreday sign"],401);
            }
         return response()->success(['token'=>$jwt],200);
        }
        else{
            return response()->error(['message'=>"email and password not valied"],400);
        }
    }
    catch(Exception $ex){
        return response()->error($ex->getMessage(),400);
    }

    }

    function logOut(Request $request){
        $token=$request->bearerToken();

        $token=(new JwtAuth)->decodeToken($token);
        $email=$token->data->email;
        $var=User::where('email', $email)->update(['remember_token' => ""]);
        $remember_token=$token->data->remember_token;
        if($remember_token==null){

            return response()->success(['message'=>"you are successfully logout"],200);
        }
        else{
            return response()->error(['message'=>"there is some problem in logout"],500);
        }
       }
       function resource(Request $request)
       {
        $token=$request->bearerToken();
        $token=(new JwtAuth)->decodeToken($token);
        $user = User::find($token->data->id);
        return new UserResourse($user);
       }

}
