<?php

declare(strict_types=1);

namespace Hustlahusky\Forms\Builder;

use Hustlahusky\Forms\FormControl;
use Hustlahusky\Forms\FormControlSize;

trait SizeTrait
{
    public function setSize(FormControlSize $size): self
    {
        \assert(isset($this->control) && $this->control instanceof FormControl);

        $this->control->size = $size;

        return $this;
    }
}
