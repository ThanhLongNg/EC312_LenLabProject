<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $q = Post::query();

        // search
        if ($request->filled('q')) {
            $q->where('title', 'like', '%' . $request->q . '%');
        }

        // category filter
        if ($request->filled('category')) {
            $q->where('category', $request->category);
        }

        // status filter
        if ($request->filled('status')) {
            $q->where('is_published', $request->status === 'public');
        }

        $posts = $q->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:posts,slug'],
            'category' => ['nullable', 'string', 'max:50'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
            'published_at' => ['nullable', 'date'],
        ]);

        // auto slug nếu bỏ trống
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']) . '-' . time();
        }

        // trạng thái theo button action
        $action = $request->input('action', 'draft'); // publish|draft
        $data['is_published'] = ($action === 'publish');
        $data['published_at'] = $data['is_published'] ? ($data['published_at'] ?? now()) : null;

        // upload ảnh
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('uploads/posts', 'public');
        }

        Post::create($data);

        return redirect()->route('admin.posts.index')->with('success', 'Tạo bài viết thành công');
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:posts,slug,' . $post->id],
            'category' => ['nullable', 'string', 'max:50'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
            'published_at' => ['nullable', 'date'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']) . '-' . time();
        }

        // trạng thái theo button action
        $action = $request->input('action', 'draft'); // publish|draft
        $data['is_published'] = ($action === 'publish');
        $data['published_at'] = $data['is_published'] ? ($data['published_at'] ?? now()) : null;

        if ($request->hasFile('thumbnail')) {
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('uploads/posts', 'public');
        }

        $post->update($data);

        return redirect()->route('admin.posts.index')->with('success', 'Cập nhật thành công');
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        if ($post->thumbnail) {
            Storage::disk('public')->delete($post->thumbnail);
        }

        $post->delete();

        return back()->with('success', 'Đã xóa bài viết');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!is_array($ids) || count($ids) === 0) {
            return back()->with('error', 'Bạn chưa chọn bài viết nào.');
        }

        // xóa thumbnail nếu có
        $posts = Post::whereIn('id', $ids)->get();
        foreach ($posts as $post) {
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail);
            }
        }

        Post::whereIn('id', $ids)->delete();

        return back()->with('success', 'Đã xóa ' . count($ids) . ' bài viết');
    }
}
