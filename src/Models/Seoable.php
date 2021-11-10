<?php

namespace Jasmine\Seo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Jasmine\Jasmine\Bread\Translatable;
use Jasmine\Jasmine\Models\JasminePage;

/**
 * * @property-read JasmineSeo|Translatable|\Eloquent $seo
 */
trait Seoable
{
    public function isSeoable() { return true; }

    public static function bootSeoable()
    {
        static::saving(function (Model $model) {
            $bag = $model instanceof JasminePage ? $model->content : $model;

            unset($bag['seo_title']);
            unset($bag['seo_description']);
            unset($bag['seo_canonical']);
            unset($bag['seo_image']);

            if ($model instanceof JasminePage) $model->content = $bag;
        });

        static::saved(function (Model $model) {
            // validated previously by bread controller
            $data = [
                'title'       => request('seo_title'),
                'description' => request('seo_description'),
                'canonical'   => request('seo_canonical'),
                'image'       => request('seo_image'),
            ];

            /** @var JasmineSeo $seo */
            $seo = JasmineSeo::firstOrNew([
                'seoable_type' => $model::class,
                'seoable_id'   => $model->getKey(),
            ]);

            $locale = app()->getLocale();
            if (method_exists($model, 'getLocale')) $locale = \request()->get('_locale', app()->getLocale());

            $seo->setLocale($locale);
            $seo->fill($data);
            $seo->save();
        });

        static::retrieved(function (Model $model) {
            if (!Route::is('jasmine.*')) return;
            /** @var Model|Seoable $model */
            if (method_exists($model, 'getLocale') && $model->seo) $model->seo->setLocale($model->getLocale());

            $bag = [
                'seo_title'       => $model->seo?->title,
                'seo_description' => $model->seo?->description,
                'seo_canonical'   => $model->seo?->canonical,
                'seo_image'       => $model->seo?->image,
            ];

            if ($model instanceof JasminePage) {
                $content = $model->content;
                foreach ($bag as $k => $v) $content[$k] = $v;
                $model->content = $content;
            } else {
                foreach ($bag as $k => $v) $model->setAttribute($k, $v);
            }
        });
    }

    public function seo()
    {
        return $this->morphOne(JasmineSeo::class, 'seoable');
    }
}
