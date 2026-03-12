<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VProductsOProduct
 * 
 * @property int $product_id
 * @property string $offer_id
 * @property int|null $sku
 *
 * @package App\Models
 */
class VProductsOProduct extends Model
{
	protected $table = 'v_products_o_products';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'product_id' => 'int',
		'sku' => 'int'
	];

	protected $fillable = [
		'product_id',
		'offer_id',
		'sku'
	];
}
