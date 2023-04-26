<?php

declare(strict_types=1);

namespace Hustlahusky\Forms\Render;

function writeln(...$lines): void
{
    foreach ($lines as $line) {
        if (\is_iterable($line)) {
            foreach ($line as $l) {
                writeln($l);
            }
            continue;
        }

        echo $line, \PHP_EOL;
    }
}
