<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Point
 * 
 * @property string|null $id
 * @property float|null $la
 * @property float|null $lo
 * @property string|null $json
 *
 * @package App\Models
 */
class Point extends Model
{
	protected $table = 'points';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'la' => 'float',
		'lo' => 'float'
	];

	protected $fillable = [
		'id',
		'la',
		'lo',
		'json'
	];
}
