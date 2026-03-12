<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AdminUser
 * 
 * @property int $id
 * @property string $login
 * @property string $password
 * @property string|null $name
 * @property string|null $email
 * @property string|null $role
 * @property bool|null $active
 * @property Carbon|null $last_login
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|AdminUserModuleAccess[] $admin_user_module_accesses
 *
 * @package App\Models
 */
class AdminUser extends Model
{
	protected $table = 'admin_users';

	protected $casts = [
		'active' => 'bool',
		'last_login' => 'datetime'
	];

	protected $hidden = [
		'password'
	];

	protected $fillable = [
		'login',
		'password',
		'name',
		'email',
		'role',
		'active',
		'last_login'
	];

	public function admin_user_module_accesses()
	{
		return $this->hasMany(AdminUserModuleAccess::class, 'user_id');
	}
}
