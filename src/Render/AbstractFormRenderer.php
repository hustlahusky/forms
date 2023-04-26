<?php

declare(strict_types=1);

namespace Hustlahusky\Forms\Render;

use Hustlahusky\Forms\Form;
use Hustlahusky\Forms\FormControl;
use Yiisoft\Html\Html;
use Yiisoft\Html\NoEncode;
use Yiisoft\Html\Tag\Span;

abstract class AbstractFormRenderer
{
    public const FORM_ATTRS = 'form_attributes';
    public const FIELDSET_ATTRS = 'fieldset_attributes';
    public const LEGEND_ATTRS = 'legend_attributes';
    public const ROW_ATTRS = 'row_attributes';
    public const CONTROL_PREFIX = 'control_prefix';

    protected Form $form;

    /**
     * @var array<string,mixed>
     */
    protected array $options;

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
     * @param string|array<string,mixed> $classOrAttributes
     * @param string|array<string,mixed> $attributesOrClass
     * @return \Generator<string|\Stringable>
     */
    abstract public function submitButton(
        string $text = 'Submit',
        $classOrAttributes = null,
        $attributesOrClass = null
    ): \Generator;

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
    abstract protected function renderControlsGroups(): \Generator;

    /**
     * @return \Generator<string|\Stringable>
     */
    abstract protected function renderControl(FormControl $control): \Generator;

    /**
     * @return \Generator<string|\Stringable>
     */
    protected function renderHiddenControls(): \Generator
    {
        foreach ($this->form->hiddenControls as $control) {
            yield from $this->renderControl($control);
        }
    }

    /**
     * @return \Generator<string|\Stringable>
     */
    protected function getControlLabel(FormControl $control): \Generator
    {
        yield $control->label;

        if ($control->required) {
            yield NoEncode::string('&nbsp;');
            yield Span::tag()->content('*')->addClass('text-danger');
        }
    }

    protected function getControlName(FormControl $control): string
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

    protected function getControlExtraClass(FormControl $control): ?string
    {
        $class = \trim((string)($control->extraAttributes['class'] ?? ''));
        unset($control->extraAttributes['class']);
        return '' === $class ? null : $class;
    }

    /**
     * @param array<string,mixed> $attributes
     * @param array<string,mixed> ...$otherAttributes
     * @return array<string,mixed>
     */
    protected static function unionAttributes(array $attributes, array ...$otherAttributes): array
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
