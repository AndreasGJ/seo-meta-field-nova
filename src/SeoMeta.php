<?php

namespace Gwd\SeoMeta;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Facades\Storage;
use Gwd\SeoMeta\Models\SeoMetaItem;

class SeoMeta extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'seo-meta';

    /**
     * Path for the SEO image
     *
     * @var string
     */
    private $file_path = '/';

    /**
     * Disk for the SEO image
     *
     * @var string
     */
    private $file_disk;

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|callable|null  $attribute
     * @param  callable|null  $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->file_disk = config('seo.disk');

        $this->withMeta([
            'hostname'     => url(''),
            'title_format' => config('seo.title_formatter'),
            'follow_type_options' => config('seo.follow_type_options'),
        ]);
        $this->hideWhenCreating();
    }

    /**
     * Resolve the field's value.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolve($resource, $attribute = null)
    {
        parent::resolve($resource, $attribute);

        $meta = [
            'default_values' => false,
            'canonical_links' => $resource->getCanonicalLinks(),
            'title_format' => $resource->getSeoTitleFormatter()
        ];
        if (!$this->value) {
            $this->value = [
                'title' => $resource->getSeoTitleDefault() ?? '',
                'description' => $resource->getSeoDescriptionDefault() ?? '',
                'keywords' => $resource->getSeoKeywordsDefault() ?? '',
                'image' => $resource->getSeoImageDefault(),
                'follow_type' => $resource->getSeoFollowDefault()
            ];
            $meta['default_values'] = true;
        }

        if ($this->value && $this->value['image']) {
            $meta['image_url'] = Storage::disk($this->file_disk)->url($this->value['image']);
        }
        $this->withMeta($meta);
    }

    /**
     * Set the url for given Model
     *
     * @param string $path Path to the view
     *
     * @return Field
     */
    public function setupUrl($path = '')
    {
        return $this->withMeta(['url' => url($path)]);
    }

    /**
     * Set the storage disk for the SEO image
     *
     * @param string $disk Which disk to put the image
     *
     * @return Field
     */
    public function disk($disk = 'public')
    {
        $this->file_disk = $disk;

        return $this;
    }

    /**
     * Set the storage path for the SEO image
     *
     * @param string $path Path to put the image
     *
     * @return Field
     */
    public function path($path = '/')
    {
        $this->file_path = $path;

        return $this;
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $requestAttribute
     * @param  object  $model
     * @param  string  $attribute
     * @return void
     */
    protected function fillAttributeFromRequest(NovaRequest $request,
                                                $requestAttribute,
                                                $model,
                                                $attribute)
    {
        $has_change = false;
        $relationship = $model->{$attribute} ?? new SeoMetaItem;

        if($model->id){
            if(!$relationship->seo_metaable_type){
                $relationship->seo_metaable_type = get_class($model);
                $relationship->seo_metaable_id = $model->id;
                $has_change = true;
            }
            if ($request->exists($requestAttribute) && is_string($request[$requestAttribute])) {
                $value = json_decode($request[$requestAttribute]);

                $relationship->fill([
                    'title'       => $value->title ?? null,
                    'description' => $value->description ?? null,
                    'keywords'    => $value->keywords ?? null,
                    'follow_type' => $value->follow_type ?? null,
                    'params' => [
                        'title_format' => $model->getSeoTitleFormatter(),
                        'canonical_links' => $value->params->canonical_links ?? []
                    ]
                ]);
                $has_change = true;
            }

            $file_attr = $requestAttribute.'_image';
            if ($request->hasFile($file_attr) && $request->file($file_attr)->isValid()) {
                $image = $request->{$file_attr};

                // Save the SEO image
                $path = $request->{$file_attr}->store($this->file_path, ['disk' => $this->file_disk]);
                if ($path) {
                    // Delete old file if any.
                    if ($relationship->image) {
                        Storage::disk($this->file_disk)->delete($relationship->image);
                    }

                    $relationship->image = $path;
                    $has_change = true;
                }
            }

            if($has_change){
                $relationship->save();
            }
        }
    }
}
