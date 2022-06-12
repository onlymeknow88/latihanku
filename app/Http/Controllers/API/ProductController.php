<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductGallery;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function addProduct(Request $request){

        $data = $request->except('image');

        $product = Product::create($data);

        if($request->hasFile('image'))
        {
            $url = $request->file('image')->store('public/gallery');

            ProductGallery::create([
                'products_id' => $product->id,
                'url' => $url,
                'path' => $url,
            ]);

        }
        return ResponseFormatter::success(
            $product,
            'Upload berhasil'
        );

    }

    public function editProduct(Request $request){
        $id = $request->id;

        $data = $request->except('id','_method');

        $product = Product::with(['category','galleries'])->find($id);
        $product->update($data);

        $cek_image = ProductGallery::where('products_id',$product->id)->first();
        Storage::delete($cek_image->path);

        $url = $request->file('image')->store('public/gallery');

        ProductGallery::where('id','=',$cek_image->id ?? null)->update([
            'url' => $url,
            'path' => $url
            ]);

        return ResponseFormatter::success(
            $product,
            'Data Product Berhasil diedit'
        );
    }

    public function getAll() {
        $products = Product::with(['category','galleries'])->get();
        return ResponseFormatter::success(
            $products,
            'Data berhasil diambil'
        );
    }

    // public function getProductByCategory(Request $request) {
    //     $categoryName = $request->name;

    //     $categories = ProductCategory::where('name',$categoryName)->first();
    //     $categoryId = $categories->id;

    //     $product = Product::with(['category','galleries'])->where('categories_id',$categoryId)->get();

    //     return ResponseFormatter::success(
    //         $product,
    //         'Data list produk by category berhasil diambil'
    //     );

    // }

    // public function getSearchProductByCategory(Request $request){
    //     $categoryName = $request->name;
    //     $cari = $request->cari;

    //     $categories = ProductCategory::where('name',$categoryName)->first();
    //     $categoryId = $categories->id;


    //     $product = Product::with(['category','galleries'])->where('categories_id',$categoryId)->where('name', 'like', '%' . $cari . '%')->get();

    //     return ResponseFormatter::success(
    //         $product,
    //         'Data Search berhasil diambil'
    //     );
    // }



    public function deleteProduct(Request $request){
        $id = $request->id;

        $product = Product::with(['category','galleries'])->find($id);

        $gallery = ProductGallery::where('products_id',$product->id)->first();

        Storage::delete($gallery->path);

        $product->delete();
        $gallery->delete();

        return ResponseFormatter::success(
            null,
            'Data Product Berhasil dihapus'
        );

    }
}
