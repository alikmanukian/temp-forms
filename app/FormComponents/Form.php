<?php

namespace App\FormComponents;

use App\FormComponents\Fields\Button;
use Illuminate\Http\Request;
use JsonSerializable;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use RuntimeException;

abstract class Form implements JsonSerializable
{
    protected ?string $actionRoute = null;

    protected string $method = 'post';

    protected array $validated = [];

    /**
     * @throws ReflectionException
     */
    public function __construct(Request $request)
    {
        $route = $request->route();
        $controller = $route->getController();
        $method = $route->getActionMethod();

        $reflection = new ReflectionMethod($controller, $method);
        foreach ($reflection->getParameters() as $parameter) {
            if ($this->hasValidateAttribute($parameter)) {
                $this->validate();
            }
        }
    }

    abstract public function fields(): array;

    public function jsonSerialize(): array
    {
        if (! $this->actionRoute) {
            throw new RuntimeException('Action route is required');
        }

        return [
            'url' => route($this->actionRoute),
            'method' => $this->method,
            'fields' => collect($this->fields())
                ->map(function (Field|Button $field) {
                    return $field->jsonSerialize();
                })->toArray()
        ];
    }

    public function validate(): void
    {
        $rules = collect($this->fields())
            ->map(function (Field|Button $field) {
                if ($field instanceof Button) {
                    return null;
                }

                $rule = $field->getRule();

                if (! $rule) {
                    return null;
                }

                return [$field->getName() => $rule];
            })
            ->filter()
            ->collapse()
            ->toArray();

        $this->validated = request()->validate($rules);
    }

    public function validated(): array
    {
        return $this->validated;
    }

    private function hasValidateAttribute(ReflectionParameter $parameter): bool
    {
        $attributes = $parameter->getAttributes(Validate::class);
        return !empty($attributes);
    }
}
