<?php

declare(strict_types=1);

namespace Hustlahusky\Forms\Builder;

use Hustlahusky\Forms\FormControlType;

final class DefaultFormControlBuilder extends FormControlBuilder
{
    use LabelTrait;
    use ReadonlyTrait;
    use RequiredTrait;
    use SizeTrait;

    public static function make(string $type, string $name, ?string $label = null): self
    {
        if (
            FormControlType::TEXT !== $type &&
            FormControlType::NUMBER !== $type &&
            FormControlType::EMAIL !== $type &&
            FormControlType::TEL !== $type
        ) {
            $type = FormControlType::TEXT;
        }

        $builder = new self();

        $builder->control->type = $type;
        $builder->setName($name);
        $builder->setLabel($label ?: $name);

        return $builder;
    }

    public static function text(string $name, ?string $label = null): self
    {
        return self::make(FormControlType::TEXT, $name, $label);
    }

    public static function number(string $name, ?string $label = null): self
    {
        return self::make(FormControlType::NUMBER, $name, $label);
    }

    public static function email(string $name, ?string $label = null): self
    {
        return self::make(FormControlType::EMAIL, $name, $label);
    }

    public static function phone(string $name, ?string $label = null): self
    {
        return self::make(FormControlType::TEL, $name, $label);
    }
}
