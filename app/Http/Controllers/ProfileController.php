<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile');
    }
    
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,jpg,png,gif|max:2048'
        ]);
        
        $user = Auth::user();
        
        if ($user->avatar && file_exists(public_path('avatars/' . $user->avatar))) {
            unlink(public_path('avatars/' . $user->avatar));
        }
        
        $file = $request->file('avatar');
        $filename = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        $avatarsPath = public_path('avatars');
        if (!file_exists($avatarsPath)) {
            mkdir($avatarsPath, 0755, true);
        }
        
        $file->move($avatarsPath, $filename);
        
        $user->avatar = $filename;
        $user->save();
        
        return response()->json([
            'success' => true,
            'avatar_url' => asset('avatars/' . $filename)
        ]);
    }
}
