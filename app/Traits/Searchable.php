<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Trait Searchable
 * Simple trait to make models searchable through standard DB connection
 *
 * Fulltext search settings (for MySQL 5.6+ using InnoDB or MyISAM)
 * @property bool $useFullText
 * @property string $fullTextColumns
 */
trait Searchable
{
    /**
     * Returns an array of fields.
     *
     * Required so we know which columns and relations to search.
     * Use dot-notation for relation field search, e.g. 'user.name' (only supports one level deep)
     *
     * @return array
     */
    abstract public function searchableAttributes(): array;

    /**
     * @param Builder $query
     * @param $searchTerm
     * @return mixed
     */
    public function scopeSearch(Builder $query, $searchTerm)
    {
        // Get the searchable attributes
        $attributes = $this->searchableAttributes();

        // Check if using fulltext
        $fullText = $this->fullTextColumns && $this->useFullText;

        // Execute the query
        $query->where(function(Builder $query) use ($attributes, $searchTerm, $fullText) {
            // Use array wrap to make sure we're dealing with an array
            // Info: https://laravel.com/docs/5.8/helpers#method-array-wrap
            foreach (Arr::wrap($attributes) as $attribute) {
                // Make sure it's at least two-characters long
                if (strlen($attribute) < 2) {
                    continue;
                }

                // Check if querying relationship
                $isRelation = Str::contains($attribute, '.');

                // Relation query
                $query->when($isRelation,
                    // If string contains '.', it's a relationship, perform nested search
                    function(Builder $query) use ($attribute, $searchTerm) {
                        // Get the table and column (only single level deep, will not search nested relations)
                        [$relationName, $relationAttribute] = explode('.', $attribute);

                        $query->orWhereHas($relationName, function(Builder $query) use ($relationAttribute, $searchTerm) {
                            $query->where($relationAttribute, 'LIKE', "%{$searchTerm}%");
                        });
                    }
                );

                // Model query
                $query->when(!($isRelation || $fullText),
                    function(Builder $query) use ($attribute, $searchTerm) {
                        $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                    }
                );
            }

            // Use boolean search for base model search
            // Info: https://dev.mysql.com/doc/refman/8.0/en/fulltext-boolean.html
            if ($fullText) {
                $against = $this->fullTextColumns;
                $terms = explode(' ', $searchTerm);
                $searchTerm = [];
                foreach ($terms as $term) {
                    $searchTerm[] = "$term*";
                }
                $searchTerm = implode(' ', $searchTerm);
                $query->orWhereRaw("MATCH ($against) AGAINST ('$searchTerm' IN BOOLEAN MODE)");
            }
        });

        // Return the query
        return $query;
    }
}
