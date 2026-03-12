<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VSeriya
 * 
 * @property int $id
 * @property string|null $value
 * @property int $cnt
 *
 * @package App\Models
 */
class VSeriya extends Model
{
	protected $table = 'v_seriya';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'cnt' => 'int'
	];

	protected $fillable = [
		'id',
		'value',
		'cnt'
	];
}
