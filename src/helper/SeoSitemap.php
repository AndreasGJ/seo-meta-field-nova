<?php
namespace Gwd\SeoMeta\Helper;

class SeoSitemap {
    private $items = [];

    public function __construct()
    {
        $sitemap_models = config('seo.sitemap_models');

        $this->attachModelItems($sitemap_models);
    }

    private function attachModelItems(array $sitemap_models = [])
    {
        foreach ($sitemap_models as $sitemap_model) {
            $items = $sitemap_model::getSitemapItems();

            if ($items && $items->count() > 0) {
                $this->items = array_merge($this->items, $items->map(function($item){
                    return (object)[
                        'url'     => $item->getSitemapItemUrl(),
                        'lastmod' => $item->getSitemapItemLastModified(),
                    ];
                })->toArray());
            }
        }
    }

    public function toArray() {
        return $this->items;
    }

    public function toXml(){
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'.
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($this->items as $item) {
            $xml .= '<url>'.
                '<loc>' . $item->url . '</loc>'.
                '<lastmod>' . $item->lastmod . '</lastmod>'.
            '</url>';
        }
        $xml .= '</urlset>';

        return $xml;
    }
}
