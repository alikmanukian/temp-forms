<?php

namespace App\TableComponents\Columns;

use App\TableComponents\Enums\ImageSize;
use App\TableComponents\Image;
use App\TableComponents\Traits\HasImage;
use Illuminate\Database\Eloquent\Model;

class ImageColumn extends Column
{
    use HasImage;

    public static function make(...$arguments): static
    {
        $object = parent::make(...$arguments);

        $object->image($arguments[0], function (Model $model, Image $image) use ($object) {
            $image->size(ImageSize::ExtraLarge)
                ->class('rounded-md');
        });

        return $object;
    }
}
