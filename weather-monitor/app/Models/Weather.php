<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $location_id
 * @property float $temperature
 * @property float $humidity
 * @property float $pressure
 * @property float $wind_speed
 * @property int $wind_direction
 * @property int $type
 * @property Carbon|null $forecasted_at
 * @property-read Location|null $location
 * @method static Builder|Weather newModelQuery()
 * @method static Builder|Weather newQuery()
 * @method static Builder|Weather query()
 * @method static Builder|Weather whereCreatedAt($value)
 * @method static Builder|Weather whereForecastedAt($value)
 * @method static Builder|Weather whereHumidity($value)
 * @method static Builder|Weather whereId($value)
 * @method static Builder|Weather whereLocationId($value)
 * @method static Builder|Weather wherePressure($value)
 * @method static Builder|Weather whereTemperature($value)
 * @method static Builder|Weather whereType($value)
 * @method static Builder|Weather whereUpdatedAt($value)
 * @method static Builder|Weather whereWindDirection($value)
 * @method static Builder|Weather whereWindSpeed($value)
 * @mixin Eloquent
 */
class Weather extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'forecasted_at' => 'datetime',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
