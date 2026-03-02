<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DsaPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DsaPostController extends Controller
{
    /**
     * Display a listing of DSA posts.
     */
    public function index()
    {
        $posts = DsaPost::orderBy('order', 'asc')->orderBy('id', 'desc')->get();
        return view('admin.blog.index', compact('posts'));
    }

    /**
     * Show the form for creating a new DSA post.
     */
    public function create()
    {
        return view('admin.blog.create');
    }

    /**
     * Store a newly created DSA post.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'feature_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'excerpt' => 'nullable|string|max:500',
            'order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->only(['title', 'content', 'excerpt', 'order', 'status']);

        // Generate unique slug
        $baseSlug = Str::slug($request->title);
        $slug = $baseSlug;
        $count = 1;
        while (DsaPost::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count++;
        }
        $data['slug'] = $slug;

        // Handle feature image upload
        if ($request->hasFile('feature_image')) {
            $data['feature_image'] = fileUploader(
                $request->file('feature_image'),
                getFilePath('blog'),
                getFileSize('blog')
            );
        }

        $data['order'] = $data['order'] ?? 0;

        DsaPost::create($data);

        return redirect()->route('admin.blog.index')
            ->with('success', 'DSA post created successfully.');
    }

    /**
     * Show the form for editing the specified DSA post.
     */
    public function edit(DsaPost $blog)
    {
        return view('admin.blog.edit', ['post' => $blog]);
    }

    /**
     * Update the specified DSA post.
     */
    public function update(Request $request, DsaPost $blog)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:dsa_posts,slug,' . $blog->id,
            'content' => 'nullable|string',
            'feature_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'excerpt' => 'nullable|string|max:500',
            'order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->only(['title', 'slug', 'content', 'excerpt', 'order', 'status']);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($request->title);
        }

        // Handle feature image upload
        if ($request->hasFile('feature_image')) {
            $oldImage = $blog->feature_image;
            $data['feature_image'] = fileUploader(
                $request->file('feature_image'),
                getFilePath('blog'),
                getFileSize('blog'),
                $oldImage
            );
        }

        $data['order'] = $data['order'] ?? 0;

        $blog->update($data);

        return redirect()->route('admin.blog.index')
            ->with('success', 'DSA post updated successfully.');
    }

    /**
     * Remove the specified DSA post.
     */
    public function destroy(DsaPost $blog)
    {
        if ($blog->feature_image) {
            $imagePath = public_path(getFilePath('blog') . '/' . $blog->feature_image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $blog->delete();

        return redirect()->route('admin.blog.index')
            ->with('success', 'DSA post deleted successfully.');
    }
}
