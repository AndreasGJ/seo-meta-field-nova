<?php

use Gwd\SeoMeta\Helper\SeoSitemap;

if(config('seo.sitemap_status')){
    Route::get(config('seo.sitemap_path'), function(){
        $sitemap = new SeoSitemap;

        return response($sitemap->toXml(), 200, [
            'Content-Type' => 'application/xml'
        ]);
    });
}
