<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\Tag as SpatieTag;

class Tag extends SpatieTag
{

    /**
     * Get the taggables for a specific user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public static function tagsForUserAndModel($userId, $modelType)
    {
        return static::whereHas('taggables', function ($query) use ($userId, $modelType) {
            $query->where('user_id', $userId)
                  ->where('taggable_type', $modelType);
        })->get();
    }

    // ...

    /**
     * Get all of the taggable models that are assigned this tag.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function taggables()
    {
        return $this->morphedByMany(
            config('tags.taggable_model'),
            'taggable',
            'taggables',
            'tag_id',
            'taggable_id'
        );
    }

    /**
     * Get all of the taggable models that are assigned this tag for a specific user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public static function investmentTags($userId)
    {
        config(['tags.taggable_model' => Investment::class]);
        return static::whereHas('taggables', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('taggable_type', Investment::class);
        })->get();
    }

    /**
     * Get all of the taggable models that are assigned this tag for a specific user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public static function investorTags($userId)
    {
        config(['tags.taggable_model' => Investor::class]);
        return static::whereHas('taggables', function ($query) use ($userId) {
            $query->where('taggables.user_id', $userId)
                  ->where('taggable_type', Investor::class);
        })->get();
    }

}
