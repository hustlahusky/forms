<?php

declare(strict_types=1);

namespace Hustlahusky\Forms\Builder;

use Hustlahusky\Forms\FormControl;

trait RequiredTrait
{
    public function setRequired(bool $required = true): self
    {
        \assert(isset($this->control) && $this->control instanceof FormControl);

        $this->control->required = $required;

        return $this;
    }
}
