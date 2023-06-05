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
            $imageName = Str::random(32) . "." . $request->image->getClientOriginalExtension();
            $request->validate([
                'name' => 'required',
                'description' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
            // Storage::disk('public')->put($imageName, file_get_contents($request->image));
            // $imageName = $request->file('image')->store('images', 'public');
            // Storage::disk('local')->put($imageName, file_get_contents($request->image));
            Storage::disk('public')->putFileAs('images', $request->file('image'), $imageName);
        } catch (Exception $e) {
            throw new Exception($e);
        }
        return Product::create([
            'name' => $request->name,
            'image' => $imageName,
            'description' => $request->description
        ]);
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
    $product = Product::find($request->id);
    $imageName = '';

    if ($request->hasFile('image')) {
    }
    $imageName = Str::random(32) . "." . $request->file('image')->getClientOriginalExtension();
    print($imageName);
    Storage::disk('public')->put($imageName, $request->file('image')->get());
    // Storage::disk('public')->put($imageName, file_get_contents($request->image));


    if ($product->image) {
        $imageName = $product->image;
    }

    $productData = [
        'name' => $request->name,
        'image' => $imageName,
        'description' => $request->description
    ];

    $product->update($productData);

    return response()->json(['message' => 'Image updated successfully']);
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
