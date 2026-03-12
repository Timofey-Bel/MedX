<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AdminRole
 * 
 * @property int $id
 * @property string $name
 * @property string $title
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|AdminRolePermission[] $admin_role_permissions
 *
 * @package App\Models
 */
class AdminRole extends Model
{
	protected $table = 'admin_roles';

	protected $fillable = [
		'name',
		'title',
		'description'
	];

	public function admin_role_permissions()
	{
		return $this->hasMany(AdminRolePermission::class, 'role_id');
	}
}
