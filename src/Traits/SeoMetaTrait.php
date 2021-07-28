<?php

namespace Gwd\SeoMeta\Traits;

use Gwd\SeoMeta\Models\SeoMetaItem;
use Illuminate\Support\Facades\Storage;

trait SeoMetaTrait
{
    /**
     * Get the seo_metaable relationship.
     *
     * @return morphOne
     */
    public function seo_meta()
    {
        return $this->morphOne(SeoMetaItem::class, 'seo_metaable');
    }

    /**
     * Return the seo_metaable data as array
     *
     * @return array
     */
    public function getSeoMeta()
    {
        $attrs = false;

        if ($this->seo_meta) {
            $attrs = $this->seo_meta->toArray();
        } else {
            $title = $this->getSeoTitleDefault();

            if ($title) {
                $formatter = $this->getSeoTitleFormatter() ?? config('seo.title_formatter');
                $attrs = [
                    'title' => $title,
                    'description' => $this->getSeoDescriptionDefault(),
                    'keywords' => $this->getSeoKeywordsDefault(),
                    'image' => $this->getSeoImageDefault(),
                    'follow_type' => $this->getSeoFollowDefault(),
                    'params' => (object)[
                        'title_format' => $formatter
                    ]
                ];
            }
        }

        if ($attrs && isset($attrs['image']) && $attrs['image']) {
            $attrs['image_path'] = strpos($attrs['image'], '//') === false ? Storage::url($attrs['image']) : $attrs['image'];
        }

        return $attrs;
    }

    /**
     * Get SEO title formatter
     *
     * @return
     */
    public function getSeoTitleFormatter()
    {
        return config('seo.title_formatter');
    }

    /**
     * Get default SEO title
     *
     * @return array
     */
    public function getSeoTitleDefault(): array
    {
        return $this->getDefaultValue();
    }

    /**
     * Get default SEO description
     *
     * @return array
     */
    public function getSeoDescriptionDefault(): array
    {
        return $this->getDefaultValue();
    }

    /**
     * Get default SEO title
     *
     * @return array
     */
    public function getSeoKeywordsDefault(): array
    {
        return $this->getDefaultValue();
    }

    /**
     * Get default SEO title
     *
     * @return string
     */
    public function getSeoImageDefault()
    {
        if (config('seo.default_seo_image')) {
            return asset(config('seo.default_seo_image'));
        }

        return null;
    }

    /**
     * Get default SEO title
     *
     * @return string
     */
    public function getSeoFollowDefault()
    {
        return config('seo.default_follow_type');
    }

    /**
     * @return array
     */
    public function getDefaultValue(): array
    {
        $array = [];
        foreach (config('seo.available_locales') as $locale) {
            $array[$locale] = null;
        }
        return $array;
    }

    /**
     * @return array
     */
    public function buildSeoForCurrentLocale(): array
    {
        $seo = $this->getSeoMeta();
        $locale = app()->getLocale();
        $fallback_locale = config('seo.fallback_locale');
        $translatable_keys = ['title', 'description', 'keywords'];
        foreach ($translatable_keys as $key) {
            if (isset($seo[$key])) {
                if (isset($seo[$key][$locale]) && !empty($seo[$key][$locale])) {
                    $seo[$key] = $seo[$key][$locale];
                } elseif (isset($seo[$key][$fallback_locale]) && !empty($seo[$key][$fallback_locale])) {
                    $seo[$key] = $seo[$key][$fallback_locale];
                } else {
                    $seo[$key] = null;
                }
            } else {
                $seo[$key] = null;
            }
        }
        return $seo;
    }
}
