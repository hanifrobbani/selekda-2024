<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Blog;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Blog::latest()->get();

        return response()->json([
            'data' => BlogResource::collection($data),
            'message' => 'Data Blog found',
            'success' => true
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:155',
            'deskripsi' => 'required',
            'author' => 'required|max:255',
            'tags' => 'required|max:255',
            'image' => 'nullable|file'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => [],
                'message' => $validator->errors(),
                'success' => false
            ]);
        }
        if ($request->hasFile('image')) {
            // Simpan gambar baru
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('public/images', $imageName);
           
        }


        $blog = Blog::create([
            'title' => $request->get('title'),
            'deskripsi' => $request->get('deskripsi'),
            'author' => $request->get('author'),
            'tags' => $request->get('tags'),
        ]);

        return response()->json([
            'data' => new BlogResource($blog),
            'message' => 'Blog created successfully.',
            'success' => true
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        return response()->json([
            'data' => new BlogResource($blog),
            'message' => 'Data post found',
            'success' => true
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blog $blog)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:155',
            'deskripsi' => 'required',
            'author' => 'required|max:255',
            'tags' => 'required|max:255',
            'image' => 'nullable|file'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => [],
                'message' => $validator->errors(),
                'success' => false
            ]);
        }

        $blog->update([
            'title' => $request->get('title'),
            'deskripsi' => $request->get('deskripsi'),
            'author' => $request->get('author'),
            'tags' => $request->get('tags'),
        ]);

        return response()->json([
            'data' => new BlogResource($blog),
            'message' => 'Post updated successfully',
            'success' => true
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        $blog->delete();

        return response()->json([
            'message' => 'Blog deleted successfully',
        ]);
    }
}
