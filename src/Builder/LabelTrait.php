<?php

declare(strict_types=1);

namespace Hustlahusky\Forms\Builder;

use Hustlahusky\Forms\FormControl;

trait LabelTrait
{
    public function setLabel(string $label): self
    {
        \assert(isset($this->control) && $this->control instanceof FormControl);

        $this->control->label = $label;

        if (!isset($this->control->placeholder)) {
            $this->setPlaceholder($label);
        }

        return $this;
    }

    public function setPlaceholder(string $placeholder): self
    {
        \assert(isset($this->control) && $this->control instanceof FormControl);

        $this->control->placeholder = $placeholder;

        if (!isset($this->control->label)) {
            $this->setLabel($placeholder);
        }

        return $this;
    }
}
