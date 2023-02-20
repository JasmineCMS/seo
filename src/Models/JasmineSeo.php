<?php

namespace Jasmine\Seo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Jasmine\Jasmine\Bread\Translatable;

/**
 * Jasmine\Seo\Models\JasmineSeo
 *
 * @property int         $id
 * @property string      $seoable_type
 * @property int         $seoable_id
 * @property string|null $title
 * @property string|null $description
 * @property string|null $canonical
 * @property string|null $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @mixin \Eloquent
 */
class JasmineSeo extends Model
{
    use Translatable;

    protected $fillable = [
        'seoable_type',
        'seoable_id',
        'title',
        'description',
        'canonical',
        'image',
    ];

    public $translatable = ['title', 'description', 'canonical'];

    protected $casts = ['image' => 'object'];

    public function seoable() { return $this->morphTo(); }
}
