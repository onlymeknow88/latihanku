<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Mail\OTPVerification;
use App\Helpers\ResponseFormatter;
use Laravel\Fortify\Rules\Password;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
            // $request->validate([
            //     'name' => ['required', 'string', 'max:255'],
            //     'username' => ['required', 'string', 'max:255', 'unique:users'],
            //     'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            //     // 'phone' => ['required', 'string', 'max:255'],
            //     'password' => ['required', 'string', new Password]
            // ]);

            // $verification_code  = substr(md5(uniqid(rand(), true)), 0, 6);

            // User::create([
            //     'name' => $request->name,
            //     'email' => $request->email,
            //     'username' => $request->username,
            //     // 'phone' => $request->phone,
            //     'password' => Hash::make($request->password),

            // ]);

            // $user = User::where('email', $request->email)->first();
            // // $user->update([
            // //     'verification_code' => $verification_code
            // // ]);

            // $tokenResult = $user->createToken('authToken')->plainTextToken;

            // $verification = [
            //     'email' => $request->email,
            //     'otp' => $verification_code
            // ];

            // Mail::to($request->email)->send(new OTPVerification($verification));

            return ResponseFormatter::success([
                // 'access_token' => $tokenResult,
                // 'token_type' => 'Bearer',
                'user' => $request->all()
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
        $data = $request->all();

        $user = Auth::user();
        $user->update($data);

        return ResponseFormatter::success($user,'Profile Updated');
    }

    public function getUserbyName(Request $request) {
        $userid = $request->id;

        if(Auth::user()->roles == 'ADMIN'){
            $data = User::where('id',$userid)->first();
            return ResponseFormatter::success($data, 'Get Name User Berhasil');
        }

        return ResponseFormatter::error(
            null,
            'Data User tidak ada',
            404
        );
    }




}
