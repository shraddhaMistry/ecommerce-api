<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $categories = Category::select('categories.id', 'categories.name as category_name', 'parents.name as parent_category_name', 'categories.parent_id')
            ->leftJoin('categories as parents', 'categories.parent_id', '=', 'parents.id')
            ->get();
        // Build category tree
        // $categories = $this->buildCategoryTree($categories);
        return response(['message' => 'Get all Categories list successfully!', 'data' => $categories], 200);
    }

    // Function to recursively build category tree
    public function buildCategoryTree($categories, $parentId = null)
    {
        $tree = [];
        foreach ($categories as $category) {
            if ($category['parent_id'] === $parentId) {
                $children = $this->buildCategoryTree($categories, $category['id']);
                if ($children) {
                    $category['children'] = $children;
                }
                $tree[] = $category;
            }
        }
        return $tree;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $category = Category::create($validator->validated());

        return response(['message' => 'Category created successfully!', 'data' => $category], 200);
    }

    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $category->update($validator->validated());

        return response(['message' => 'Category updated successfully!', 'data' => $category], 200);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);

        return response([
            'message' => 'Get Categories details successfully!',
            'data' => $category
        ], 200);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response(['message' => 'Category deleted successfully!', 'data' => []], 200);
    }
}
