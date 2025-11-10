<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(
        private ImageService $imageService
    ) {}

    public function showProfile()
    {
        $user = Auth::user();
        $user->load('reviews.reviewer', 'reviews.product');
        
        return view('profile.show', [
            'user' => $user,
        ]);
    }

    public function editProfile()
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $user = Auth::user();
            $data = $request->validated();

            if (isset($data['phone']) && $data['phone']) {
                $existingUser = \App\Models\User::where('phone', $data['phone'])
                    ->where('id', '!=', $user->id)
                    ->first();
                
                if ($existingUser) {
                    return back()
                        ->withErrors(['phone' => 'Nomor HP ini sudah digunakan oleh akun lain.'])
                        ->withInput();
                }
            }

            if ($request->hasFile('avatar')) {
                $uploadResult = $this->imageService->uploadAvatar($request->file('avatar'));
                $data['avatar'] = $uploadResult['cloudinary_url'];
            }

            $user->update($data);

            return redirect()->route('profile.show')->with('success', 'Profile berhasil diperbarui!');
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Profile update error: ' . $e->getMessage());
            
            if (str_contains($e->getMessage(), 'unique constraint') || 
                str_contains($e->getMessage(), 'duplicate key') ||
                str_contains($e->getMessage(), 'users_phone_unique')) {
                return back()
                    ->withErrors(['phone' => 'Nomor HP ini sudah digunakan oleh akun lain.'])
                    ->withInput();
            }
            
            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui profile. Silakan coba lagi.'])
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Profile update error: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui profile. Silakan coba lagi.'])
                ->withInput();
        }
    }
}
