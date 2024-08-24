<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @method static Builder|FavouriteLocation newModelQuery()
 * @method static Builder|FavouriteLocation newQuery()
 * @method static Builder|FavouriteLocation query()
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $user_id
 * @property int $location_id
 * @method static Builder|FavouriteLocation whereCreatedAt($value)
 * @method static Builder|FavouriteLocation whereId($value)
 * @method static Builder|FavouriteLocation whereLocationId($value)
 * @method static Builder|FavouriteLocation whereUpdatedAt($value)
 * @method static Builder|FavouriteLocation whereUserId($value)
 * @mixin Eloquent
 */
class FavouriteLocation extends Model
{
    use HasFactory;

	protected $guarded = [];
}
