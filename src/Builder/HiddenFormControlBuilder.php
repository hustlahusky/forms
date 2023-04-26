<?php

declare(strict_types=1);

namespace Hustlahusky\Forms\Builder;

use Hustlahusky\Forms\FormControlSize;
use Hustlahusky\Forms\FormControlType;

final class HiddenFormControlBuilder extends FormControlBuilder
{
    public static function make(string $name, ?string $value = null, bool $mutable = false): self
    {
        $builder = new self();

        $builder->control->type = FormControlType::HIDDEN;
        $builder->control->size = FormControlSize::empty();
        $builder->control->mutable = $mutable;
        $builder->setName($name);
        $builder->setValue($value);

        return $builder;
    }
}
