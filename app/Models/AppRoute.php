<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AppRoute
 * 
 * @property int $id
 * @property string $app_id
 * @property string|null $route_type
 * @property string $route_path
 * @property string $module_path
 * @property Carbon|null $created_at
 * 
 * @property InstalledApp $installed_app
 *
 * @package App\Models
 */
class AppRoute extends Model
{
	protected $table = 'app_routes';
	public $timestamps = false;

	protected $fillable = [
		'app_id',
		'route_type',
		'route_path',
		'module_path'
	];

	public function installed_app()
	{
		return $this->belongsTo(InstalledApp::class, 'app_id', 'app_id');
	}
}
