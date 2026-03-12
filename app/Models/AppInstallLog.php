<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AppInstallLog
 * 
 * @property int $id
 * @property string $app_id
 * @property string $action
 * @property string $status
 * @property string|null $message
 * @property string|null $log_data
 * @property Carbon|null $created_at
 *
 * @package App\Models
 */
class AppInstallLog extends Model
{
	protected $table = 'app_install_logs';
	public $timestamps = false;

	protected $fillable = [
		'app_id',
		'action',
		'status',
		'message',
		'log_data'
	];
}
