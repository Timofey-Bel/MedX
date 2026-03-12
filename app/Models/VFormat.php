<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VFormat
 * 
 * @property string|null $value
 * @property int $cnt
 *
 * @package App\Models
 */
class VFormat extends Model
{
	protected $table = 'v_format';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'cnt' => 'int'
	];

	protected $fillable = [
		'value',
		'cnt'
	];
}
