<?php

namespace App\FormComponents;

use App\FormComponents\Fields\Button;
use Illuminate\Http\Request;
use Inertia\Inertia;
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

    abstract public function fields(): array;

    /**
     * Try to validate FOrm if the Form class is used in controller action parameter with Validate attribute
     * @throws ReflectionException
     */
    public function __construct(Request $request)
    {
        Inertia::share('form_defaults', config('forms'));

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

    /**
     * This function used when form sending in response data
     */
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

    /**
     * Get the validated data
     */
    public function validated(): array
    {
        return $this->validated;
    }

    /**
     * Validate the data sent like FormRequest
     */
    private function validate(): void
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

    /**
     * Check if Form object used for validation or not
     */
    private function hasValidateAttribute(ReflectionParameter $parameter): bool
    {
        $attributes = $parameter->getAttributes(Validate::class);
        return !empty($attributes);
    }
}
