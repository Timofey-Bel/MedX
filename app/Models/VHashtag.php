<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VHashtag
 * 
 * @property string $value
 * @property int $cnt
 *
 * @package App\Models
 */
class VHashtag extends Model
{
	protected $table = 'v_hashtags';
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
