<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

/**
 * Class User
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string $user_type
 * @property int|null $org_id
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Organization|null $organization
 *
 * @package App\Models
 */
class User extends Model implements Authenticatable
{
	use AuthenticatableTrait;
	
	protected $table = 'users';

	/**
	 * Типы пользователей
	 */
	const TYPE_RETAIL = 'retail';
	const TYPE_WHOLESALE = 'wholesale';

	protected $casts = [
		'email_verified_at' => 'datetime',
		'org_id' => 'integer',
	];

	protected $hidden = [
		'password',
		'remember_token'
	];

	protected $fillable = [
		'name',
		'email',
		'phone',
		'user_type',
		'org_id',
		'email_verified_at',
		'password',
		'remember_token'
	];

	/**
	 * Получить организацию пользователя (для оптовых покупателей)
	 */
	public function organization()
	{
		return $this->belongsTo(Organization::class, 'org_id');
	}

	/**
	 * Проверить, является ли пользователь розничным покупателем
	 */
	public function isRetail(): bool
	{
		return $this->user_type === self::TYPE_RETAIL;
	}

	/**
	 * Проверить, является ли пользователь оптовым покупателем
	 */
	public function isWholesale(): bool
	{
		return $this->user_type === self::TYPE_WHOLESALE;
	}

	/**
	 * Получить название типа пользователя
	 */
	public function getUserTypeNameAttribute(): string
	{
		return match ($this->user_type) {
			self::TYPE_RETAIL => 'Розничный покупатель',
			self::TYPE_WHOLESALE => 'Оптовый покупатель',
			default => 'Неизвестный тип',
		};
	}
}
