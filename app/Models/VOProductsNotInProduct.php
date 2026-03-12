<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VOProductsNotInProduct
 * 
 * @property int $product_id
 * @property string $offer_id
 * @property string|null $name
 * @property string|null $barcode
 *
 * @package App\Models
 */
class VOProductsNotInProduct extends Model
{
	protected $table = 'v_o_products_not_in_products';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'product_id' => 'int'
	];

	protected $fillable = [
		'product_id',
		'offer_id',
		'name',
		'barcode'
	];
}
