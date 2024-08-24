<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property string $description
 * @property string $key
 * @property string|null $value
 * @method static Builder|Setting newModelQuery()
 * @method static Builder|Setting newQuery()
 * @method static Builder|Setting query()
 * @method static Builder|Setting whereDescription($value)
 * @method static Builder|Setting whereKey($value)
 * @method static Builder|Setting whereName($value)
 * @method static Builder|Setting whereValue($value)
 * @mixin Eloquent
 */
class Setting extends Model
{
    use HasFactory;

	public $timestamps = false;

	protected $primaryKey = 'key';

	protected $keyType = 'string';

	protected $fillable = ['key', 'value'];

	public $incrementing = false;
}
