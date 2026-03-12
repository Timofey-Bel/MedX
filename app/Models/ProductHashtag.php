<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductHashtag
 * 
 * @property int $id
 * @property string $product_id
 * @property string $value
 * @property Carbon $created_at
 *
 * @package App\Models
 */
class ProductHashtag extends Model
{
	protected $table = 'product_hashtags';
	public $timestamps = false;

	protected $fillable = [
		'product_id',
		'value'
	];
}
