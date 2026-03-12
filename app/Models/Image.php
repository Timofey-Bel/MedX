<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Image
 * 
 * @property string|null $product_id
 * @property string|null $url
 *
 * @package App\Models
 */
class Image extends Model
{
	protected $table = '_images';
	public $incrementing = false;
	public $timestamps = false;

	protected $fillable = [
		'product_id',
		'url'
	];
}
