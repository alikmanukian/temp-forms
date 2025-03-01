<?php

namespace App\FormComponents;

use Attribute;

/**
 * This Validate attribute is used to mark a Form object for validation purposes.
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class Validate
{
    // This class is just a marker attribute.
}
