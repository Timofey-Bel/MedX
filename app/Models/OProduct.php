<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OProduct
 * 
 * @property int $id
 * @property int $product_id
 * @property string $offer_id
 * @property string|null $barcode
 * @property int|null $sku
 * @property string|null $name
 * @property float|null $height
 * @property float|null $depth
 * @property float|null $width
 * @property string|null $dimension_unit
 * @property float|null $weight
 * @property string|null $weight_unit
 * @property int|null $description_category_id
 * @property int|null $type_id
 * @property string|null $primary_image
 * @property bool|null $has_fbo_stocks
 * @property bool|null $has_fbs_stocks
 * @property bool|null $archived
 * @property bool|null $is_discounted
 * @property string|null $quants
 * @property string|null $product_data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class OProduct extends Model
{
	protected $table = 'o_products';

	protected $casts = [
		'product_id' => 'int',
		'sku' => 'int',
		'height' => 'float',
		'depth' => 'float',
		'width' => 'float',
		'weight' => 'float',
		'description_category_id' => 'int',
		'type_id' => 'int',
		'has_fbo_stocks' => 'bool',
		'has_fbs_stocks' => 'bool',
		'archived' => 'bool',
		'is_discounted' => 'bool'
	];

	protected $fillable = [
		'product_id',
		'offer_id',
		'barcode',
		'sku',
		'name',
		'height',
		'depth',
		'width',
		'dimension_unit',
		'weight',
		'weight_unit',
		'description_category_id',
		'type_id',
		'primary_image',
		'has_fbo_stocks',
		'has_fbs_stocks',
		'archived',
		'is_discounted',
		'quants',
		'product_data'
	];
}
