<?php

namespace Gwd\SeoMeta\Models;

use Illuminate\Database\Eloquent\Model;

class SeoMetaItem extends Model
{
    /**
     * Guarded variables
     *
     * @var array
     */
    protected $guarded = [ 'id' ];
    
    /**
     * Hidden variables
     *
     * @var array
     */
    protected $hidden = [
        'seo_metaable_type', 'created_at', 'updated_at'
    ];

    /**
     * Table name for the model
     *
     * @var string
     */
    protected $table = 'seo_meta';

    /**
     * Casts variables
     *
     * @var array
     */
    protected $casts = [
        'params' => 'object'
    ];

    /**
     * Get the owning seo_metaable model.
     *
     * @return morphTo
     */
    public function seo_metaable()
    {
        return $this->morphTo();
    }
}
