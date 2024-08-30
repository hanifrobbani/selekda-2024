<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PortofolioResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Portofolio;

class PortofolioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Portofolio::latest()->get();

        return response()->json([
            'data' => PortofolioResource::collection($data),
            'message' => 'Data portofolio found',
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


        $porto = Portofolio::create([
            'title' => $request->get('title'),
            'deskripsi' => $request->get('deskripsi'),
            'author' => $request->get('author'),
        ]);

        return response()->json([
            'data' => new PortofolioResource($porto),
            'message' => 'Portofolio created successfully.',
            'success' => true
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json([
            'data' => new PortofolioResource($id),
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
    public function update(Request $request, Portofolio $porto)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:155',
            'deskripsi' => 'required',
            'author' => 'required|max:255',
            'image' => 'nullable|file'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => [],
                'message' => $validator->errors(),
                'success' => false
            ]);
        }

        $porto->update([
            'title' => $request->get('title'),
            'deskripsi' => $request->get('deskripsi'),
            'author' => $request->get('author'),
        ]);

        return response()->json([
            'data' => new PortofolioResource($porto),
            'message' => 'Post updated successfully',
            'success' => true
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Portofolio $porto)
    {
        $porto->delete();

        return response()->json([
            'data' => [],
            'message' => 'Post deleted successfully',
            'success' => true
        ]);
    }
}
