<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show($Nama = "", $NPM = "", $Kelas = "")
    {
        $photoPath = 'profile_pictures/' . $NPM . '.jpg';
        $photoUrl = null;
        if (Storage::disk('public')->exists($photoPath)) {
            $photoUrl = asset('storage/' . $photoPath);
        }

        $data = [
            'Nama' => $Nama,
            'NPM' => $NPM,
            'Kelas' => $Kelas,
            'photoUrl' => $photoUrl,
        ];

        return view('profile', $data);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'NPM' => 'required|string',
            'Nama' => 'required|string',
            'Kelas' => 'required|string',
        ]);

        $photo = $request->file('photo');
        $NPM = $request->input('NPM');
        $Nama = $request->input('Nama');
        $Kelas = $request->input('Kelas');

        $photoPath = 'profile_pictures/' . $NPM . '.jpg';
        $photo->storeAs('public/profile_pictures', $NPM . '.jpg');

        return redirect()->route('profile', ['Nama' => $Nama, 'NPM' => $NPM, 'Kelas' => $Kelas]);
    }
}
