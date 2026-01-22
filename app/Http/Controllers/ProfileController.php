<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Intern;

class ProfileController extends Controller
{
    // Show profile page
    public function show()
    {
        $user = Auth::user();
        $intern = null;

        // Only fetch intern data if user is intern
        if ($user->role === 'intern') {
            $intern = Intern::where('user_id', $user->id)->first();
        }

        return view('profile.show', compact('user', 'intern'));
    }

    // Change password
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password changed successfully!');
    }
}
