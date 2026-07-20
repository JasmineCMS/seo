<?php

namespace Jasmine\Seo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
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

    public static function seoableJasmineOnSaving(Model|BreadableInterface $m, array $data): array
    {
        $keys = ['seo_title', 'seo_description', 'seo_canonical', 'seo_image'];

        $source = $m instanceof JasminePage ? ($m->content ?? []) : $data;
        session([$m::class . ':seo' => Arr::only($source, $keys)]);

        if ($m instanceof JasminePage) {
            $m->content = Arr::except($m->content ?? [], $keys);
        } else {
            foreach ($keys as $k) unset($m[$k]);
        }

        return Arr::except($data, $keys);
    }

    public static function seoableJasmineOnSaved(Model|BreadableInterface $m) {
        $bag = session($m::class . ':seo', []);

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
    }

    public static function seoableJasmineOnRetrievedForEdit(Model|BreadableInterface $m, array $data): array
    {
        $m->load('seo');

        if (method_exists($m, 'getLocale') && $m->seo) {
            $m->seo->setLocale(request('_locale', app()->getLocale()));
        }

        $bag = [
            'seo_title'       => $m->seo?->title,
            'seo_description' => $m->seo?->description,
            'seo_canonical'   => $m->seo?->canonical,
            'seo_image'       => $m->seo?->image,
        ];

        if ($m instanceof JasminePage) $data['content'] = [...($data['content'] ?? []), ...$bag];
        else $data = [...$data, ...$bag];

        return $data;
    }

    public function seo() { return $this->morphOne(JasmineSeo::class, 'seoable'); }
}
