<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admins\FooterCategory;
use App\Models\Category;
use Illuminate\Validation\Rule;

class FooterCategoryController extends Controller
{
    function index() {
        $pageTitle = 'Footer Categories';

        $footerCategoriesQuery = FooterCategory::with('category')->searchable(['label']);

        $sortField = request('sort_by', 'sort_order');
        $sortDir   = request('sort_dir', 'asc');

        if (!in_array($sortField, ['id', 'sort_order'])) {
            $sortField = 'sort_order';
        }
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'asc';
        }

        $footerCategoriesQuery->orderBy($sortField, $sortDir);
        if ($sortField !== 'id') {
            $footerCategoriesQuery->orderByDesc('id');
        }

        $footerCategories = $footerCategoriesQuery
            ->paginate(getPaginate())
            ->appends(request()->all());

        $categories = Category::active()->orderBy('name')->get();

        return view('admin.page.footer-categories', compact('pageTitle', 'footerCategories', 'categories'));
    }

    function store($id = 0) {
        $categoryIds = request('category_ids', []);
        $categoryIds = is_array($categoryIds) ? array_values(array_map('intval', $categoryIds)) : [];

        $this->validate(request(), [
            'label' => [
                'required',
                'string',
                'max:190',
                Rule::unique('footer_categories', 'label')->ignore($id)
            ],
            'slug' => ['required', 'string', 'max:190', Rule::unique('footer_categories', 'slug')->ignore($id)],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['exists:categories,id'],
        ], [
            'label.required' => 'Label is required',
            'label.unique' => 'A footer category with this label already exists.',
            'slug.required' => 'Slug is required.',
            'slug.unique' => 'A footer category with this slug already exists.',
        ]);

        if ($id) {
            $footerCategory = FooterCategory::findOrFail($id);
            $message = 'Footer category successfully updated';
        } else {
            $footerCategory = new FooterCategory();
            $message = 'Footer category successfully added';
        }

        $footerCategory->label = request('label');
        $footerCategory->slug = slug(request('slug'));
        $footerCategory->category_ids = $categoryIds;
        $footerCategory->category_id = !empty($categoryIds) ? $categoryIds[0] : null;
        $footerCategory->sort_order = request('sort_order', 0);
        $footerCategory->status = request('status', 'active');
        $footerCategory->save();

        $toast[] = ['success', $message];

        return back()->withToasts($toast);
    }

    function status($id) {
        $footerCategory = FooterCategory::findOrFail($id);
        $footerCategory->status = $footerCategory->status == 'active' ? 'inactive' : 'active';
        $footerCategory->save();

        $toast[] = ['success', 'Status updated successfully'];
        return back()->withToasts($toast);
    }

    function delete($id) {
        $footerCategory = FooterCategory::findOrFail($id);
        $footerCategory->delete();

        $toast[] = ['success', 'Footer category deleted successfully'];
        return back()->withToasts($toast);
    }
}
