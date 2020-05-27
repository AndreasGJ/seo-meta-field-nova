<?php
namespace Gwd\SeoMeta\Helper;

use Carbon\Carbon;

class SeoSitemap {
    /**
     * Array of the all the items in the sitemap
     *
     * @var array
     */
    private $items = [];

    /**
     * Should the sitemap have the lastmod var?
     * 
     * @var bool
     */
    private $use_lastmod = true;

    /**
     * Construct the sitemap class
     *
     * @return void
     */
    public function __construct($use_lastmod = true)
    {
        $this->use_lastmod = $use_lastmod;

        $sitemap_models = config('seo.sitemap_models');
        $this->attachModelItems($sitemap_models);
    }

    /**
     * Attach the model items
     *
     * @param array $sitemap_models
     *
     * @return void
     */
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

    /**
     * Attach a custom sitemap item
     *
     * @param string $path    Path on the current site
     * @param string $lastmod Date of last edit
     *
     * @return SeoSitemap
     */
    public function attachCustom($path, $lastmod = null)
    {
        $this->items[] = (object)[
            'url' => url($path),
            'lastmod' => $lastmod
        ];
        return $this;
    }

    /**
     * Return sitemap items as array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * Return xml for sitemap items
     *
     * @return string
     */
    public function toXml()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'.
        '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $lastmod = Carbon::now()->format('Y-m-d H:i:s');

        foreach ($this->items as $item) {
            $use_lastmod = $this->use_lastmod ? ($item->lastmod ?? $lastmod) : null;
            $xml .= '<url>'.
                '<loc>' . (substr($item->url, 0, 1) == '/' ? url($item->url) : $item->url) . '</loc>'.
                ($use_lastmod ? '<lastmod>' . $use_lastmod . '</lastmod>' : '').
            '</url>';

            if ($item->lastmod) {
                $lastmod = $item->lastmod;
            }
        }
        $xml .= '</urlset>';

        return $xml;
    }
}
