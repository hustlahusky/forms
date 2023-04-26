<?php

declare(strict_types=1);

namespace Hustlahusky\Forms\Render;

use Hustlahusky\Forms\FormControl;
use Hustlahusky\Forms\FormControlRow;
use Hustlahusky\Forms\FormControlType;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Button;
use Yiisoft\Html\Tag\Input;
use Yiisoft\Html\Tag\Label;
use Yiisoft\Html\Tag\Legend;
use Yiisoft\Html\Tag\Option;
use Yiisoft\Html\Tag\Select;
use Yiisoft\Html\Tag\Textarea;

final class Bootstrap5FormRenderer extends AbstractFormRenderer
{
    /**
     * @param string|array<string,mixed> $classOrAttributes
     * @param string|array<string,mixed> $attributesOrClass
     * @return \Generator<string|\Stringable>
     */
    public function submitButton(
        string $text = 'Submit',
        $classOrAttributes = null,
        $attributesOrClass = null
    ): \Generator {
        if (\is_array($classOrAttributes)) {
            $attributes = $classOrAttributes;
            $class = $attributesOrClass;
        } elseif (\is_array($attributesOrClass)) {
            $attributes = $attributesOrClass;
            $class = $classOrAttributes;
        } else {
            $attributes = [];
            $class = \is_string($classOrAttributes) ? $classOrAttributes : $attributesOrClass;
        }

        if (!\is_string($class)) {
            $class = \trim((string)($attributes['class'] ?? ''));
        }

        if ('' === $class) {
            $class = 'btn-primary';
        }

        yield Button::submit($text)
            ->addClass('btn', $class)
            ->addAttributes($attributes);
    }

    /**
     * @return \Generator<string|\Stringable>
     */
    protected function renderControlsGroups(): \Generator
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
    protected function renderRow(FormControlRow $row): \Generator
    {
        if ([] === $row->controls) {
            return yield from [];
        }

        yield Html::openTag('div', self::unionAttributes([
            'class' => 'row',
        ], $this->options[self::ROW_ATTRS] ?? []));

        foreach ($row->controls as $control) {
            yield Html::openTag('div', [
                'class' => \sprintf('col-xs-12 col-md-%d', $control->size->size),
            ]);

            yield from $this->renderControl($control);

            yield Html::closeTag('div');
        }

        yield Html::closeTag('div');
    }

    /**
     * @return \Generator<string|\Stringable>
     */
    protected function renderControl(FormControl $control): \Generator
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
            ->addClass('form-label')
            ->content(...$this->getControlLabel($control));

        yield Input::tag()
            ->type($control->type)
            ->id($id)
            ->name($this->getControlName($control))
            ->value($control->value)
            ->addClass('form-control', $this->getControlExtraClass($control))
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
            ->addClass('form-label')
            ->content(...$this->getControlLabel($control));

        yield Textarea::tag()
            ->id($id)
            ->name($this->getControlName($control))
            ->value($control->value)
            ->addClass('form-control', $this->getControlExtraClass($control))
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

        $values = \array_map(
            static fn ($v) => $v ?? '',
            $control->multiple && \is_iterable($control->value) ? $control->value : [$control->value]
        );

        yield Label::tag()
            ->forId($id)
            ->addClass('form-label')
            ->content(...$this->getControlLabel($control));

        yield Select::tag()
            ->id($id)
            ->name($control->readonly ? null : $this->getControlName($control))
            ->values($values)
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
            ->addClass('form-select', $this->getControlExtraClass($control))
            ->unionAttributes($control->extraAttributes);

        if ($control->readonly) {
            foreach ($values as $value) {
                yield Input::hidden($this->getControlName($control), $value);
            }
        }
    }

    /**
     * @return \Generator<string|\Stringable>
     */
    private function renderControlRadio(FormControl $control): \Generator
    {
        yield Label::tag()
            ->addClass('form-label')
            ->content(...$this->getControlLabel($control));

        $controlValue = (string)$control->value;

        foreach ($control->options as $value => $label) {
            $value = (string)$value;

            yield Html::openTag('div', [
                'class' => 'form-check',
            ]);

            yield Input\Radio::tag()
                ->id($this->getNextControlId())
                ->name($control->readonly ? null : $this->getControlName($control))
                ->disabled($control->readonly)
                ->required($control->readonly ? false : $control->required)
                ->value($value)
                ->checked($value === $controlValue)
                ->addClass('form-check-input', $this->getControlExtraClass($control))
                ->sideLabel($label, [
                    'class' => 'form-check-label',
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
            'class' => 'form-check',
        ]);

        $checked = \filter_var($control->value, \FILTER_VALIDATE_BOOLEAN);

        yield Input\Checkbox::tag()
            ->id($this->getNextControlId())
            ->name($control->readonly ? null : $this->getControlName($control))
            ->addClass('form-check-input', $this->getControlExtraClass($control))
            ->disabled($control->readonly)
            ->required($control->readonly ? false : $control->required)
            ->sideLabel(\implode(\iterator_to_array($this->getControlLabel($control), false)), [
                'class' => 'form-check-label',
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

        yield Label::tag()
            ->addClass('form-label')
            ->content(...$this->getControlLabel($control));

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
                'class' => 'form-check',
            ]);

            yield Input\Checkbox::tag()
                ->id($this->getNextControlId())
                ->name($control->readonly ? null : $this->getControlName($control))
                ->value($value)
                ->addClass('form-check-input', $this->getControlExtraClass($control))
                ->disabled($control->readonly)
                ->required($control->readonly ? false : $control->required)
                ->sideLabel($label, [
                    'class' => 'form-check-label',
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
}
