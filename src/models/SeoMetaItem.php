<?php

namespace Gwd\SeoMeta\Models;

use Illuminate\Database\Eloquent\Model;

class SeoMetaItem extends Model
{
    protected $guarded = ['id'];
    protected $table = 'seo_meta';

    protected $casts = [
        'params' => 'object'
    ];

    /**
     * Get the owning seo_metaable model.
     */
    public function seo_metaable()
    {
        return $this->morphTo();
    }
}
