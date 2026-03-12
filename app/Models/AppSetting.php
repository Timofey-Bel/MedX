<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AppSetting
 * 
 * @property int $id
 * @property string $app_id
 * @property string $setting_key
 * @property string|null $setting_value
 * @property string|null $setting_type
 * @property Carbon|null $updated_at
 * 
 * @property InstalledApp $installed_app
 *
 * @package App\Models
 */
class AppSetting extends Model
{
	protected $table = 'app_settings';
	public $timestamps = false;

	protected $fillable = [
		'app_id',
		'setting_key',
		'setting_value',
		'setting_type'
	];

	public function installed_app()
	{
		return $this->belongsTo(InstalledApp::class, 'app_id', 'app_id');
	}
}
