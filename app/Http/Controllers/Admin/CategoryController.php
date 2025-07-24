<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Exception;
use Illuminate\Validation\Rules\File;

class CategoryController extends Controller
{
    function index() {
        $pageTitle  = 'Campaign Categories';
        $categories = Category::searchable(['name'])->latest()->with('campaigns')->paginate(getPaginate());

        return view('admin.page.categories', compact('pageTitle', 'categories'));
    }

    function store($id = 0) {
        $imageValidation = $id ? 'nullable' : 'required';

        $this->validate(request(), [
            'image' => [$imageValidation, File::types(['png', 'jpg', 'jpeg'])],
            'name'  => 'required|string|max:40|unique:categories,name,' . $id,
        ]);

        if ($id) {
            $category = Category::findOrFail($id);
            $message  = 'Category successfully updated';
        } else {
            $category = new Category();
            $message  = 'Category successfully added';
        }

        if (request()->hasFile('image')) {
            try {
                $category->image = fileUploader(request('image'), getFilePath('category'), getFileSize('category'), @$category->image);
            } catch (Exception) {
                $toast[] = ['error', 'Image uploading process has failed'];

                return back()->withToasts($toast);
            }
        }

        $category->name = request('name');
        $category->slug = slug(request('name'));
        $category->save();

        $toast[] = ['success', $message];

        return back()->withToasts($toast);
    }

    function status($id) {
        return Category::changeStatus($id);
    }
}
