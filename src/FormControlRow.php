<?php

declare(strict_types=1);

namespace Hustlahusky\Forms;

final class FormControlRow
{
    /**
     * @var list<FormControl>
     */
    public array $controls = [];

    private FormControlSize $size;

    public function __construct()
    {
        $this->size = FormControlSize::empty();
    }

    public function canFitControl(FormControl $control): bool
    {
        return !$this->size->add($control->size)->isOverflowing();
    }

    public function addControl(FormControl $control): void
    {
        $newSize = $this->size->add($control->size);

        if ($newSize->isOverflowing()) {
            throw new \RuntimeException('Current row cannot fit given control.');
        }

        $this->size = $newSize;
        $this->controls[] = $control;
    }
}
