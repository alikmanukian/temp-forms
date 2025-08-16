<?php

namespace App\TableComponents\Enums;

use Illuminate\Support\Str;

enum Clause: string
{
    case Equals = 'equals'; // ''
    case DoesNotEqual = 'does_not_equal'; // '!';
    case Contains = 'contains'; // '~';
    case DoesNotContain = 'does_not_contain'; // '!~';
    case StartsWith = 'starts_with'; // '^';
    case DoesNotStartWith = 'does_not_start_with'; // '!^';
    case EndsWith = 'ends_with'; // '$';
    case DoesNotEndWith = 'does_not_end_with'; // '!$';
    case IsIn = 'is_in'; // ''
    case IsNotIn = 'is_not_in'; // '!'
    case IsSet = 'is_set'; // '!null'
    case IsNotSet = 'is_not_set'; // 'null'
    case IsTrue = 'is_true'; // 'true'
    case IsFalse = 'is_false'; // 'false'
    case After = 'after'; // '>'
    case Before = 'before'; // '<'
    case Between = 'between'; // '<>'

    public function toArray(): array
    {
        return [
            'name' => $this->name(),
            'searchSymbol' => $this->searchSymbol(),
            'value' => $this->value,
            'prefix' => $this->prefix(),
        ];
    }

    public static function findBySearchSymbol(string $symbol, ?string $value = null): ?self
    {
        return collect(self::cases())
            ->first(fn (Clause $clause) => $clause->searchSymbol() === $symbol);
    }

    private function name(): string
    {
        return match ($this) {
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
            self::IsSet => 'Is Set',
            self::IsNotSet => 'Is Not Set',
            self::IsTrue => 'Is True',
            self::IsFalse => 'Is False',
            self::After => 'After',
            self::Before => 'Before',
            self::Between => 'Between',
        };
    }

    private function prefix(): string
    {
        return match ($this) {
            self::Contains => '',
            self::DoesNotContain => 'Not Contains',
            self::StartsWith => 'Starts with',
            self::DoesNotStartWith => 'Not Starts with',
            self::EndsWith => 'Ends with',
            self::DoesNotEndWith => 'Not ends with',
            self::Equals => '',
            self::DoesNotEqual => 'Not',
            self::IsIn => '',
            self::IsNotIn => 'Not',
            self::IsSet => 'Is Set',
            self::IsNotSet => 'Is Not Set',
            self::IsTrue => 'Is True',
            self::IsFalse => 'Is False',
            self::After => 'After',
            self::Before => 'Before',
            self::Between => 'Between',
        };
    }

    private function searchSymbol(): string
    {
        return match ($this) {
            self::Contains => 'contains',
            self::DoesNotContain => 'not.contains',
            self::StartsWith => 'starts',
            self::DoesNotStartWith => 'not.starts',
            self::EndsWith => 'ends',
            self::DoesNotEndWith => 'not.ends',
            self::Equals => '',
            self::DoesNotEqual => 'not',
            self::IsIn => 'in',
            self::IsNotIn => 'not.in',
            self::IsSet => 'not.null',
            self::IsNotSet => 'null',
            self::IsTrue => 'true',
            self::IsFalse => 'false',
            self::After => 'after',
            self::Before => 'before',
            self::Between => 'between',
        };
    }

    public static function sortByLength(array $clauses): array
    {
        static $sortedSymbols = null;
        
        // Cache the sorted symbols for better performance
        if ($sortedSymbols === null) {
            $symbols = [];
            foreach (self::cases() as $clause) {
                $symbols[] = $clause->searchSymbol();
            }
            
            usort($symbols, fn($a, $b) => strlen($b) - strlen($a));
            $sortedSymbols = $symbols;
        }

        return $sortedSymbols;
    }
}
