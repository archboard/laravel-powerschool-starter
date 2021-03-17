<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class PersonalSettingsController extends Controller
{
    /**
     * Show the settings page
     *
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function index()
    {
        $title = __('Personal Settings');

        return inertia('settings/Personal', [
            'title' => $title,
        ])->withViewData(compact('title'));
    }

    /**
     * Updates a users name, email, and password
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'nullable|string|confirmed|min:8',
        ]);

        /** @var User $user */
        $user = $request->user();

        $user->fill(Arr::except($data, 'password'));

        if ($data['password']) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        session()->flash('success', __('Settings updated successfully.'));

        return back();
    }
}
