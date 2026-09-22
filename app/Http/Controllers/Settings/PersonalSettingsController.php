<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\FlashesAndRedirects;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class PersonalSettingsController extends Controller
{
    use FlashesAndRedirects;

    /**
     * Show the settings page
     */
    public function edit(Request $request): Response
    {
        $title = __('Personal settings');
        $user = $request->user();

        return inertia('settings/Personal', [
            'title' => $title,
            'hasPassword' => $user && (bool) $user->password,
        ])->withViewData(compact('title'));
    }

    /**
     * Updates a users name, email, and password
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email'],
            'timezone' => ['required', 'timezone'],
        ]);

        $request->user()?->update($data);

        return $this->flashAndBack();
    }
}
