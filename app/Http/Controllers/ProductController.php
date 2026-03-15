<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try{
       $validate = $request->validate([
            "name"=>["string","required","min:3",
            Rule::unique("products","name")
            ],
"price"=>"required|numeric",
"image"=>"required|image|mimes:jpg,png,jpeg,gif"
        ]);
        $imagepath ="";
        if($request->hasFile("image")){
            $imagepath = $request->file("image")->store("products-image","public",);
        }
       $product = Product::create(
            [
              "name"=>$validate["name"],
              "price"=>$validate["price"],
              "image_url"=>$imagepath 
            ]
        );
        return response()->json([
          "data"=>$product,  
        ]);
        }catch(Exception $err){
            return response()->json(
                [
                    "data"=>$err->getMessage()
                ]
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
        $request->validate(
            [
                "name"=>["string","nullable","min:2","max:30",
                Rule::unique('products','name')->ignore($product->id)
                ],
                "price"=>"nullable|numeric|decimal:0,2",
                "image"=>"required|image|mimes:jpg,png,jpeg,gif"
            ],
            // [messages error]
        );
        $product->name = $request->name;
        $product->price = $request->price;
        $imagepath="";
        if($request->hasFile("image")){
            if($product->image_url && Storage::disk("public")->exists($product->image_url)){
                Storage::disk("public")->delete($product->image_url);
            }
            // $request->file("image")->
              $imagepath = $request->file("image")->store("products-image","public",);
              $product->image_url=$imagepath;
              
        }
        $product->update();

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
       {
        //
        $product= Product::findOrfail($id);
         if($product->image_url && Storage::disk("public")->exists($product->image_url)){
                Storage::disk("public")->delete($product->image_url);
            }
            $product->delete();
            return response()->json(
               "deleted",
            );
    }
}
