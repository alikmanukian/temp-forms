<?php

namespace App\Forms;

use App\FormComponents\Field;
use App\FormComponents\Fields\TextField;
use App\FormComponents\Form;

class UserForm extends Form
{
    protected ?string $actionRoute = 'forms.update';

    /**
     * @return list<Field>
     */
    public function fields(): array
    {
        return [
            TextField::make('name')
                ->required()
                ->precognitive(),

            TextField::make('email')
                ->email()
                ->required()
                ->precognitive()
                ->rule('email'),
        ];
    }
}
