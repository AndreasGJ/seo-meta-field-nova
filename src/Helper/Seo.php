<?php
namespace Gwd\SeoMeta\Helper;

use Illuminate\Support\Facades\Storage;

class Seo {
    /**
     * Constructs the SEO array for a given page
     *
     * @param string $title       SEO title
     * @param string $description SEO description
     * @param string $keywords    SEO keywords
     * @param string $image       SEO image
     * @param string $follow_type SEO robots value
     *
     * @return array
     */
    public static function renderAttributes($title = '', $description = '', $keywords = '', $image = null, $follow_type = 'index, follow')
    {
        if (!$image && config('seo.default_seo_image')) {
            $image = asset(config('seo.default_seo_image'));
        }

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'image' => $image,
            'image_path' => $image && strpos($image, '//') === false ? Storage::url($image) : $image,
            'follow_type' => $follow_type
        ];
    }
}
