<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CartSession
 * 
 * @property int $id
 * @property string $session_id
 * @property string|null $session
 * @property string|null $ip
 * @property int|null $cart_amount
 * @property float|null $cart_sum
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class CartSession extends Model
{
	protected $table = '_cart_sessions';

	protected $casts = [
		'cart_amount' => 'int',
		'cart_sum' => 'float'
	];

	protected $fillable = [
		'session_id',
		'session',
		'ip',
		'cart_amount',
		'cart_sum'
	];
}
