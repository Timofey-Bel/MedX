<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Attribute
 * 
 * @property int $id
 * @property string $product_id
 * @property string $name
 * @property string|null $value
 *
 * @package App\Models
 */
class Attribute extends Model
{
	protected $table = 'attributes';
	public $timestamps = false;

	protected $fillable = [
		'product_id',
		'name',
		'value'
	];
}
