<?php

namespace App\TableComponents\Columns;

use App\TableComponents\Traits\HasIcon;
use App\TableComponents\Traits\HasImage;
use App\TableComponents\Traits\HasLink;

class TextColumn extends Column
{
    use HasImage, HasIcon, HasLink;
}
