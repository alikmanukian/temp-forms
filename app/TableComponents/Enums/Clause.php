<?php

namespace App\TableComponents\Enums;

enum Clause: string
{
    case Equals = 'equals';
    case Contains = 'contains';
    case StartsWith = 'startsWith';
}
