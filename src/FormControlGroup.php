<?php

declare(strict_types=1);

namespace Hustlahusky\Forms;

final class FormControlGroup
{
    public string $title;

    /**
     * @var list<FormControlRow>
     */
    public array $rows = [];

    public function __construct(string $title = '')
    {
        $this->title = $title;
    }

    public function addControl(FormControl $control): void
    {
        $lastRow = $this->getLastRow();
        if ($lastRow->canFitControl($control)) {
            $lastRow->addControl($control);
            return;
        }

        $this->addRow()->addControl($control);
    }

    private function addRow(): FormControlRow
    {
        return $this->rows[] = new FormControlRow();
    }

    private function getLastRow(): FormControlRow
    {
        if ([] === $this->rows) {
            return $this->rows[] = new FormControlRow();
        }

        return \end($this->rows);
    }
}
