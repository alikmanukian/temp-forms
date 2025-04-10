<?php

namespace App\TableComponents\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * @method setColumnParamToModel(Model $model, string $paramName, mixed $paramValue)
 */
trait HasLink
{
    protected mixed $linkTo = null;

    public function linkTo(string|callable $url, array $params = []): static
    {
        if (is_string($url)) {
            $this->linkTo = static fn () => array_merge(['href' => $url], $params);
        }

        if (is_callable($url)) {
            $this->linkTo = $url;
        }

        return $this;
    }

    protected function setLink(Model $inputModel, Model $outputModel): void
    {
        if (! $this->linkTo) {
            return;
        }

        $link = call_user_func($this->linkTo, $inputModel);

        if (is_string($link)) {
            $link = ['href' => $link];
        }

        $this->setColumnParamToModel($outputModel, 'link', $link);
    }
}
