<?php

declare(strict_types=1);

namespace Hustlahusky\Forms\Handler;

use Hustlahusky\Forms\Form;
use Hustlahusky\Forms\FormControl;
use Hustlahusky\Forms\FormControlType;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;

final class FormHandler
{
    private Form $form;
    private FormControlVisitor $visitor;
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(Form $form, ?PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->form = $form;
        $this->visitor = new FormControlVisitor($this->form);
        $this->propertyAccessor = $propertyAccessor ?? PropertyAccess::createPropertyAccessor();
    }

    /**
     * @param array<string,mixed>|\ArrayAccess<string,mixed> $request
     * @return array<string,mixed>
     */
    public function handle($request): array
    {
        if ('' === $this->form->name) {
            $formData = $request;
        } else {
            $path = new PropertyPath(\sprintf('[%s]', $this->form->name));

            $formData = $this->propertyAccessor->isReadable($request, $path)
                ? $this->propertyAccessor->getValue($request, $path)
                : [];
        }

        $output = [];

        $this->visitor->visit(function (FormControl $control) use (&$output, $formData) {
            $output[$control->name] = $control->value;

            if (FormControlType::HIDDEN === $control->type) {
                return;
            }

            $path = new PropertyPath(\sprintf('[%s]', $control->name));

            if (!$this->propertyAccessor->isReadable($formData, $path)) {
                return;
            }

            $value = $this->propertyAccessor->getValue($formData, $path);

            switch ($control->type) {
                case FormControlType::CHECKBOX_MULTI:
                case FormControlType::SELECT:
                    $selected = [];
                    foreach ($control->multiple && \is_array($value) ? $value : [$value] as $v) {
                        $v = (string)$v;

                        if (!$control->options->has($v)) {
                            continue;
                        }

                        $selected[] = $v;
                    }

                    $control->value = $control->multiple ? $selected : ($selected[0] ?? null);
                    break;

                case FormControlType::RADIO:
                    $value = (string)$value;
                    $control->value = $control->options->has($value) ? $value : null;
                    break;

                case FormControlType::CHECKBOX:
                    $control->value = \filter_var($value, \FILTER_VALIDATE_BOOLEAN);
                    break;

                default:
                    $control->value = (string)$value;
                    break;
            }

            $output[$control->name] = $control->value;
        });

        return $output;
    }
}
