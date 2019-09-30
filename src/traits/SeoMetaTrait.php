<?php

namespace Gwd\SeoMeta\Traits;

use Gwd\SeoMeta\Models\SeoMetaItem;
use Illuminate\Support\Facades\Storage;

trait SeoMetaTrait
{
    /**
     * Get page seo meta
     */
    public function seo_meta()
    {
        return $this->morphOne(SeoMetaItem::class, 'seo_metaable');
    }

    public function getSeoMeta(){
        $attrs = $this->seo_meta->toArray();

        if($attrs['image']){
            $attrs['image_path'] = Storage::url($attrs['image']);
        }

        return $attrs;
    }
}
