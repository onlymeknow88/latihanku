<?php

namespace App\Http\Controllers\API;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    public function addCcow(Request $request) {
        $data = $request->all();
        $data['level'] = 2;
        $data['ccow_id'] = 0;
        $data['mitra_id'] = 0;
        $data['hiden'] = null;

        $ccow = Company::create($data);

        return ResponseFormatter::success([
            'ccow' => $ccow
        ], 'Data ccow berhasil ditambahkan');
    }

    public function getCcow() {
        $ccow = Company::where('level',2)->get();

        return ResponseFormatter::success([
            'ccow' => $ccow
        ], 'Data ccow ditampilkan');
    }

    public function addMitra(Request $request) {
        $data = $request->all();
        $data['level'] = 3;
        $data['mitra_id'] = 0;
        $data['hiden'] = null;

        $mitra = Company::create($data);

        return ResponseFormatter::success([
            'mitra' => $mitra
        ], 'Data mitra berhasil ditambahkan');
    }

    public function getMitra(Request $request) {
        $ccow_id = $request->ccow_id;

        $mitra = Company::orderBy('id','DESC')->where('level','=',3)->where('ccow_id',$ccow_id)->get();

        return ResponseFormatter::success([
            'mitra' => $mitra
        ], 'Data ccow ditampilkan');
    }

    public function addSubmitra(Request $request) {
        $data = $request->all();
        $data['level'] = 4;
        $data['hiden'] = null;

        $sub_mitra = Company::create($data);

        return ResponseFormatter::success([
            'sub_mitra' => $sub_mitra
        ], 'Data sub_mitra berhasil ditambahkan');
    }

    public function getSubMitra(Request $request) {
        $ccow_id = $request->ccow_id;
        $mitra_id = $request->mitra_id;

        $submitra = Company::orderBy('id','DESC')->where('level','=',4)->where('ccow_id',$ccow_id)->where('mitra_id',$mitra_id)->get();

        return ResponseFormatter::success([
            'submitra' => $submitra,
        ], 'Data ccow ditampilkan');
    }


}
