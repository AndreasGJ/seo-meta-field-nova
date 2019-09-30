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

    private $title_format = ':text';

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

        return $this->withMeta([
            'hostname'     => url(''),
            'title_format' => $this->title_format
        ]);
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

        if($this->value && $this->value->image){
            $this->withMeta([ 'image_url' => Storage::url($this->value->image) ]);
        }
    }

    public function setupUrl($path = ''){
        return $this->withMeta(['url' => url($path)]);
    }

    public function titleFormat($format = ':text'){
        $this->title_format = $format;
        return $this->withMeta([
            'title_format' => $format
        ]);
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
                    'title_format' => $this->title_format
                ]
            ]);
            $has_change = true;
        }

        $file_attr = $requestAttribute.'_image';
        if ($request->hasFile($file_attr) && $request->file($file_attr)->isValid()) {
            $image = $request->{$file_attr};

            $path = $request->{$file_attr}->store('public');
            if ($path) {
                // Delete old file if any.
                if ($relationship->image) {
                    Storage::delete($relationship->image);
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
