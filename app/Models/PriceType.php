<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PriceType
 * 
 * @property string $id
 * @property string $name
 * @property string|null $currency
 * @property string|null $tax_name
 * @property bool|null $tax_included
 *
 * @package App\Models
 */
class PriceType extends Model
{
	protected $table = 'price_types';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'tax_included' => 'bool'
	];

	protected $fillable = [
		'name',
		'currency',
		'tax_name',
		'tax_included'
	];
}
