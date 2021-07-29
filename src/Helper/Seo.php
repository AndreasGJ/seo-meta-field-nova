<?php
namespace Gwd\SeoMeta\Helper;

use Illuminate\Support\Facades\Storage;

class Seo {
    /**
     * Constructs the SEO array for a given page
     *
     * @param array $title       SEO title
     * @param array $description SEO description
     * @param array $keywords    SEO keywords
     * @param string|null $image       SEO image
     * @param string $follow_type SEO robots value
     *
     * @return array
     */
    public static function renderAttributes(array $title = [],array $description = [],array $keywords = [],string $image = null, string $follow_type = 'index, follow'):array
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
