<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Age
 * 
 * @property int $id
 * @property string $product_id
 * @property string $age
 *
 * @package App\Models
 */
class Age extends Model
{
	protected $table = 'ages';
	public $timestamps = false;

	protected $fillable = [
		'product_id',
		'age'
	];
}
