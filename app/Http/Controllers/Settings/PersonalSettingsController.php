<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\FlashesAndRedirects;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class PersonalSettingsController extends Controller
{
    use FlashesAndRedirects;

    /**
     * Show the settings page
     *
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function edit()
    {
        $title = __('Personal settings');

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
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email'],
        ]);

        $request->user()
            ->update($data);

        return $this->flashAndBack();
    }
}
