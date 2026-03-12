<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Import
 * 
 * @property string|null $json
 * @property float|null $product_id
 * @property string|null $offer_id
 * @property bool|null $has_fbo_stocks
 * @property bool|null $has_fbs_stocks
 * @property bool|null $archived
 * @property bool|null $is_discounted
 * @property string|null $quants
 *
 * @package App\Models
 */
class Import extends Model
{
	protected $table = '_import';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'product_id' => 'float',
		'has_fbo_stocks' => 'bool',
		'has_fbs_stocks' => 'bool',
		'archived' => 'bool',
		'is_discounted' => 'bool'
	];

	protected $fillable = [
		'json',
		'product_id',
		'offer_id',
		'has_fbo_stocks',
		'has_fbs_stocks',
		'archived',
		'is_discounted',
		'quants'
	];
}
