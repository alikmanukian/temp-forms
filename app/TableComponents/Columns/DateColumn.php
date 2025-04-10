<?php

namespace App\TableComponents\Columns;

use App\TableComponents\Column;
use App\TableComponents\Enums\Variant;
use App\TableComponents\Traits\HasIcon;
use App\TableComponents\Traits\HasLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class DateColumn extends Column
{
    use HasLink;

    private ?string $format = null;
    private ?bool $translateFormat = null;

    private static string $defaultFormat = 'Y-m-d';
    private static bool $defaultTranslateFormat = false;

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function translate(): static
    {
        $this->translateFormat = true;
        return $this;
    }

    public static function setDefaultFormat(string $format): void
    {
        static::$defaultFormat = $format;
    }

    public static function setDefaultTranslate(bool $translate): void
    {
        static::$defaultTranslateFormat = $translate;
    }

    public function transform(Model $inputModel, Model $outputModel): void
    {
        parent::transform($inputModel, $outputModel);

        $value = $inputModel->{$this->name};

        // rewrite model value for the dates
        if ($value instanceof Carbon) {
            $outputModel->{$this->name} = ($this->translateFormat ?? static::$defaultTranslateFormat)
                ? $value->translatedFormat($this->format ?? static::$defaultFormat)
                : $value->format($this->format ?? static::$defaultFormat);
        }
    }
}
