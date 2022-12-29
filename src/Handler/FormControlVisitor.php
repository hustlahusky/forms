<?php

declare(strict_types=1);

namespace Hustlahusky\Forms\Handler;

use Hustlahusky\Forms\Form;
use Hustlahusky\Forms\FormControl;

final class FormControlVisitor
{
    private Form $form;

    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    public static function new(Form $form): self
    {
        return new self($form);
    }

    /**
     * @param callable(FormControl):void $visitor
     * @return void
     */
    public function visit(callable $visitor): void
    {
        foreach ($this->form->hiddenControls as $control) {
            $visitor($control);
        }

        foreach ($this->form->controlGroups as $controlGroup) {
            foreach ($controlGroup->rows as $controlRow) {
                foreach ($controlRow->controls as $control) {
                    $visitor($control);
                }
            }
        }
    }
}
