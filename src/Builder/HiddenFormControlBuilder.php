<?php

declare(strict_types=1);

namespace Hustlahusky\Forms\Builder;

use Hustlahusky\Forms\FormControlSize;
use Hustlahusky\Forms\FormControlType;

final class HiddenFormControlBuilder extends FormControlBuilder
{
    public static function make(string $name, ?string $value = null): self
    {
        $builder = new self();

        $builder->control->type = FormControlType::HIDDEN;
        $builder->control->size = FormControlSize::empty();
        $builder->setName($name);
        $builder->setValue($value);

        return $builder;
    }
}
