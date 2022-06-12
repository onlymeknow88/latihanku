<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;

class ProductCategoryController extends Controller
{
    // public function all(Request $request)
    // {
    //     $id = $request->input('id');
    //     $limit = $request->input('limit', 6);
    //     $name = $request->input('name');
    //     $show_product = $request->input('show_product');

    //     if($id)
    //     {
    //         $category = ProductCategory::with(['products'])->find($id);

    //         if($category)
    //             return ResponseFormatter::success(
    //                 $category,
    //                 'Data produk berhasil diambil'
    //             );
    //         else
    //             return ResponseFormatter::error(
    //                 null,
    //                 'Data kategori produk tidak ada',
    //                 404
    //             );
    //     }

    //     $category = ProductCategory::query();

    //     if($name)
    //         $category->where('name', 'like', '%' . $name . '%');

    //     if($show_product)
    //         $category->with('products');

    //     return ResponseFormatter::success(
    //         $category->paginate($limit),
    //         'Data list kategori produk berhasil diambil'
    //     );
    // }

    public function getCategory() {
        $category = ProductCategory::all();
        return ResponseFormatter::success(
            $category,
            'Data list kategori produk berhasil diambil'
        );
    }

    public function addCategory(Request $request) {
        $request->validate([
            'name' => 'required',
        ]);

        $data = $request->all();

        $category = ProductCategory::create($data);

        return ResponseFormatter::success(
            $category,
            'Data kategori produk berhasil ditambahkan'
        );
    }
}
