<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VOAttribute
 * 
 * @property int $id
 * @property string $product_id
 * @property string $dictionary_value_id
 * @property string $name
 * @property string|null $value
 *
 * @package App\Models
 */
class VOAttribute extends Model
{
	protected $table = 'v_o_attributes';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int'
	];

	protected $fillable = [
		'id',
		'product_id',
		'dictionary_value_id',
		'name',
		'value'
	];
}
