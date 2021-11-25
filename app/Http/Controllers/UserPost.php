<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\friend;
use App\Models\comment;
use App\Http\Requests\addfriend;
use App\Http\Requests\addpost;
use App\Http\Requests\addcomment;
use Exception;
use App\Service\JwtAuth;

class UserPost extends Controller
{
    //
    function addPost(addpost $request){
        try{

        $auth=$request->validated();
        $token=$request->bearerToken();
        if(!empty($token)){
        $token=(new JwtAuth)->decodeToken($token);
        $id=$token->data->id;
       // $attachment=$auth['attachment']->store('api doc');
        if(!empty($id)){
        $user = User::find($id);
        $post = new Post;
       // $post->attachment=$attachment;
        $post->body = $auth['body'];
        $post->visibility = $auth['visibility'];
        $user = $user->posts()->save($post);

        return response()->success(['data'=>"your post added successfuly"],200);
        }
        else{
            return response()->error(['data'=>"there is some server error please try again"],500);
        }
        }
        else
        return response()->error(['data'=>"token expire"],401);
    }
    catch(Exception $ex){
        return response()->error($ex->getMessage(),400);
            }

     }

function addFriend(addfriend $request){
    try{
    $auth=$request->validated();
    $token=$request->bearerToken();
    if(!empty($token)){
    $token=(new JwtAuth)->decodeToken($token);
    $id=$token->data->id;

    $email=$auth['email'];
    $user = User::where('email',$email)->first();
    $user_id=$user['id'];

    if($user_id==$id){
    if(!empty($user_id)){
    $user = User::find($user_id);
    $friend = new friend;
    $friend->friend_id =$user_id;
    $user = $user->friends()->save($friend);
    return response()->success(['data'=>"friend added successfuly"],200);
    }
    else{
        return response()->error(['data'=>"user does not exit"],404);
    }
    }
    else{
        return response()->error(['data'=>"there is some server error"],500);
    }

    }
    else{
    return response()->error(['data'=>"token expire please login again"],202);
    }
        }
        catch(Exception $ex){
    return response()->error($ex->getMessage(),400);

}
 }


function addComment(addcomment $request){
    try{
    $auth=$request->validated();
    $token=$request->bearerToken();
    $post=$auth['post_id'];
    if(!empty($token)){
    $token=(new JwtAuth)->decodeToken($token);
    $token_id=$token->data->id;
    $comment_attachment=$auth['comment_attachment']->store('api doc');
    if(!empty($token_id)){
    $user = User::find($token_id)->first();
    $post =Post::find($post)->first();
    $comment = new comment();
    $comment->comment_body =$auth['comment_body'];
    $comment->comment_attachment =$comment_attachment;
        $comment->users()->associate($user);
        $comment->posts()->associate($post);
        $comment=$comment->save();
    return response()->success(['data'=>"comment added successfuly"],200);
    }
    else{
        return response()->error(['data'=>"user does not exit"],404);
    }
    }
    else{
    return response()->error(['data'=>"token expire please login again"],202);
    }

    }
    catch(Exception $ex){
        return response()->error($ex->getMessage(),400);

    }

}


}
