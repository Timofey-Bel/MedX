<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AdminPermission
 * 
 * @property int $id
 * @property string $module
 * @property string $action
 * @property string $title
 * @property string|null $description
 * 
 * @property Collection|AdminRolePermission[] $admin_role_permissions
 *
 * @package App\Models
 */
class AdminPermission extends Model
{
	protected $table = 'admin_permissions';
	public $timestamps = false;

	protected $fillable = [
		'module',
		'action',
		'title',
		'description'
	];

	public function admin_role_permissions()
	{
		return $this->hasMany(AdminRolePermission::class, 'permission_id');
	}
}
