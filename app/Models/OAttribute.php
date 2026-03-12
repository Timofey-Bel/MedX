<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class OAttribute
 * 
 * @property int $id
 * @property string $product_id
 * @property string $dictionary_value_id
 * @property string|null $value
 *
 * @package App\Models
 */
class OAttribute extends Model
{
	protected $table = 'o_attributes';
	public $timestamps = false;

	protected $fillable = [
		'product_id',
		'dictionary_value_id',
		'value'
	];
}
