<?php

namespace Spatie\Sluggable\Tests;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class UniqueWithTestModel extends Model
{
    use HasSlug;

    protected $table = 'unique_with_test_models';

    protected $guarded = [];

    public $timestamps = false;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->uniqueWith(['type'])
            ->saveSlugsTo('slug');
    }
}
