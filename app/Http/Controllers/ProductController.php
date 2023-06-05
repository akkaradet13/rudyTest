<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'description' => 'required|string',
            ]);

            $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();

            Product::create([
                'name' => $request->name,
                'image' => $imageName,
                'description' => $request->description
            ]);

            // $request->image->move(public_path('images'), $imageName);
            Storage::disk('public')->put($imageName,file_get_contents($request->image));
            return response()->json([
                'message' => "product successfully created."
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "somting went really wrong!"
            ],500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Product::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'description' => 'required|string',
            ]);

            $product = Product::find($request->id);
            if(!$product){
                return response()->json([
                    'message'=>'Product Not Found.'
                ],404);
            }

            $product->name = $request->name;
            $product->description = $request->description;

            if($request->image) {
                $storage = Storage::disk('public');

                if($storage->exists($product->image)){
                    $storage->delete($product->image);
                }
                $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
                $product->image = $imageName;
                Storage::disk('public')->put($imageName,file_get_contents($request->image));    
            }
            $product->save();
            // $request->image->move(public_path('images'), $imageName);
            return response()->json([
                'message' => "product successfully update."
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "somting went really wrong!"
            ],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Product::destroy($id);
    }

    /**
     * Search for a name
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        return Product::where('name', 'like', '%' . $name . '%')->get();
    }
}
