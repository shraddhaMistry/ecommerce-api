<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('categories')->get();

        $products->each(function ($product) {
            $categoryNames = $product->categories->pluck('name')->join(', ');
            $product->category_names = $categoryNames;
        });
    
        return response(['message' => 'Get all Product list successfully!', 'data' => $products], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|array',
            'category_id.*' => 'exists:categories,id'
        ]);

        if ($validator->fails()) {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $product = Product::create(['name' => $request->name]);
        $product->categories()->attach($request->category_id);
        $product->load('categories');
    
        return response(['message' => 'Category created successfully!', 'data' => $product], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|array',
            'category_id.*' => 'exists:categories,id'
        ]);

        if ($validator->fails()) {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $product = Product::find($id);

        if (!$product) {
            return response(['message' => 'Product not found!', 'data' => []], 404);
        }

        // Update the product's name
        $product->name = $request->name;
        $product->save();
    
        $product->categories()->sync($request->category_id);
        $product->load('categories');

        return response(['message' => 'Product updated successfully!', 'data' => $product], 200);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response(['message' => 'Product deleted successfully!', 'data' => []], 200);
    }
}
