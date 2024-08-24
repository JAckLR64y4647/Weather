<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @method static Builder|LocationView newModelQuery()
 * @method static Builder|LocationView newQuery()
 * @method static Builder|LocationView query()
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $user_id
 * @property int $location_id
 * @method static Builder|LocationView whereCreatedAt($value)
 * @method static Builder|LocationView whereId($value)
 * @method static Builder|LocationView whereLocationId($value)
 * @method static Builder|LocationView whereUpdatedAt($value)
 * @method static Builder|LocationView whereUserId($value)
 * @mixin Eloquent
 */
class LocationView extends Model
{
    use HasFactory;

	protected $guarded = [];
}
