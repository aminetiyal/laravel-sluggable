<?php

namespace Aminetiyal\Sluggable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSlug
{
    protected SlugOptions $slugOptions;

    abstract public function getSlugOptions(): SlugOptions;

    protected static function bootHasSlug()
    {
        static::creating(function (Model $model) {
            $model->generateSlugOnCreate();
        });

        static::updating(function (Model $model) {
            $model->generateSlugOnUpdate();
        });
    }

    protected function generateSlugOnCreate()
    {
        $this->slugOptions = $this->getSlugOptions();

        if (! $this->slugOptions->generateSlugsOnCreate) {
            return;
        }

        $this->addSlug();
    }

    protected function generateSlugOnUpdate()
    {
        $this->slugOptions = $this->getSlugOptions();

        if (! $this->slugOptions->generateSlugsOnUpdate) {
            return;
        }

        $this->addSlug();
    }

    public function generateSlug()
    {
        $this->slugOptions = $this->getSlugOptions();

        $this->addSlug();
    }

    protected function addSlug()
    {
        $this->ensureValidSlugOptions();

        $slug = $this->generateNonUniqueSlug();

        if ($this->slugOptions->generateUniqueSlugs) {
            $slug = $this->makeSlugUnique($slug);
        }

        $slugField = $this->slugOptions->slugField;

        $this->$slugField = $slug;
    }

    protected function generateNonUniqueSlug(): string
    {
        $slugField = $this->slugOptions->slugField;

        if ($this->hasCustomSlugBeenUsed() && ! empty($this->$slugField)) {
            return $this->$slugField;
        }

        return Str::slug($this->getSlugSourceString(), $this->slugOptions->slugSeparator, $this->slugOptions->slugLanguage);
    }

    protected function hasCustomSlugBeenUsed(): bool
    {
        $slugField = $this->slugOptions->slugField;

        return $this->getOriginal($slugField) != $this->$slugField;
    }

    protected function getSlugSourceString(): string
    {
        if (is_callable($this->slugOptions->generateSlugFrom)) {
            $slugSourceString = $this->getSlugSourceStringFromCallable();

            return $this->generateSubstring($slugSourceString);
        }

        $slugSourceString = collect($this->slugOptions->generateSlugFrom)
            ->map(fn (string $fieldName): string => data_get($this, $fieldName, ''))
            ->implode($this->slugOptions->slugSeparator);

        return $this->generateSubstring($slugSourceString);
    }

    protected function getSlugSourceStringFromCallable(): string
    {
        return call_user_func($this->slugOptions->generateSlugFrom, $this);
    }

    protected function makeSlugUnique(string $slug): string
    {
        $originalSlug = $slug;
        $i = 1;

        while ($this->otherRecordExistsWithSlug($slug) || $slug === '') {
            $slug = $originalSlug.$this->slugOptions->slugSeparator.$i++;
        }

        return $slug;
    }

    protected function otherRecordExistsWithSlug(string $slug): bool
    {
        $key = $this->getKey();

        if ($this->getIncrementing()) {
            $key ??= '0';
        }

        $query = static::where($this->slugOptions->slugField, $slug);

         // check if generateUniqueSlugs is true and uniqueWith array not empty
        if (count($this->slugOptions->uniqueWith) > 0 && $this->slugOptions->generateUniqueSlugs) {
            // get column and value of each item of uniqueWith
            foreach ($this->slugOptions->uniqueWith as $column) {
                // run additional queries related to uniqueWith array
                $query->where($column, $this->$column);
            }
        }

        $query->where($this->getKeyName(), '!=', $key)->withoutGlobalScopes();

        if ($this->usesSoftDeletes()) {
            $query->withTrashed();
        }

        return $query->exists();
    }

    protected function usesSoftDeletes(): bool
    {
        return (bool) in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this));
    }

    protected function ensureValidSlugOptions()
    {
        if (is_array($this->slugOptions->generateSlugFrom) && ! count($this->slugOptions->generateSlugFrom)) {
            throw InvalidOption::missingFromField();
        }

        if (! strlen($this->slugOptions->slugField)) {
            throw InvalidOption::missingSlugField();
        }

        if ($this->slugOptions->maximumLength <= 0) {
            throw InvalidOption::invalidMaximumLength();
        }
    }

    /**
     * Helper function to handle multi-bytes strings if the module mb_substr is present,
     * default to substr otherwise.
     */
    protected function generateSubstring($slugSourceString)
    {
        if (function_exists('mb_substr')) {
            return mb_substr($slugSourceString, 0, $this->slugOptions->maximumLength);
        }

        return substr($slugSourceString, 0, $this->slugOptions->maximumLength);
    }
}
