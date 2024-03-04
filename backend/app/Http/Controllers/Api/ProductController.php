<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json(['status' => 200, 'products' => $products]);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'prix' => 'required',
            'image' => 'image|mimes:jpeg,jpg,png|max:2048',
        ]);

        if ($validation->fails()) {
            return response()->json(['status' => 404, 'errors' => $validation->errors()]);
        }

        $product = new Product();
        $product->title = $request->title;
        $product->content = $request->content;
        $product->prix = $request->prix;

        if ($request->hasFile('image')) {
            $imagePath = 'images/' . time() . '.' . $request->image->extension();
            Storage::disk('public')->put($imagePath, file_get_contents($request->image));
            $product->image = $imagePath;
        }

        $product->save();
        return response()->json(['status' => 200, 'message' => 'Product created']);
    }

    public function show($id)
    {
        $product = Product::find($id);
        if ($product) {
            return response()->json(['status' => 200, 'product' => $product]);
        }
        return response()->json(['status' => 404, 'message' => 'Product not found']);
    }

    public function update(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'prix' => 'required',
            'image' => 'image|mimes:jpeg,jpg,png|max:2048',
        ]);

        if ($validation->fails()) {
            return response()->json(['status' => 404, 'errors' => $validation->errors()]);
        }

        $product = Product::find($request->id);
        if (!$product) {
            return response()->json(['status' => 404, 'message' => 'Product not found']);
        }
        $product->title = $request->title;
        $product->content = $request->content;
        $product->prix = $request->prix;

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($product->image);
            $imagePath = 'images/' . time() . '.' . $request->image->extension();
            Storage::disk('public')->put($imagePath, file_get_contents($request->image));
            $product->image = $imagePath;
        }

        $product->save();
        return response()->json(['status' => 200, 'message' => 'Product updated']);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product) {
            Storage::disk('public')->delete($product->image);
            $product->delete();
            return response()->json(['status' => 200, 'message' => 'Product deleted']);
        }
        return  response()->json(['status' => 404, 'message' => 'Product not found']);
    }
}
