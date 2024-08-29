<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthAdminController extends Controller
{
    public function login(Request $request)
    {
        // Validasi input dari request
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Mencoba melakukan login dengan guard 'admin'
        if (Auth::guard('admin')->attempt($credentials)) {
            // Mendapatkan user yang sudah terautentikasi
            $user = Auth::guard('admin')->user();

            $token = $user->createToken('adminToken')->plainTextToken;

            // Mengembalikan respon sukses dengan token
            return response()->json(['token' => $token]);
        }

        // Jika autentikasi gagal, kembalikan pesan error
        return response()->json(['error' => 'Email or password invalid'], 401);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:admin',
            'password' => 'required|string|min:4',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = Admin::create([
            'username' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('adminToken')->plainTextToken;

        return response()->json([
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
}
