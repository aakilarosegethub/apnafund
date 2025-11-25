<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Admins\SubCategory;
use Exception;

class SubCategoryController extends Controller
{
    function index() {
        $pageTitle = 'Subcategories';
        $categories = Category::active()->orderBy('name')->get();
        $subcategories = SubCategory::with('category')
            ->latest()
            ->searchable(['name'])
            ->paginate(getPaginate());

        return view('admin.page.subcategories', compact('pageTitle', 'categories', 'subcategories'));
    }

    function store($id = 0) {
        $this->validate(request(), [
            'name' => 'required|string|max:190|unique:sub_categories,name,' . $id,
            'category_id' => 'required|exists:categories,id',
        ], [
            'category_id.required' => 'Please select a parent category',
            'category_id.exists' => 'Selected category does not exist',
        ]);

        if ($id) {
            $subcategory = SubCategory::findOrFail($id);
            $message = 'Subcategory successfully updated';
        } else {
            $subcategory = new SubCategory();
            $message = 'Subcategory successfully added';
        }

        $subcategory->name = request('name');
        $subcategory->slug = slug(request('name'));
        $subcategory->category_id = request('category_id');
        $subcategory->save();

        $toast[] = ['success', $message];

        return back()->withToasts($toast);
    }

    function status($id) {
        $subcategory = SubCategory::findOrFail($id);
        $subcategory->status = $subcategory->status == 'active' ? 'inactive' : 'active';
        $subcategory->save();

        $toast[] = ['success', 'Status updated successfully'];
        return back()->withToasts($toast);
    }

    function delete($id) {
        $subcategory = SubCategory::findOrFail($id);
        $subcategory->delete();

        $toast[] = ['success', 'Subcategory deleted successfully'];
        return back()->withToasts($toast);
    }
}

