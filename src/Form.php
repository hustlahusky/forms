<?php

declare(strict_types=1);

namespace Hustlahusky\Forms;

final class Form
{
    public string $name = '';
    public string $action = '';

    /**
     * @phpstan-var 'get'|'post'
     */
    public string $method = 'post';

    /**
     * @var list<FormControl>
     */
    public array $hiddenControls = [];

    /**
     * @var list<FormControlGroup>
     */
    public array $controlGroups = [];

    private int $lastId = 0;

    public function getNextControlId(string $prefix = ''): string
    {
        return \sprintf(
            '%s_%d',
            '' === $prefix ? \spl_object_hash($this) : $prefix,
            ++$this->lastId,
        );
    }

    public function addControl(FormControl $control): void
    {
        if (FormControlType::HIDDEN === $control->type) {
            $this->hiddenControls[] = $control;
            return;
        }

        $this->getActiveControlGroup()->addControl($control);
    }

    public function addControlGroup(string $title = ''): void
    {
        $this->controlGroups[] = new FormControlGroup($title);
    }

    public function getActiveControlGroup(): FormControlGroup
    {
        if ([] === $this->controlGroups) {
            return $this->controlGroups[] = new FormControlGroup();
        }

        return \end($this->controlGroups);
    }
}
