<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\TokenFirebase;
use App\Helpers\ResponseFormatter;
use Laravel\Fortify\Rules\Password;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{


    public function fetch(Request $request)
    {
        return ResponseFormatter::success($request->user(),'Data profile user berhasil diambil');
    }


    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ],'Authentication Failed', 500);
            }

            $user = User::where('email', $request->email)->first();
            if ( ! Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Invalid Credentials');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ],'Authenticated');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ],'Authentication Failed', 500);
        }
    }

    public function logout(Request $request)
    {

        $token = $request->user()->currentAccessToken()->delete();


        return ResponseFormatter::success($token,'Token Revoked');
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                // 'phone' => ['required', 'string', 'max:255'],
                'password' => ['required', 'string', new Password]
            ]);


            $data = $request->all();
            $data['password'] = Hash::make($data['password']);
            $data['url'] = $request->file('image')->store('public/profile');
            $data['profile_photo_path'] = $request->file('image')->store('public/profile');

            User::create($data);

            $user = User::where('email', $request->email)->first();

            $tokenFirebase = TokenFirebase::create([
                'token' => $request->token_firebase,
                'users_id' => $user->id
            ]);

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user,
                'token_firebase' => $tokenFirebase
            ],'User Registered');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ],'Authentication Failed', 500);
        }

    }

    public function updateProfile(Request $request)
    {
        $id = $request->id;
        $data = $request->all();

        $user = User::find($id);
        $user->update($data);

        return ResponseFormatter::success($user,'Profile Updated');
    }

    public function updatePhoto(Request $request) {
        $id = $request->id;
        $data = $request->except('id','_method');

        $user = User::find($id);

        Storage::delete($user->profile_photo_path);

        if($request->hasFile('image'))
        {
            $data['url'] = $request->file('image')->store('public/profile');
            $data['profile_photo_path'] = $request->file('image')->store('public/profile');

        }
        $user->update($data);

        return ResponseFormatter::success($user,'Photo Updated');
    }

    public function autoLogin(Request $request)
    {
        $user = User::where('email',$request->email)->first();
        return ResponseFormatter::success([
            'user' => $user
        ],'Authenticated');
    }

    // public function saveToken(Request $request)
    // {
    //     $data = $request->all();

    //     $user = TokenFirebase::create($data);
    //     return ResponseFormatter::success($user,'Token Saved');
    // }

    // public function getToken(Request $request){
    //     $user = User::with(['tokenFirebase'])->where('email',$request->email)->first();
    //     return ResponseFormatter::success(['user' => $user],'Token Fetched');
    // }

}
