<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $name
 * @property string $code
 * @property int $type
 * @property string $latitude
 * @property string $longitude
 * @property int|null $parent_id
 * @property string|null $slug
 * @property-read Collection<int, Location> $children
 * @property-read int|null $children_count
 * @property-read Location|null $parent
 * @property-read Collection<int, Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read Collection<int, Weather> $weather
 * @property-read int|null $weather_count
 * @method static Builder|Location newModelQuery()
 * @method static Builder|Location newQuery()
 * @method static Builder|Location query()
 * @method static Builder|Location whereCode($value)
 * @method static Builder|Location whereCreatedAt($value)
 * @method static Builder|Location whereId($value)
 * @method static Builder|Location whereLatitude($value)
 * @method static Builder|Location whereLongitude($value)
 * @method static Builder|Location whereName($value)
 * @method static Builder|Location whereParentId($value)
 * @method static Builder|Location whereSlug($value)
 * @method static Builder|Location whereType($value)
 * @method static Builder|Location whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Location extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    public function weather(): HasMany
    {
        return $this->hasMany(Weather::class);
    }

	public function reviews(): HasMany
	{
		return $this->hasMany(Review::class);
	}
}
