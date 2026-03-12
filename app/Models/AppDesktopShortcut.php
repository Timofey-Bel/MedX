<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AppDesktopShortcut
 * 
 * @property int $id
 * @property string $app_id
 * @property string $title
 * @property string $icon
 * @property string|null $icon_color
 * @property string $function_name
 * @property int|null $sort_order
 * @property bool|null $enabled
 * @property Carbon|null $created_at
 * @property bool|null $show_on_desktop
 * @property bool|null $show_in_quick_access
 * 
 * @property InstalledApp $installed_app
 *
 * @package App\Models
 */
class AppDesktopShortcut extends Model
{
	protected $table = 'app_desktop_shortcuts';
	public $timestamps = false;

	protected $casts = [
		'sort_order' => 'int',
		'enabled' => 'bool',
		'show_on_desktop' => 'bool',
		'show_in_quick_access' => 'bool'
	];

	protected $fillable = [
		'app_id',
		'title',
		'icon',
		'icon_color',
		'function_name',
		'sort_order',
		'enabled',
		'show_on_desktop',
		'show_in_quick_access'
	];

	public function installed_app()
	{
		return $this->belongsTo(InstalledApp::class, 'app_id', 'app_id');
	}
}
