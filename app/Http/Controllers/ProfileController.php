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
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $user = auth()->user();

        // ✅ Check if new password is same as current password
        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'New password cannot be the same as your current password.'
            ])->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

}
