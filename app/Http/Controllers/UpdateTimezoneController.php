<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\FlashesAndRedirects;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class UpdateTimezoneController extends Controller
{
    use FlashesAndRedirects;

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        /** @var Collection<string, string> $timezones */
        $timezones = timezones();

        $data = $request->validate([
            'timezone' => ['required', Rule::in($timezones->keys())],
        ]);

        $request->user()?->update($data);

        return $this->flashAndBack();
    }
}
