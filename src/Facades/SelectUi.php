<?php

declare(strict_types = 1);

namespace Brcas\Select\Facades;

use Brcas\Select\SelectTheme;
use Illuminate\Support\Facades\Facade;

/**
 * @method static SelectTheme customize()
 * @method static SelectTheme block(string $name)
 * @method static SelectTheme set(string $name, string $value)
 * @method static SelectTheme replace(array|string $from, ?string $to = null)
 * @method static SelectTheme append(string $content)
 * @method static SelectTheme prepend(string $content)
 * @method static SelectTheme remove(array|string $classes)
 * @method static string      get(string $block)
 * @method static array       toArray()
 *
 * @see SelectTheme
 */
final class SelectUi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SelectTheme::class;
    }
}
