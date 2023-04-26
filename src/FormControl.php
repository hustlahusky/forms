<?php

declare(strict_types=1);

namespace Hustlahusky\Forms;

final class FormControl
{
    public string $name;
    public string $type;
    public string $group;

    /**
     * @var mixed
     */
    public $value;

    public string $label;
    public string $placeholder;
    public FormControlSize $size;
    public bool $readonly = false;
    public bool $required = false;
    public FormControlOptions $options;
    public bool $multiple = false;
    public bool $mutable = true;

    /**
     * @var array<string,string>
     */
    public array $extraAttributes = [];

    public function __construct()
    {
        $this->options = FormControlOptions::new();
        $this->size = FormControlSize::new();
    }

    public function addOption(string $value, ?string $label = null, ?string $group = null): void
    {
        $this->options->addOption($value, $label, $group);
    }

    public function acceptUnknownValues(bool $acceptUnknownValues = true): void
    {
        $this->options->acceptUnknownValues($acceptUnknownValues);
    }
}
