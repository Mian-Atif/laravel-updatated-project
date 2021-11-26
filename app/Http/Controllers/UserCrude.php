<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Service\JwtAuth;
use Exception;
use App\Http\Requests\uploadfilerequest;
use App\Http\Requests\searchuserrequest;
use App\Http\Requests\forgetpassrequest;

class UserCrude extends Controller
{

    function updateUser(Request $request)
    {
        try {
            $token = $request->bearerToken();
            $token = (new UserController)->decodeToken($token);
            $email = $token->data->email;
            $data = User::where("email", $email)->first();
            $data->name    = $request->name;
            $data->phone_no = $request->phone_no;
            $data->profile = $request->profile;
            $result        = $data->save();
            if ($result) {
                return response()->success(['result' => "data is successfuly updated"]);
            } else {
                return response()->error(['result' => "data is not updated"]);
            }
        } catch (Exception $ex) {
            return response()->error($ex->getMessage(), 400);
        }
    }


    function searchUser(searchuserrequest $request)
    {
        try {
            $auth = $request->validated();
            $token = $request->bearerToken();
            if (isset($token)) {
                $email = $auth['email'];
                $result = User::where("email", $email)->get();
                return response()->success(['data' => $result], 200);
            } else {
                return response()->error(['token' => "token expire"], 401);
            }
        } catch (Exception $ex) {
            return response()->error($ex->getMessage(), 400);
        }
    }

    function upLoadFile(uploadfilerequest $request)
    {
        try {
            $auth = $request->validated();
            $token = $request->bearerToken();
            $token = (new JwtAuth)->decodeToken($token);
            $email = $token->data->email;
            if (isset($token)) {
                $user = DB::table('users')->where('email', $email)->first();
                $result = $auth['file']->store('api doc');
                User::where('email', $user->email)
                    ->update(['profile' => $result]);
                return response()->success(['result' => $result, 'data' => "file is successfulyy added"], 200);
            } else {
                return response()->error(['token' => "token expire"], 401);
            }
        } catch (Exception $ex) {
            return response()->error($ex->getMessage(), 400);
        }
    }
    function forGetPassword(forgetpassrequest $request)
    {
        try {
            $auth = $request->validated();
            $user = DB::table('users')->where('email', $auth['email'])->first();
            //dd($user);
            if ($auth['favorite_animal'] === $user->favorite_animal) {
                $new_password = hash::make($auth['password']);
                User::where('email', $user->email)
                    ->update(['password' => $new_password]);

                return response()->success(['data' => "new password save successfuly"], 200);
            } else {
                return response()->error(['data' => "credentials is invailed"], 401);
            }
        } catch (Exception $ex) {
            return response()->error($ex->getMessage(), 400);
        }
    }
}
