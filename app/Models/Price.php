<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Price
 * 
 * @property int $id
 * @property string $product_id
 * @property string $price_type_id
 * @property float $price
 * @property string|null $currency
 * @property string|null $representation
 *
 * @package App\Models
 */
class Price extends Model
{
	protected $table = 'prices';
	public $timestamps = false;

	protected $casts = [
		'price' => 'float'
	];

	protected $fillable = [
		'product_id',
		'price_type_id',
		'price',
		'currency',
		'representation'
	];
}
