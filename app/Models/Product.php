<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 * 
 * @property string $id
 * @property string|null $category_id
 * @property string|null $sku
 * @property string|null $code
 * @property string $name
 * @property string|null $description
 * @property string|null $picture
 * @property string|null $base_unit
 * @property int|null $quantity
 * @property float|null $weight
 * @property bool|null $is_new
 * @property array|null $attributes_json
 *
 * @package App\Models
 */
class Product extends Model
{
	protected $table = 'products';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'quantity' => 'int',
		'weight' => 'float',
		'is_new' => 'bool',
		'attributes_json' => 'json'
	];

	protected $fillable = [
		'category_id',
		'sku',
		'code',
		'name',
		'description',
		'picture',
		'base_unit',
		'quantity',
		'weight',
		'is_new',
		'attributes_json'
	];
}
