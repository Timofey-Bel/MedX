<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VGod
 * 
 * @property string|null $value
 * @property int $cnt
 *
 * @package App\Models
 */
class VGod extends Model
{
	protected $table = 'v_god';
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
