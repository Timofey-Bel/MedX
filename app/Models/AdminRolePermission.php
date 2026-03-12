<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AdminRolePermission
 * 
 * @property int $id
 * @property int $role_id
 * @property int $permission_id
 * @property bool|null $granted
 * 
 * @property AdminRole $admin_role
 * @property AdminPermission $admin_permission
 *
 * @package App\Models
 */
class AdminRolePermission extends Model
{
	protected $table = 'admin_role_permissions';
	public $timestamps = false;

	protected $casts = [
		'role_id' => 'int',
		'permission_id' => 'int',
		'granted' => 'bool'
	];

	protected $fillable = [
		'role_id',
		'permission_id',
		'granted'
	];

	public function admin_role()
	{
		return $this->belongsTo(AdminRole::class, 'role_id');
	}

	public function admin_permission()
	{
		return $this->belongsTo(AdminPermission::class, 'permission_id');
	}
}
