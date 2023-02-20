<?php

namespace Jasmine\Seo\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array fieldsBox()
 * @method static mixed generate($minify = false)
 * @method static void build(Model $model)
 */
class JasmineSeo extends Facade
{
    protected static function getFacadeAccessor() { return 'jasmine-seo'; }
}
