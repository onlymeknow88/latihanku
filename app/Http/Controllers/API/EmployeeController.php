<?php

namespace App\Http\Controllers\API;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\EmployeeGallery;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
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
            'employee'=>$employee,
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
}
