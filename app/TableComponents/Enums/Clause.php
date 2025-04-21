<?php

namespace App\TableComponents\Enums;

enum Clause: string
{
    case Equals = '';
    case DoesNotEqual = '!';
    case Contains = '~';
    case DoesNotContain = '!~';
    case StartsWith = '^';
    case DoesNotStartWith = '!^';
    case EndsWith = '$';
    case DoesNotEndWith = '!$';
}
