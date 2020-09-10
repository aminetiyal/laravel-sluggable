<?php

namespace Aminetiyal\Sluggable\Tests;

use Aminetiyal\Sluggable\HasSlug;
use Aminetiyal\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;

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
