<?php

namespace Gwd\SeoMeta\Traits;

trait SeoSitemapTrait
{
    abstract public function getSitemapItemUrl(): String;

    public function getSitemapItemLastModified(){
        if(isset($this->updated_at) || isset($this->created_at)) {
            return isset($this->updated_at) ? $this->updated_at : $this->created_at;
        }
        return null;
    }

    abstract public static function getSitemapItems();
}
