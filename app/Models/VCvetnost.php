<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VCvetnost
 * 
 * @property string|null $value
 * @property int $cnt
 *
 * @package App\Models
 */
class VCvetnost extends Model
{
	protected $table = 'v_cvetnost';
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
