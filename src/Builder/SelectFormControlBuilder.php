<?php

declare(strict_types=1);

namespace Hustlahusky\Forms\Builder;

use Hustlahusky\Forms\FormControlType;

final class SelectFormControlBuilder extends FormControlBuilder
{
    use LabelTrait;
    use ReadonlyTrait;
    use RequiredTrait;
    use SizeTrait;
    use OptionTrait;
    use MultipleTrait;

    public static function make(string $name, ?string $label = null): self
    {
        $builder = new self();

        $builder->control->type = FormControlType::SELECT;
        $builder->setName($name);
        $builder->setLabel($label ?: $name);

        return $builder;
    }
}
