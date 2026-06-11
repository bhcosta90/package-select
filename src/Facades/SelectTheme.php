<?php

declare(strict_types = 1);

namespace Brcas\Select\Facades;

use Brcas\Select\SelectTheme as SelectThemeClass;
use Brcas\Select\Theme\InputThemeDomain;
use Brcas\Select\Theme\ItemThemeDomain;
use Brcas\Select\Theme\ThemeDomain;
use Illuminate\Support\Facades\Facade;

/**
 * @method static InputThemeDomain input()
 * @method static ThemeDomain      dropdown()
 * @method static ItemThemeDomain  item()
 * @method static ThemeDomain      tag()
 * @method static ThemeDomain      footer()
 * @method static ThemeDomain      empty()
 * @method static ThemeDomain      error()
 * @method static SelectThemeClass placeholder(string $value)
 * @method static SelectThemeClass block(string $name)
 * @method static SelectThemeClass replace(array|string $from, ?string $to = null)
 * @method static SelectThemeClass append(string $content)
 * @method static SelectThemeClass prepend(string $content)
 * @method static SelectThemeClass remove(array|string $classes)
 * @method static string           get(string $block)
 * @method static array            toArray()
 *
 * @see SelectThemeClass
 */
final class SelectTheme extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SelectThemeClass::class;
    }
}
