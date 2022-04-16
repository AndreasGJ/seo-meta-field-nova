<?php

namespace Gwd\SeoMeta\Traits;

trait SeoSitemapTrait
{
    /**
     * Get the item's url for sitemap.
     *
     * @return string
     */
    abstract public function getSitemapItemUrl(): String;

    /**
     * Get the item's last modified date for sitemap.
     *
     * @return string|null
     */
    public function getSitemapItemLastModified()
    {
        $lastmod = $this->updated_at ?: $this->created_at;

        return $lastmod ? $lastmod->format('Y-m-d') : null;
    }

    /**
     * Get the model's items for sitemap.
     */
    abstract public static function getSitemapItems();
}
