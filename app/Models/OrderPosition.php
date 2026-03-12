<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderPosition
 * 
 * @property int $id
 * @property Carbon|null $created
 * @property int|null $order_num
 * @property string|null $order_code
 * @property int|null $pieces
 * @property int|null $min
 * @property float|null $bill
 * @property float|null $cost
 * @property float|null $piece_cost
 * @property float|null $amount
 * @property float|null $sum
 * @property string|null $art
 * @property string|null $guid
 * @property string|null $title
 * @property string|null $model
 * @property float|null $weight
 * @property float|null $w
 * @property float|null $l
 * @property float|null $h
 * @property float|null $volume
 * @property float|null $piece_weight
 *
 * @package App\Models
 */
class OrderPosition extends Model
{
	protected $table = 'order_positions';
	public $timestamps = false;

	protected $casts = [
		'created' => 'datetime',
		'order_num' => 'int',
		'pieces' => 'int',
		'min' => 'int',
		'bill' => 'float',
		'cost' => 'float',
		'piece_cost' => 'float',
		'amount' => 'float',
		'sum' => 'float',
		'weight' => 'float',
		'w' => 'float',
		'l' => 'float',
		'h' => 'float',
		'volume' => 'float',
		'piece_weight' => 'float'
	];

	protected $fillable = [
		'created',
		'order_num',
		'order_code',
		'pieces',
		'min',
		'bill',
		'cost',
		'piece_cost',
		'amount',
		'sum',
		'art',
		'guid',
		'title',
		'model',
		'weight',
		'w',
		'l',
		'h',
		'volume',
		'piece_weight'
	];
}
