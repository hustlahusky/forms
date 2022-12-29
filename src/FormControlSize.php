<?php

declare(strict_types=1);

namespace Hustlahusky\Forms;

final class FormControlSize
{
    public const GRID_SIZE = 12;

    public int $size;

    public function __construct(int $size = self::GRID_SIZE)
    {
        $this->size = $size;
    }

    public static function new(int $size = self::GRID_SIZE): self
    {
        return new self($size);
    }

    public function add(self $other): self
    {
        return new self($this->size + $other->size);
    }

    public function isFull(): bool
    {
        return $this->size === self::GRID_SIZE;
    }

    public function isOverflowing(): bool
    {
        return $this->size > self::GRID_SIZE;
    }

    public static function empty(): self
    {
        return new self(0);
    }
}
