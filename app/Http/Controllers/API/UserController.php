<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\OTPVerification;
use App\Models\TokenFirebase;
use App\Helpers\ResponseFormatter;
use Laravel\Fortify\Rules\Password;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

            $verification_code  = substr(md5(uniqid(rand(), true)), 0, 6);

            $user = User::where('email', $request->email)->first();
            $user->update([
                'verification_code' => $verification_code
            ]);

            if ( ! Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Invalid Credentials');
            }

            $mail  = ResponseFormatter::email();

            $mail->addAddress($request->email);
            $mail->Subject = 'Verification Code';
            $body = file_get_contents(resource_path('views/emails/verification.blade.php'));
            $body = ResponseFormatter::strReplace(
                $body, $request->email,  $verification_code
            );

            $mail->MsgHTML($body);
            $mail->send();

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

        $user = User::find($request->user()->id);

        $user->update([
            'verification_code' => false,
            'verified' => false
        ]);

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
            $data['verification_code']  = substr(md5(uniqid(rand(), true)), 0, 6);
            $data['password'] = Hash::make($data['password']);
            $data['url'] = $request->file('image')->store('public/profile');
            $data['profile_photo_path'] = $request->file('image')->store('public/profile');
            $data['verified'] = false;

            User::create($data);

            $user = User::where('email', $request->email)->first();

            $tokenFirebase = TokenFirebase::create([
                'token' => $request->token_firebase,
                'users_id' => $user->id
            ]);

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            $mail  = ResponseFormatter::email();

            $mail->addAddress($request->email);
            $mail->Subject = 'Verification Code';
            $body = file_get_contents(resource_path('views/emails/verification.blade.php'));
            $body = ResponseFormatter::strReplace(
                $body, $request->email,  $data['verification_code']
            );

            $mail->MsgHTML($body);
            $mail->send();

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

    public function verificationLogin(Request $request) {
        $verification_code = $request->verification_code;

        $user = User::where('verification_code', $verification_code)->first();
        if ( ! $user) {
            return ResponseFormatter::error([
                'message' => 'Verification Code Not Found'
            ],'Verification Code Not Found', 404);
        }

        $user->update([
            'verification_code' => false,
            'verified' => true
        ]);

        return ResponseFormatter::success([
            'user' => $user,
        ],'Verified');

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

    public function getUserById(Request $request) {
        $user = User::with(['tokenFirebase'])->where('email', $request->email)->where('roles',$request->roles)->first();
        return ResponseFormatter::success($user,'User Found');
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


    public function loginEmployee(Request $request) {
        try {
            $request->validate([
                'email' => 'email|required',
            ]);

            $verification_code  = substr(md5(uniqid(rand(), true)), 0, 6);

            $user = User::where('email', $request->email)->first();
            $user->update([
                'verification_code' => $verification_code
            ]);

            $mail  = ResponseFormatter::email();

            $mail->addAddress($request->email);
            $mail->Subject = 'Verification Code';
            $body = file_get_contents(resource_path('views/emails/verification.blade.php'));
            $body = ResponseFormatter::strReplace(
                $body, $request->email,  $verification_code
            );

            $mail->MsgHTML($body);
            $mail->send();

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

}
