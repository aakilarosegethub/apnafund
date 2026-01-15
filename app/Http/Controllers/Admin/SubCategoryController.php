<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Admins\SubCategory;
use Exception;
use Illuminate\Validation\Rule;

class SubCategoryController extends Controller
{
    function index() {
        $pageTitle = 'Subcategories';
        $categories = Category::active()->orderBy('name')->get();

        $subcategoriesQuery = SubCategory::with('category')
            ->searchable(['name']);

        if (request()->filled('category_id')) {
            $subcategoriesQuery->where('category_id', request('category_id'));
            $selectedCategory = $categories->firstWhere('id', (int) request('category_id'));
            if ($selectedCategory) {
                $pageTitle = 'Subcategories: ' . $selectedCategory->name;
            }
        }

        // Sorting: by id or sort_order
        $sortField = request('sort_by', 'sort_order');
        $sortDir   = request('sort_dir', 'asc');

        if (!in_array($sortField, ['id', 'sort_order'])) {
            $sortField = 'sort_order';
        }
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'asc';
        }

        $subcategoriesQuery->orderBy($sortField, $sortDir);
        if ($sortField !== 'id') {
            $subcategoriesQuery->orderByDesc('id');
        }

        $subcategories = $subcategoriesQuery
            ->paginate(getPaginate())
            ->appends(request()->all());

        return view('admin.page.subcategories', compact('pageTitle', 'categories', 'subcategories'));
    }

    function store($id = 0) {
        $this->validate(request(), [
            'name' => [
                'required',
                'string',
                'max:190',
                Rule::unique('sub_categories', 'name')->where(function ($query) {
                    return $query->where('category_id', request('category_id'));
                })->ignore($id)
            ],
            'category_id' => 'required|exists:categories,id',
        ], [
            'category_id.required' => 'Please select a parent category',
            'category_id.exists' => 'Selected category does not exist',
            'name.unique' => 'A subcategory with this name already exists in the selected category.',
        ]);

        if ($id) {
            $subcategory = SubCategory::findOrFail($id);
            $message = 'Subcategory successfully updated';
        } else {
            $subcategory = new SubCategory();
            $message = 'Subcategory successfully added';
        }

        $subcategory->name = request('name');
        $subcategory->category_id = request('category_id');
        
        // Generate unique slug within the category
        $baseSlug = slug(request('name'));
        $slug = $baseSlug;
        $counter = 1;
        
        while (SubCategory::where('category_id', request('category_id'))
            ->where('slug', $slug)
            ->where('id', '!=', $id)
            ->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        $subcategory->slug = $slug;
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

