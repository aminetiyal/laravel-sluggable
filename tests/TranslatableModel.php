<?php

namespace Aminetiyal\Sluggable\Tests;

use Illuminate\Database\Eloquent\Model;
use Aminetiyal\Sluggable\HasTranslatableSlug;
use Aminetiyal\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class TranslatableModel extends Model
{
    use HasTranslations;
    use HasTranslatableSlug;

    protected $table = 'translatable_models';

    protected $guarded = [];
    public $timestamps = false;

    public $translatable = ['name', 'other_field', 'slug'];

    private $customSlugOptions;

    public function useSlugOptions($slugOptions)
    {
        $this->customSlugOptions = $slugOptions;
    }

    public function getSlugOptions(): SlugOptions
    {
        return $this->customSlugOptions ?: SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
}
