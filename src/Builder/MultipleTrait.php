<?php

declare(strict_types=1);

namespace Hustlahusky\Forms\Builder;

use Hustlahusky\Forms\FormControl;

trait MultipleTrait
{
    public function setMultiple(bool $multiple = true): self
    {
        \assert(isset($this->control) && $this->control instanceof FormControl);

        $this->control->multiple = $multiple;

        return $this;
    }
}
