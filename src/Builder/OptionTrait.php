<?php

declare(strict_types=1);

namespace Hustlahusky\Forms\Builder;

use Hustlahusky\Forms\FormControl;

trait OptionTrait
{
    public function addOption(string $value, ?string $label = null, ?string $group = null): self
    {
        \assert(isset($this->control) && $this->control instanceof FormControl);

        $this->control->addOption($value, $label, $group);

        return $this;
    }

    public function acceptUnknownValues(bool $acceptUnknownValues = true): self
    {
        \assert(isset($this->control) && $this->control instanceof FormControl);

        $this->control->acceptUnknownValues($acceptUnknownValues);

        return $this;
    }
}
