<?php

namespace App\Http\Controllers\Settings;

use App\FormComponents\Validate;
use App\Forms\UserForm;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FormsController extends Controller
{
    public function edit(UserForm $form): Response
    {
        return Inertia::render('settings/Forms', compact('form'));
    }

    public function update(#[Validate] UserForm $form): RedirectResponse
    {
        $user = request()->user();

        $user->fill($form->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return to_route('forms.edit');
    }
}
