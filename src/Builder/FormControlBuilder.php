<?php

declare(strict_types=1);

namespace Hustlahusky\Forms\Builder;

use Hustlahusky\Forms\FormControl;

abstract class FormControlBuilder
{
    protected FormControl $control;

    public function __construct()
    {
        $this->control = new FormControl();
    }

    public function build(): FormControl
    {
        return $this->control;
    }

    public function setName(string $name): self
    {
        $this->control->name = $name;

        return $this;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): self
    {
        $this->control->value = $value;

        return $this;
    }

    public function setAttribute(string $attribute, string $value): self
    {
        $this->control->extraAttributes[$attribute] = $value;

        return $this;
    }

    /**
     * @param array<string,string> $attributes
     */
    public function setExtraAttributes(array $attributes): self
    {
        foreach ($attributes as $k => $v) {
            $this->setAttribute((string)$k, (string)$v);
        }

        return $this;
    }

    public static function default(string $type, string $name, ?string $label = null): DefaultFormControlBuilder
    {
        return DefaultFormControlBuilder::make($type, $name, $label);
    }

    public static function text(string $name, ?string $label = null): DefaultFormControlBuilder
    {
        return DefaultFormControlBuilder::text($name, $label);
    }

    public static function textarea(string $name, ?string $label = null): TextareaFormControlBuilder
    {
        return TextareaFormControlBuilder::make($name, $label);
    }

    public static function number(string $name, ?string $label = null): DefaultFormControlBuilder
    {
        return DefaultFormControlBuilder::number($name, $label);
    }

    public static function email(string $name, ?string $label = null): DefaultFormControlBuilder
    {
        return DefaultFormControlBuilder::email($name, $label);
    }

    public static function phone(string $name, ?string $label = null): DefaultFormControlBuilder
    {
        return DefaultFormControlBuilder::phone($name, $label);
    }

    public static function radio(string $name, ?string $label = null): RadioFormControlBuilder
    {
        return RadioFormControlBuilder::make($name, $label);
    }

    public static function checkbox(string $name, ?string $label = null): CheckboxFormControlBuilder
    {
        return CheckboxFormControlBuilder::make($name, $label);
    }

    public static function checkboxMulti(string $name, ?string $label = null): CheckboxMultiFormControlBuilder
    {
        return CheckboxMultiFormControlBuilder::make($name, $label);
    }

    public static function select(string $name, ?string $label = null): SelectFormControlBuilder
    {
        return SelectFormControlBuilder::make($name, $label);
    }

    public static function hidden(string $name, ?string $value = null, bool $mutable = false): HiddenFormControlBuilder
    {
        return HiddenFormControlBuilder::make($name, $value, $mutable);
    }
}
