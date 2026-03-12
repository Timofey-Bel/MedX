<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductProperty
 * 
 * @property string $id
 * @property string $name
 * @property string|null $value_type
 *
 * @package App\Models
 */
class ProductProperty extends Model
{
	protected $table = 'product_properties';
	public $incrementing = false;
	public $timestamps = false;

	protected $fillable = [
		'name',
		'value_type'
	];
}
