<?php

declare(strict_types=1);

namespace Hustlahusky\Forms;

/**
 * @implements \IteratorAggregate<string,string>
 */
final class FormControlOptions implements \IteratorAggregate, \Countable
{
    /**
     * @var array<string,true>
     */
    private array $knownValues = [];

    /**
     * @var list<string>
     */
    private array $values = [];

    /**
     * @var list<string>
     */
    private array $labels = [];

    /**
     * @var list<string|null>
     */
    private array $groups = [];

    private bool $hasGroups = false;

    public function __construct()
    {
    }

    public static function new(): self
    {
        return new self();
    }

    public function addOption(string $value, ?string $label = null, ?string $group = null): void
    {
        $this->knownValues[$value] = true;
        $this->values[] = $value;
        $this->labels[] = $label ?: $value;
        $this->groups[] = $group = $group ?: null;

        if (null !== $group) {
            $this->hasGroups = true;
        }
    }

    public function has(string $value): bool
    {
        return isset($this->knownValues[$value]);
    }

    /**
     * @return array<string,string>|array<string,array<string,string>>
     */
    public function toSelectArray(): array
    {
        $out = [];

        foreach ($this->values as $i => $value) {
            $label = $this->labels[$i] ?? $value;
            $group = $this->groups[$i] ?? '-';

            $out[$group] ??= [];
            $out[$group][$value] = $label;
        }

        return $this->hasGroups ? $out : $out[\array_key_first($out)] ?? [];
    }

    /**
     * @return \Generator<string,string>
     */
    public function getIterator(): \Generator
    {
        foreach ($this->values as $i => $value) {
            $label = $this->labels[$i] ?? $value;

            yield $value => $label;
        }
    }

    public function count(): int
    {
        return \count($this->values);
    }
}
