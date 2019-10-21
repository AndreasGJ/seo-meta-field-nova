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
        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'image' => $image,
            'image_path' => $image ? Storage::url($image) : null,
            'follow_type' => $follow_type
        ];
    }
}
