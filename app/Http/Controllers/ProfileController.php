<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        if ($user->isSiswa()) {
            // Optimasi memory & cegah N+1 query: Eager load classroom hanya kolom id dan name
            $user->loadMissing(['classroom:id,name']);
            $subjects = collect();
            $mySubjectIds = [];
        } elseif ($user->isGuru()) {
            // Optimasi memory: Pilih kolom id dan name saja
            $subjects = \App\Models\Subject::select(['id', 'name'])->orderBy('name')->get();
            $mySubjectIds = $user->subjects()->pluck('subjects.id')->toArray();
        } else {
            $subjects = collect();
            $mySubjectIds = [];
        }

        return view('profile.edit', [
            'user' => $user,
            'subjects' => $subjects,
            'mySubjectIds' => $mySubjectIds,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        if ($request->user()->isSiswa()) {
            abort(403, 'Siswa tidak diizinkan mengubah informasi profil.');
        }

        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if ($request->user()->isSiswa()) {
            abort(403, 'Siswa tidak diizinkan menghapus akun.');
        }

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
