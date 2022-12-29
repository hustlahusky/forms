<?php

declare(strict_types=1);

namespace Hustlahusky\Forms\Builder;

use Hustlahusky\Forms\FormControlType;

final class RadioFormControlBuilder extends FormControlBuilder
{
    use LabelTrait;
    use ReadonlyTrait;
    use RequiredTrait;
    use SizeTrait;
    use OptionTrait;

    public static function make(string $name, ?string $label = null): self
    {
        $builder = new self();

        $builder->control->type = FormControlType::RADIO;
        $builder->setName($name);
        $builder->setLabel($label ?: $name);

        return $builder;
    }
}
