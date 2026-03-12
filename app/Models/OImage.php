<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OImage
 * 
 * @property int $id
 * @property int $product_id
 * @property string $image_url
 * @property int|null $image_order
 * @property Carbon|null $created_at
 *
 * @package App\Models
 */
class OImage extends Model
{
	protected $table = 'o_images';
	public $timestamps = false;

	protected $casts = [
		'product_id' => 'int',
		'image_order' => 'int'
	];

	protected $fillable = [
		'product_id',
		'image_url',
		'image_order'
	];
}
