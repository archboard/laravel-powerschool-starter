<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\FlashesAndRedirects;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UpdateTimezoneController extends Controller
{
    use FlashesAndRedirects;

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): \Illuminate\Http\RedirectResponse
    {
        /** @var \Illuminate\Support\Collection<string, string> $timezones */
        $timezones = timezones();

        $data = $request->validate([
            'timezone' => ['required', Rule::in($timezones->keys())],
        ]);

        $request->user()?->update($data);

        return $this->flashAndBack();
    }
}
