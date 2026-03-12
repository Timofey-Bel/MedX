<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ImportedItem
 * 
 * @property int|null $id
 * @property string|null $name
 * @property string|null $offer_id
 * @property string|null $primary_image
 * @property float|null $price
 * @property float|null $volume_weight
 * @property string|null $images
 * @property string|null $sku
 *
 * @package App\Models
 */
class ImportedItem extends Model
{
	protected $table = '_imported_items';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'price' => 'float',
		'volume_weight' => 'float'
	];

	protected $fillable = [
		'id',
		'name',
		'offer_id',
		'primary_image',
		'price',
		'volume_weight',
		'images',
		'sku'
	];
}
