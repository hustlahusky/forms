<?php

declare(strict_types=1);

namespace Hustlahusky\Forms\Builder;

use Hustlahusky\Forms\FormControlType;

final class TextareaFormControlBuilder extends FormControlBuilder
{
    use LabelTrait;
    use ReadonlyTrait;
    use RequiredTrait;
    use SizeTrait;

    public static function make(string $name, ?string $label = null): self
    {
        $builder = new self();

        $builder->control->type = FormControlType::TEXTAREA;
        $builder->setName($name);
        $builder->setLabel($label ?: $name);

        return $builder;
    }

    public function setCols(int $cols): self
    {
        $this->control->extraAttributes['cols'] = (string)\max($cols, 1);

        return $this;
    }

    public function setRows(int $rows): self
    {
        $this->control->extraAttributes['rows'] = (string)\max($rows, 1);

        return $this;
    }
}
