<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\EmployeeGallery;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{

    public function getAllEmployee()
    {
        $employee = Employee::with(['galleries'])->get();
        return ResponseFormatter::success($employee, 'Data Employee berhasil diambil');
    }

    public function addEmployee(Request $request)
    {
        $data = $request->except('image');
        $employee = Employee::create($data);

        if($request->hasFile('image'))
        {
            $url = $request->file('image')->store('public/employee/gallery');

            $gallery = EmployeeGallery::create([
                'employees_id' => $employee->id,
                'url' => $url,
                'path' => $url,
            ]);

        }

        return ResponseFormatter::success([
            'employee'=> $employee,
            'galleries' => $gallery
        ], 'Data employee berhasil ditambahkan');
    }

    public function getEmployee(Request $request)
    {
        $id = $request->id;
        $employee = Employee::with(['galleries'])->find($id);

        return ResponseFormatter::success(
           $employee
        , 'Data employee berhasil ditampilkan');
    }


    public function getEmployeeByPermit(Request $request)
    {
        $permit_id = $request->permit_id;
        $employee = Employee::where('permit_id',$permit_id)->first();
        $employee['url'] = $employee['galleries']['0']['url'];

        return ResponseFormatter::success(
           $employee
        , 'Data employee berhasil ditampilkan');
    }

    public function editEmployee(Request $request)
    {
        $id = $request->id;
        $data = $request->except('image','_method');
        $employee = Employee::with(['galleries'])->find($id);
        $employee->update($data);

        $cek_image = EmployeeGallery::where('employees_id', $employee->id)->first();
        Storage::delete($cek_image->path);

        if($request->hasFile('image'))
        {
            $url = $request->file('image')->store('public/employee/gallery');

            $cek_image->update([
                'url' => $url,
                'path' => $url,
            ]);

        }

        return ResponseFormatter::success([
            'employee'=>$employee,
            'galleries' => $cek_image
        ], 'Data employee berhasil diubah');

    }

    public function permitLogin(Request $request)
    {
        try {
            $permit_id = $request->permit_id;
            $employee = Employee::where('permit_id',$permit_id)->first();

            $verification_code  = substr(md5(uniqid(rand(), true)), 0, 6);

            $user = User::where('email', $employee->email)->first();
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
