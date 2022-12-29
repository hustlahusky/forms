<?php

declare(strict_types=1);

namespace Hustlahusky\Forms\Builder;

use Hustlahusky\Forms\FormControlType;

final class CheckboxFormControlBuilder extends FormControlBuilder
{
    use LabelTrait;
    use ReadonlyTrait;
    use RequiredTrait;
    use SizeTrait;

    public static function make(string $name, ?string $label = null): self
    {
        $builder = new self();

        $builder->control->type = FormControlType::CHECKBOX;
        $builder->control->value = false;
        $builder->setName($name);
        $builder->setLabel($label ?: $name);

        return $builder;
    }
}
