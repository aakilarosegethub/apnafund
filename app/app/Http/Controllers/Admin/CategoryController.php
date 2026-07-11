<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $pageTitle = 'Campaign Categories';
        $query = Category::searchable(['name'])
            ->with(['campaigns', 'subcategories'])
            ->withCount(['campaigns', 'subcategories']);

        // Sorting: by id or sort_order
        $sortField = request('sort_by', 'sort_order');
        $sortDir = request('sort_dir', 'asc');

        if (! in_array($sortField, ['id', 'sort_order'])) {
            $sortField = 'sort_order';
        }

        if (! in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'asc';
        }

        $query->orderBy($sortField, $sortDir);
        if ($sortField !== 'name') {
            $query->orderBy('name');
        }

        $categories = $query->paginate(getPaginate())->appends(request()->all());

        return view('admin.page.categories', compact('pageTitle', 'categories'));
    }

    public function store(Request $request, $id = 0)
    {
        $this->validate($request, [
            'name' => 'required|string|max:40|unique:categories,name,'.$id,
            'sort_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ]);

        if ($id) {
            $category = Category::findOrFail($id);
            $message = 'Category successfully updated';
        } else {
            $category = new Category;
            $message = 'Category successfully added';
        }

        $category->name = $request->input('name');
        $category->slug = slug($request->input('name'));
        $category->sort_order = $request->input('sort_order', 0);
        $category->meta_title = $request->input('meta_title');
        $category->meta_description = $request->input('meta_description');
        $category->meta_keywords = $request->input('meta_keywords');
        $category->save();

        $toast[] = ['success', $message];

        return back()->withToasts($toast);
    }

    public function status($id)
    {
        return Category::changeStatus($id);
    }
}
