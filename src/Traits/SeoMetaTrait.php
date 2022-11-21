<?php

namespace Gwd\SeoMeta\Traits;

use Gwd\SeoMeta\Models\SeoMetaItem;
use Illuminate\Support\Facades\Storage;

trait SeoMetaTrait
{
    private $title = [];
    private $description = [];
    private $keywords = [];
    private $image = null;
    private $follow = null;

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
     * @throws \Exception
     */
    public function getSeoMeta()
    {
        $attrs = false;
        if ($this->seo_meta) {
            $attrs = $this->seo_meta->toArray();
        } else {
            $this->setDefaultValues();
            if ($this->validateTitleExistsForAnyLocale()) {
                $formatter = $this->getSeoTitleFormatter() ?? config('seo.title_formatter');
                $attrs = [
                    'title' => $this->title,
                    'description' => $this->description,
                    'keywords' => $this->keywords,
                    'image' => $this->image,
                    'follow_type' => $this->follow,
                    'params' => (object)[
                        'title_format' => $formatter
                    ]
                ];
            }
        }

        if ($attrs && isset($attrs['image']) && $attrs['image']) {
            $attrs['image_path'] = strpos($attrs['image'], '//') === false
                ? Storage::disk(config('seo.disk'))->url($attrs['image'])
                : $attrs['image'];
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
        return $this->title;
    }

    /**
     * Get default SEO description
     *
     * @return array
     */
    public function getSeoDescriptionDefault(): array
    {
        return $this->description;
    }

    /**
     * Get default SEO title
     *
     * @return array
     */
    public function getSeoKeywordsDefault(): array
    {
        return $this->keywords;
    }

    /**
     * Get default SEO title
     *
     * @return string|null
     */
    public function getSeoImageDefault(): ?string
    {
        return $this->image;
    }

    /**
     * Get default SEO title
     *
     * @return string|null
     */
    public function getSeoFollowDefault(): ?string
    {
        return $this->follow;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function buildSeoForCurrentLocale(): array
    {
        $seo = $this->getSeoMeta();
        $fallback_locale = config('seo.fallback_locale');
        $translatable_keys = ['title', 'description', 'keywords'];
        $locale = app()->getLocale();
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

    /**
     * @param array $title
     *
     * @return bool
     */
    private function validateTitleExistsForAnyLocale(): bool
    {
        $locale = app()->getLocale();
        $fallback_locale = config('seo.fallback_locale');
        $exists = false;
        $title = $this->title;
        if (isset($title[$locale]) && !empty($title[$locale])) {
            $exists = true;
        } elseif (isset($title[$fallback_locale]) && !empty($title[$fallback_locale])) {
            $exists = true;
        }
        return $exists;
    }

    /**
     * Set the default values from the config
     * then call the registerDefaultValues function to override
     * them if the user added new default values
     */
    private function setDefaultValues(): void
    {
        if (config('seo.default_seo_image')) {
            $this->addImageDefault(asset(config('seo.default_seo_image')));
        }
        foreach (config('seo.available_locales') as $locale) {
            $this->addTitleDefault(config('seo.default_seo_title', $locale));
            $this->addDescriptionDefault(config('seo.default_seo_description', $locale));
            $this->addKeywordsDefault(config('seo.default_seo_keywords', $locale));
        }
        $this->addFollowDefault(config('seo.default_follow_type'));
        // override by user defaults if exists
        $this->registerDefaultValues();
    }

    /**
     * REGISTERING THE DEFAULT VALUES IF EXISTS
     */
    public function registerDefaultValues(): void
    {

    }

    /**
     * @param string|null $value
     * @param string|null $locale
     */
    public function addTitleDefault(string $value = null, string $locale = null): void
    {
        if (!$locale) {
            $locale = config('seo.fallback_locale');
        }
        $this->title[$locale] = $value;
    }

    /**
     * @param string|null $value
     * @param string|null $locale
     */
    public function addDescriptionDefault(string $value = null, string $locale = null): void
    {
        if (!$locale) {
            $locale = config('seo.fallback_locale');
        }
        $this->description[$locale] = $value;
    }

    /**
     * @param string|null $value
     * @param string|null $locale
     */
    public function addKeywordsDefault(string $value = null, string $locale = null): void
    {
        if (!$locale) {
            $locale = config('seo.fallback_locale');
        }
        $this->keywords[$locale] = $value;
    }

    /**
     * @param string|null $value
     */
    public function addImageDefault(string $value = null): void
    {
        $this->image = $value;
    }

    /**
     * @param string $value
     */
    public function addFollowDefault(string $value): void
    {
        $this->follow = $value;
    }
}
