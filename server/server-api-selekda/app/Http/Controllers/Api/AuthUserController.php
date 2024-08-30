<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;


class AuthUserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();

        return response()->json([
            'data' => UserResource::collection($users),
            'message' => 'Data users found',
            'success' => true
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:4',
            'date_birth' => 'required|string',
            'phone_number' => 'required|string',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'date_birth' => $request->date_birth,
            'phone_number' => $request->phone_number,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function login(Request $request)
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json([
            'message' => 'logout success'
        ]);
    }

    public function show(User $user)
    {
        return response()->json([
            'data' => new UserResource($user),
            'message' => 'Data user found',
            'success' => true
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'date_birth' => 'required|string',
            'phone_number' => 'required|string|max:15',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => [],
                'message' => $validator->errors(),
                'success' => false
            ]);
        }

        // Jika ada gambar baru yang di-upload
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($user->image) {
                Storage::delete('public/images/' . $user->image);
            }

            // Simpan gambar baru
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('public/images', $imageName);

            // Update nama gambar baru
            $user->image = $imageName;
        }

        // Update data pengguna
        $user->update([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'date_birth' => $request->get('date_birth'),
            'phone_number' => $request->get('phone_number'),
        ]);

        return response()->json([
            'data' => new UserResource($user),
            'message' => 'User updated successfully',
            'success' => true
        ]);
    }
}
