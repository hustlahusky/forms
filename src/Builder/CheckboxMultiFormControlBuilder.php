<?php

declare(strict_types=1);

namespace Hustlahusky\Forms\Builder;

use Hustlahusky\Forms\FormControlType;

final class CheckboxMultiFormControlBuilder extends FormControlBuilder
{
    use LabelTrait;
    use ReadonlyTrait;
    use RequiredTrait;
    use SizeTrait;
    use OptionTrait;

    public static function make(string $name, ?string $label = null): self
    {
        $builder = new self();

        $builder->control->type = FormControlType::CHECKBOX_MULTI;
        $builder->control->multiple = true;
        $builder->setName($name);
        $builder->setLabel($label ?: $name);

        return $builder;
    }
}
