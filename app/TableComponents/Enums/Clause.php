<?php

namespace App\TableComponents\Enums;

use Illuminate\Support\Str;

enum Clause: string
{
    case Equals = 'equals'; // ''
    case DoesNotEqual = 'does_not_equal'; // '!';
    case Contains = 'contains'; //'~';
    case DoesNotContain = 'does_not_contain'; //'!~';
    case StartsWith = 'starts_with'; //'^';
    case DoesNotStartWith = 'does_not_start_with'; //'!^';
    case EndsWith = 'ends_with'; //'$';
    case DoesNotEndWith = 'does_not_end_with'; //'!$';
    case IsIn = 'is_in'; // ''
    case IsNotIn = 'is_not_in'; // '!'

    public function toArray(): array
    {
        return [
            'name' => $this->name(),
            'symbol' => $this->symbol(),
            'searchSymbol' => $this->searchSymbol(),
            'value' => $this->value,
        ];
    }

    public static function findBySearchSymbol(string $symbol, ?string $value = null): ?self
    {
        return collect(self::cases())
            ->first(fn(Clause $clause) => $clause->searchSymbol() === $symbol);
    }

    private function name(): string
    {
        return match($this) {
            self::Contains => 'Contains',
            self::DoesNotContain => 'Does Not Contain',
            self::StartsWith => 'Starts With',
            self::DoesNotStartWith => 'Does Not Start With',
            self::EndsWith => 'Ends With',
            self::DoesNotEndWith => 'Does Not End With',
            self::Equals => 'Equals',
            self::DoesNotEqual => 'Does Not Equal',
            self::IsIn => 'Is In',
            self::IsNotIn => 'Is Not In',
        };
    }

    private function symbol(): string
    {
        return match($this) {
            self::Contains => 'Asterisk',
            self::DoesNotContain => '!Asterisk',
            self::StartsWith => 'ChevronUp',
            self::DoesNotStartWith => '!ChevronUp',
            self::EndsWith => 'DollarSign',
            self::DoesNotEndWith => '!DollarSign',
            self::Equals => 'Equal',
            self::DoesNotEqual=> 'EqualNot',
            self::IsIn => 'EqualApproximately',
            self::IsNotIn => '!EqualApproximately',
        };
    }

    private function searchSymbol(): string
    {
        return match($this) {
            self::Contains => '*',
            self::DoesNotContain => '!*',
            self::StartsWith => '^',
            self::DoesNotStartWith => '!^',
            self::EndsWith => '$',
            self::DoesNotEndWith => '!$',
            self::Equals => '',
            self::DoesNotEqual => '!',
            self::IsIn => '~',
            self::IsNotIn => '!~',
        };
    }

    public static function sortByLength(array $clauses): array
    {
        return collect(self::cases())
            ->map(fn(Clause $clause) => $clause->searchSymbol())
            ->sortByDesc(fn(string $symbol) => Str::length($symbol))
            ->values()
            ->toArray();
    }
}
