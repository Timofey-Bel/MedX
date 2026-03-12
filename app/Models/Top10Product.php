<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Top10Product
 * 
 * @property int $id
 * @property string $product_id
 * @property int $sort
 * @property bool $active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Top10Product extends Model
{
	protected $table = 'top10_products';

	protected $casts = [
		'sort' => 'int',
		'active' => 'bool'
	];

	protected $fillable = [
		'product_id',
		'sort',
		'active'
	];
}
