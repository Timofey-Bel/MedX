<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AdminUserModuleAccess
 * 
 * @property int $id
 * @property int $user_id
 * @property string $module
 * @property bool $access
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property AdminUser $admin_user
 *
 * @package App\Models
 */
class AdminUserModuleAccess extends Model
{
	protected $table = 'admin_user_module_access';

	protected $casts = [
		'user_id' => 'int',
		'access' => 'bool'
	];

	protected $fillable = [
		'user_id',
		'module',
		'access'
	];

	public function admin_user()
	{
		return $this->belongsTo(AdminUser::class, 'user_id');
	}
}
