<?php

declare(strict_types=1);

namespace Hustlahusky\Forms\Builder;

use Hustlahusky\Forms\FormControl;

trait ReadonlyTrait
{
    public function setReadonly(bool $readonly = true): self
    {
        \assert(isset($this->control) && $this->control instanceof FormControl);

        $this->control->readonly = $readonly;

        return $this;
    }
}
