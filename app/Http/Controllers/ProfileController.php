<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return back()->with('status', 'Your profile has been updated.');
    }

    public function editPassword()
    {
        return view('profile.password');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $request->user()->forceFill([
            'password' => $data['password'],
        ])->save();

        return back()->with('status', 'Your password has been changed.');
    }
}
