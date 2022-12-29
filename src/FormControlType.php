<?php

declare(strict_types=1);

namespace Hustlahusky\Forms;

abstract class FormControlType
{
    public const TEXT = 'text';
    public const TEXTAREA = 'textarea';
    public const NUMBER = 'number';
    public const EMAIL = 'email';
    public const TEL = 'tel';
    public const RADIO = 'radio';
    public const CHECKBOX = 'checkbox';
    public const CHECKBOX_MULTI = 'checkbox_multi';
    public const SELECT = 'select';
    public const HIDDEN = 'hidden';
}
