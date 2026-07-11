<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admins\HeaderCategory;
use App\Models\Category;
use Illuminate\Validation\Rule;

class HeaderCategoryController extends Controller
{
    public function index()
    {
        $pageTitle = 'Header Categories';

        $headerCategoriesQuery = HeaderCategory::searchable(['label']);

        $sortField = request('sort_by', 'sort_order');
        $sortDir = request('sort_dir', 'asc');

        if (! in_array($sortField, ['id', 'sort_order'])) {
            $sortField = 'sort_order';
        }
        if (! in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'asc';
        }

        $headerCategoriesQuery->orderBy($sortField, $sortDir);
        if ($sortField !== 'id') {
            $headerCategoriesQuery->orderByDesc('id');
        }

        $headerCategories = $headerCategoriesQuery
            ->paginate(getPaginate())
            ->appends(request()->all());

        $categories = Category::active()->orderBy('name')->get();

        return view('admin.page.header-categories', compact('pageTitle', 'headerCategories', 'categories'));
    }

    public function store($id = 0)
    {
        $this->validate(request(), [
            'label' => [
                'required',
                'string',
                'max:190',
                Rule::unique('header_categories', 'label')->ignore($id),
            ],
            'slug' => [
                'required',
                'string',
                'max:190',
                Rule::unique('header_categories', 'slug')->ignore($id),
            ],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['exists:categories,id'],
        ], [
            'label.required' => 'Label is required',
            'label.unique' => 'A header category with this label already exists.',
            'slug.required' => 'Slug is required',
            'slug.unique' => 'A header category with this slug already exists.',
        ]);

        if ($id) {
            $headerCategory = HeaderCategory::findOrFail($id);
            $message = 'Header category successfully updated';
        } else {
            $headerCategory = new HeaderCategory;
            $message = 'Header category successfully added';
        }

        $headerCategory->label = request('label');
        $headerCategory->slug = slug(request('slug'));
        $categoryIds = request('category_ids', []);
        $headerCategory->category_ids = is_array($categoryIds) ? array_values(array_map('intval', $categoryIds)) : [];
        $headerCategory->category_id = ! empty($headerCategory->category_ids) ? $headerCategory->category_ids[0] : null;
        $headerCategory->sort_order = request('sort_order', 0);
        $headerCategory->status = request('status', 'active');
        $headerCategory->save();

        $toast[] = ['success', $message];

        return back()->withToasts($toast);
    }

    public function status($id)
    {
        $headerCategory = HeaderCategory::findOrFail($id);
        $headerCategory->status = $headerCategory->status == 'active' ? 'inactive' : 'active';
        $headerCategory->save();

        $toast[] = ['success', 'Status updated successfully'];

        return back()->withToasts($toast);
    }

    public function delete($id)
    {
        $headerCategory = HeaderCategory::findOrFail($id);
        $headerCategory->delete();

        $toast[] = ['success', 'Header category deleted successfully'];

        return back()->withToasts($toast);
    }
}
