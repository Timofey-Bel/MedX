<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InstalledApp
 * 
 * @property int $id
 * @property string $app_id
 * @property string $name
 * @property string|null $description
 * @property string $version
 * @property string|null $author
 * @property string|null $icon
 * @property string|null $icon_color
 * @property string|null $category
 * @property string|null $status
 * @property Carbon|null $installed_at
 * @property Carbon|null $updated_at
 * @property string|null $package_path
 * 
 * @property Collection|AppDesktopShortcut[] $app_desktop_shortcuts
 * @property Collection|AppRoute[] $app_routes
 * @property Collection|AppSetting[] $app_settings
 *
 * @package App\Models
 */
class InstalledApp extends Model
{
	protected $table = 'installed_apps';
	public $timestamps = false;

	protected $casts = [
		'installed_at' => 'datetime'
	];

	protected $fillable = [
		'app_id',
		'name',
		'description',
		'version',
		'author',
		'icon',
		'icon_color',
		'category',
		'status',
		'installed_at',
		'package_path'
	];

	public function app_desktop_shortcuts()
	{
		return $this->hasMany(AppDesktopShortcut::class, 'app_id', 'app_id');
	}

	public function app_routes()
	{
		return $this->hasMany(AppRoute::class, 'app_id', 'app_id');
	}

	public function app_settings()
	{
		return $this->hasMany(AppSetting::class, 'app_id', 'app_id');
	}
}
