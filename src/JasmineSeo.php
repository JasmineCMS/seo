<?php

namespace Jasmine\Seo;

use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Database\Eloquent\Model;
use Jasmine\Jasmine\Bread\BreadableInterface;
use Jasmine\Jasmine\Bread\Fields\ImageField;
use Jasmine\Jasmine\Bread\Fields\InputField;
use Jasmine\Jasmine\Bread\Fields\TextareaField;
use Jasmine\Seo\Models\Seoable;

class JasmineSeo
{
    public function fieldsBox(): array
    {
        return [
            __('SEO Properties') => [
                (new InputField('seo_title'))
                    ->setLabel(__('Title'))
                    ->setValidation(['nullable', 'string']),

                (new TextareaField('seo_description'))
                    ->setLabel(__('Description'))
                    ->setValidation(['nullable', 'string']),

                (new InputField('seo_canonical'))
                    ->setLabel(__('Canonical URL'))
                    ->setValidation(['nullable', 'string', 'url']),

                (new ImageField('seo_image'))
                    ->setLabel(__('Image')),
            ],
        ];
    }

    public function generate($minify = false) { return \SEO::generate($minify); }

    /**
     * @param Model|BreadableInterface|Seoable $model
     */
    public function build(Model $model)
    {
        if (!method_exists($model, 'isSeoable')) return;

        if ($model->seo->title) SEOTools::setTitle($model->seo->title);
        if ($model->seo->description) SEOTools::setDescription($model->seo->description);
        if ($model->seo->canonical) SEOTools::setCanonical($model->seo->canonical);
        if ($model->seo->image) SEOTools::addImages($model->seo->image->src);
    }
}
