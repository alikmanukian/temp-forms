<?php

namespace App\TableComponents\Columns;

use App\TableComponents\Column;

class BooleanColumn extends Column
{
    public function trueLabel(string $label): static
    {
        return $this;
    }

    public function falseLabel(string $label): static
    {
        return $this;
    }
}
