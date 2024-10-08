<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Notif;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::count();

        return view('categories.index', [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create([
            "user_id"=>auth()->id(),
            "name" => $request->name,
            "slug" => Str::slug($request->name)
        ]);

        Notif::create([
            "title" => "Notification",
            "description" => "Category ".$request->name." has been added",
            "date" => date('Y-m-d')
        ]);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category has been created!');
    }

    public function show(Category $category)
    {
        return view('categories.show', [
            'category' => $category
        ]);
    }

    public function edit(Category $category)
    {
        return view('categories.edit', [
            'category' => $category
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update([
            "name" => $request->name,
            "slug" => Str::slug($request->name)
        ]);

        Notif::create([
            "title" => "Notification",
            "description" => "Category ".$request->name." has been updated",
            "date" => date('Y-m-d')
        ]);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category has been updated!');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category has been deleted!');
    }
}
