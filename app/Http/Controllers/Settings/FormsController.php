<?php

namespace App\Http\Controllers\Settings;

use App\Forms\UserForm;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FormsController extends Controller
{
    public function edit(UserForm $form): Response
    {
        return Inertia::render('settings/Forms', compact('form'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return to_route('forms.edit');
    }
}
