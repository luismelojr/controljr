<?php

namespace App\Traits;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasTags
{
    /**
     * Get the tags for the model.
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Attach tags to the model.
     *
     * @param array $tags Array of tag IDs or Tag objects
     */
    public function attachTags(array $tags): void
    {
        $this->tags()->attach($tags);
    }

    /**
     * Detach tags from the model.
     *
     * @param array $tags Array of tag IDs or Tag objects
     */
    public function detachTags(array $tags): void
    {
        $this->tags()->detach($tags);
    }

    /**
     * Sync tags for the model.
     *
     * @param array $tags Array of tag IDs or Tag objects
     */
    public function syncTags(array $tags): void
    {
        $this->tags()->sync($tags);
    }
}
