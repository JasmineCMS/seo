<?php

namespace Jasmine\Seo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Jasmine\Jasmine\Bread\BreadableInterface;
use Jasmine\Jasmine\Bread\Translatable;
use Jasmine\Jasmine\Models\JasminePage;

/**
 * * @property-read JasmineSeo|Translatable|\Eloquent $seo
 */
trait Seoable
{
    public function isSeoable() { return true; }

    public static function seoableJasmineOnSaving(Model|BreadableInterface $m, array $data)
    {
        $bag = $m instanceof JasminePage ? $m->content : $data;

        /** @var JasmineSeo $seo */
        $seo = JasmineSeo::firstOrNew([
            'seoable_type' => $m::class,
            'seoable_id'   => $m->getKey(),
        ]);

        $locale = app()->getLocale();
        if (method_exists($m, 'getLocale')) $locale = \request()->get('_locale', app()->getLocale());

        $seo->setLocale($locale)->fill([
            'title'       => $bag['seo_title'] ?? null,
            'description' => $bag['seo_description'] ?? null,
            'canonical'   => $bag['seo_canonical'] ?? null,
            'image'       => $bag['seo_image'] ?? new \stdClass(),
        ])->save();

        unset(
            $m['seo_title'],
            $m['seo_description'],
            $m['seo_canonical'],
            $m['seo_image'],
        );
    }

    public static function seoableJasmineOnRetrievedForEdit(Model|BreadableInterface $m)
    {
        $m->load('seo');

        if (method_exists($m, 'getLocale') && $m->seo) $m->seo->setLocale(request('_locale', app()->getLocale()));

        $data = [
            'seo_title'       => $m->seo?->title,
            'seo_description' => $m->seo?->description,
            'seo_canonical'   => $m->seo?->canonical,
            'seo_image'       => $m->seo?->image,
        ];

        if ($m instanceof JasminePage) {
            $content = $m->content;
            foreach ($data as $k => $v) $content[$k] = $v;
            $m->content = $content;
        } else foreach ($data as $k => $v) $m->setAttribute($k, $v);
    }

    public function seo() { return $this->morphOne(JasmineSeo::class, 'seoable'); }
}
