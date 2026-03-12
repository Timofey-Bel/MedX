<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Log404
 * 
 * @property int $id
 * @property Carbon|null $dt
 * @property string|null $text
 * @property string|null $ip
 *
 * @package App\Models
 */
class Log404 extends Model
{
	protected $table = 'log_404';
	public $timestamps = false;

	protected $casts = [
		'dt' => 'datetime'
	];

	protected $fillable = [
		'dt',
		'text',
		'ip'
	];
}
