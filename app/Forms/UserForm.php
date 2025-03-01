<?php

namespace App\Forms;

use App\FormComponents\Field;
use App\FormComponents\Fields\Submit;
use App\FormComponents\Fields\TextField;
use App\FormComponents\Form;
use App\Models\User;
use Illuminate\Validation\Rule;

class UserForm extends Form
{
    protected ?string $actionRoute = 'forms.update';

    protected string $method = 'patch';

    /**
     * @return list<Field>
     */
    public function fields(): array
    {
        return [
            TextField::make('name')
                ->required()
                ->precognitive()
                ->rule(['required', 'string', 'max:255'])
                ->value(auth()->user()?->name),

            TextField::make('email')
                ->email()
                ->required()
                ->precognitive()
                ->rule([
                    'required',
                    'string',
                    'lowercase',
                    'email',
                    'max:255',
                    Rule::unique(User::class)->ignore(auth()->id()),
                ])
                ->value(auth()->user()?->email),

            Submit::make('Save')
        ];
    }
}
