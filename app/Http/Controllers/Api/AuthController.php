<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission; // استيراد ضروري

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials do not match our records.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // --- [الحل المؤكد] ---

        $user->load('roles'); // تحميل الأدوار أولاً

        if ($user->hasRole('Super Admin')) {
            // جلب كل الصلاحيات من قاعدة البيانات
            $allPermissions = Permission::all();

            // إلحاقها بالدور الأول للمستخدم (في الذاكرة فقط)
            if ($user->roles->isNotEmpty()) {
                $user->roles->first()->permissions = $allPermissions;
            }
        } else {
            // للمستخدمين الآخرين، قم بتحميل الصلاحيات بشكل طبيعي
            $user->load('roles.permissions');
        }

        // --- [نهاية الحل] ---

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
