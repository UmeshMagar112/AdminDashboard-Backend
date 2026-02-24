<?php
// app/Http/Controllers/Admin/AuthController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\CustomerResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->status) {
            return response()->json(['message' => 'Your account has been deactivated.'], 403);
        }

        // Only allow admin or manager to access admin panel
        if (!$user->hasAnyRole(['admin', 'manager'])) {
            return response()->json(['message' => 'Unauthorized. Admin access only.'], 403);
        }

        $token = $user->createToken('admin-token', ['*'], now()->addDays(30))->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => new CustomerResource($user->load('roles')),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new CustomerResource($request->user()->load('roles')),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name'         => ['sometimes', 'string', 'max:191'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'avatar'       => ['nullable', 'string'],
            'password'     => ['nullable', 'string', 'min:8', 'confirmed'],
            'current_password' => ['required_with:password', 'string'],
        ]);

        if (isset($data['password'])) {
            if (!Hash::check($data['current_password'], $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['Current password is incorrect.'],
                ]);
            }
            $data['password'] = Hash::make($data['password']);
        }

        $user->update(array_filter($data, fn($key) => $key !== 'current_password', ARRAY_FILTER_USE_KEY));

        return response()->json([
            'message' => 'Profile updated',
            'user'    => new CustomerResource($user->load('roles')),
        ]);
    }
}
