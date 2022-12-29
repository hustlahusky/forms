<?php

declare(strict_types=1);

namespace Hustlahusky\Forms\Render;

use Hustlahusky\Forms\Form;
use Hustlahusky\Forms\FormControl;
use Hustlahusky\Forms\FormControlRow;
use Hustlahusky\Forms\FormControlType;
use Yiisoft\Html\Html;
use Yiisoft\Html\NoEncode;
use Yiisoft\Html\Tag\Button;
use Yiisoft\Html\Tag\Input;
use Yiisoft\Html\Tag\Label;
use Yiisoft\Html\Tag\Legend;
use Yiisoft\Html\Tag\Option;
use Yiisoft\Html\Tag\Select;
use Yiisoft\Html\Tag\Span;
use Yiisoft\Html\Tag\Textarea;

final class BootstrapFormRenderer
{
    public const FORM_ATTRS = 'form_attributes';
    public const FIELDSET_ATTRS = 'fieldset_attributes';
    public const LEGEND_ATTRS = 'legend_attributes';
    public const ROW_ATTRS = 'row_attributes';
    public const CONTROL_PREFIX = 'control_prefix';

    private Form $form;

    /**
     * @var array<string,mixed>
     */
    private array $options;

    /**
     * @param array<string,mixed> $options
     */
    public function __construct(
        Form $form,
        array $options = []
    ) {
        $this->form = $form;
        $this->options = $options;
    }

    public function getNextControlId(): string
    {
        return $this->form->getNextControlId($this->options[self::CONTROL_PREFIX] ?? '');
    }

    /**
     * @param array<string,mixed> $attributes
     * @return \Generator<string|\Stringable>
     */
    public function startForm(array $attributes = []): \Generator
    {
        yield Html::openTag('form', self::unionAttributes([
            'action' => $this->form->action,
            'method' => $this->form->method,
            'enctype' => 'multipart/form-data',
        ], $attributes, $this->options[self::FORM_ATTRS] ?? []));
    }

    /**
     * @return \Generator<string|\Stringable>
     */
    public function renderControls(): \Generator
    {
        yield from $this->renderHiddenControls();
        yield from $this->renderControlsGroups();
    }

    /**
     * @param array<string,mixed> $attributes
     * @return \Generator<string|\Stringable>
     */
    public function submitButton(string $text = 'Submit', array $attributes = []): \Generator
    {
        yield Button::submit($text)
            ->addClass('btn', 'btn-primary')
            ->addAttributes($attributes);
    }

    /**
     * @return \Generator<string|\Stringable>
     */
    public function endForm(): \Generator
    {
        yield Html::closeTag('form');
    }

    /**
     * @return \Generator<string|\Stringable>
     */
    private function renderHiddenControls(): \Generator
    {
        foreach ($this->form->hiddenControls as $control) {
            yield from $this->renderControl($control);
        }
    }

    /**
     * @return \Generator<string|\Stringable>
     */
    private function renderControlsGroups(): \Generator
    {
        foreach ($this->form->controlGroups as $controlGroup) {
            yield Html::openTag('fieldset', $this->options[self::FIELDSET_ATTRS] ?? []);

            if ('' !== $controlGroup->title) {
                yield Legend::tag()
                    ->content($controlGroup->title)
                    ->unionAttributes($this->options[self::LEGEND_ATTRS] ?? []);
            }

            foreach ($controlGroup->rows as $row) {
                yield from $this->renderRow($row);
            }

            yield Html::closeTag('fieldset');
        }
    }

    /**
     * @return \Generator<string|\Stringable>
     */
    private function renderRow(FormControlRow $row): \Generator
    {
        if ([] === $row->controls) {
            return yield from [];
        }

        yield Html::openTag('div', self::unionAttributes([
            'class' => 'row',
        ], $this->options[self::ROW_ATTRS] ?? []));

        foreach ($row->controls as $control) {
            yield Html::openTag('div', [
                'class' => \sprintf('form-group col-%d', $control->size->size),
            ]);

            yield from $this->renderControl($control);

            yield Html::closeTag('div');
        }

        yield Html::closeTag('div');
    }

    /**
     * @return \Generator<string|\Stringable>
     */
    private function renderControl(FormControl $control): \Generator
    {
        switch ($control->type) {
            case FormControlType::HIDDEN:
                return yield Input::hidden($this->getControlName($control), $control->value);

            case FormControlType::CHECKBOX_MULTI:
                return yield from $this->renderControlCheckboxMulti($control);

            case FormControlType::SELECT:
                return yield from $this->renderControlSelect($control);

            case FormControlType::RADIO:
                return yield from $this->renderControlRadio($control);

            case FormControlType::CHECKBOX:
                return yield from $this->renderControlCheckbox($control);

            case FormControlType::TEXTAREA:
                return yield from $this->renderControlTextarea($control);

            default:
                $control->placeholder = '';
                return yield from $this->renderControlDefault($control);
        }
    }

    /**
     * @return \Generator<string|\Stringable>
     */
    private function renderControlDefault(FormControl $control): \Generator
    {
        $id = $this->getNextControlId();

        yield Label::tag()
            ->forId($id)
            ->content(...$this->getControlLabel($control));

        yield Input::tag()
            ->type($control->type)
            ->id($id)
            ->name($this->getControlName($control))
            ->value($control->value)
            ->addClass('form-control')
            ->readonly($control->readonly)
            ->required($control->required)
            ->attribute('placeholder', $control->placeholder)
            ->unionAttributes($control->extraAttributes);
    }

    /**
     * @return \Generator<string|\Stringable>
     */
    private function renderControlTextarea(FormControl $control): \Generator
    {
        $id = $this->getNextControlId();

        yield Label::tag()
            ->forId($id)
            ->content(...$this->getControlLabel($control));

        yield Textarea::tag()
            ->id($id)
            ->name($this->getControlName($control))
            ->value($control->value)
            ->addClass('form-control')
            ->attribute('placeholder', $control->placeholder)
            ->attribute('required', $control->required)
            ->unionAttributes($control->extraAttributes);
    }

    /**
     * @return \Generator<string|\Stringable>
     */
    private function renderControlSelect(FormControl $control): \Generator
    {
        $id = $this->getNextControlId();

        yield Label::tag()
            ->forId($id)
            ->content(...$this->getControlLabel($control));

        yield Select::tag()
            ->id($id)
            ->name($control->readonly ? null : $this->getControlName($control))
            ->values($control->multiple && \is_iterable($control->value) ? $control->value : [$control->value])
            ->multiple($control->multiple)
            ->disabled($control->readonly)
            ->required($control->readonly ? false : $control->required)
            ->promptOption(
                Option::tag()
                    ->value('')
                    ->content($control->placeholder)
                    ->disabled($control->required)
            )
            ->optionsData($control->options->toSelectArray())
            ->addClass('custom-select form-control select2 js-select2')
            ->unionAttributes($control->extraAttributes);

        if ($control->readonly) {
            foreach ($control->multiple && \is_iterable($control->value) ? $control->value : [$control->value] as $value) {
                yield Input::hidden($this->getControlName($control), $value);
            }
        }
    }

    /**
     * @return \Generator<string|\Stringable>
     */
    private function renderControlRadio(FormControl $control): \Generator
    {
        yield Label::tag()->content(...$this->getControlLabel($control));

        $controlValue = (string)$control->value;

        foreach ($control->options as $value => $label) {
            $value = (string)$value;

            yield Html::openTag('div', [
                'class' => 'custom-control custom-radio',
            ]);

            yield Input\Radio::tag()
                ->id($this->getNextControlId())
                ->name($control->readonly ? null : $this->getControlName($control))
                ->disabled($control->readonly)
                ->required($control->readonly ? false : $control->required)
                ->value($value)
                ->checked($value === $controlValue)
                ->addClass('custom-control-input')
                ->sideLabel($label, [
                    'class' => 'custom-control-label font-weight-normal',
                ])
                ->unionAttributes($control->extraAttributes);

            yield Html::closeTag('div');
        }

        if ($control->readonly) {
            yield Input::hidden($this->getControlName($control), $controlValue);
        }
    }

    /**
     * @return \Generator<string|\Stringable>
     */
    private function renderControlCheckbox(FormControl $control): \Generator
    {
        yield Html::openTag('div', [
            'class' => 'custom-control custom-checkbox',
        ]);

        $checked = \filter_var($control->value, \FILTER_VALIDATE_BOOLEAN);

        yield Input\Checkbox::tag()
            ->id($this->getNextControlId())
            ->name($control->readonly ? null : $this->getControlName($control))
            ->addClass('custom-control-input')
            ->disabled($control->readonly)
            ->required($control->readonly ? false : $control->required)
            ->sideLabel(\implode(\iterator_to_array($this->getControlLabel($control), false)), [
                'class' => 'custom-control-label',
            ])
            ->checked($checked)
            ->unionAttributes($control->extraAttributes);

        if ($control->readonly) {
            yield Input::hidden($this->getControlName($control), $checked ? 'on' : 'off');
        }

        yield Html::closeTag('div');
    }

    /**
     * @return \Generator<string|\Stringable>
     */
    private function renderControlCheckboxMulti(FormControl $control): \Generator
    {
        $checked = \array_flip($control->value);

        yield Label::tag()->content(...$this->getControlLabel($control));

        $count = \count($control->options);
        $splitColumns = 11 < $count && $control->size->isFull();
        $splitOn = (int)\ceil($count / 2);

        if ($splitColumns) {
            yield Html::openTag('div', [
                'class' => 'row',
            ]);

            yield Html::openTag('div', [
                'class' => 'col-6',
            ]);
        }

        $index = 0;
        foreach ($control->options as $value => $label) {
            $value = (string)$value;

            if ($splitColumns && $index === $splitOn) {
                yield Html::closeTag('div');
                yield Html::openTag('div', [
                    'class' => 'col-6',
                ]);
            }

            yield Html::openTag('div', [
                'class' => 'custom-control custom-checkbox',
            ]);

            yield Input\Checkbox::tag()
                ->id($this->getNextControlId())
                ->name($control->readonly ? null : $this->getControlName($control))
                ->value($value)
                ->addClass('custom-control-input')
                ->disabled($control->readonly)
                ->required($control->readonly ? false : $control->required)
                ->sideLabel($label, [
                    'class' => 'custom-control-label font-weight-normal',
                ])
                ->checked(isset($checked[$value]))
                ->unionAttributes($control->extraAttributes);

            if ($control->readonly && isset($checked[$value])) {
                yield Input::hidden($this->getControlName($control), $value);
            }

            yield Html::closeTag('div');

            $index++;
        }

        if ($splitColumns) {
            yield Html::closeTag('div');
            yield Html::closeTag('div');
        }
    }

    /**
     * @return \Generator<string|\Stringable>
     */
    private function getControlLabel(FormControl $control): \Generator
    {
        yield $control->label;

        if ($control->required) {
            yield NoEncode::string('&nbsp;');
            yield Span::tag()->content('*')->addClass('text-danger');
        }
    }

    private function getControlName(FormControl $control): string
    {
        return \sprintf(
            '%s%s%s%s%s',
            $this->form->name,
            '' === $this->form->name ? '' : '[',
            $control->name,
            '' === $this->form->name ? '' : ']',
            $control->multiple ? '[]' : '',
        );
    }

    /**
     * @param array<string,mixed> $attributes
     * @param array<string,mixed> ...$otherAttributes
     * @return array<string,mixed>
     */
    private static function unionAttributes(array $attributes, array ...$otherAttributes): array
    {
        $output = $attributes;

        foreach ($otherAttributes as $other) {
            if (isset($other['class'])) {
                Html::addCssClass($output, $other['class']);
            }

            $output += $other;
        }

        return $output;
    }
}
