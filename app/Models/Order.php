<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Order
 * 
 * @property int $id
 * @property string|null $order_code
 * @property Carbon|null $date_init
 * @property int|null $status
 * @property float|null $full_sum
 * @property float|null $discount_sum
 * @property float|null $pay_sum
 * @property float|null $bonus
 * @property float|null $cart_weight
 * @property float|null $cart_volume
 * @property float|null $cart_density
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $comment_user
 * @property string|null $tracking_id
 * @property string|null $checkoutOrderId
 * @property int|null $user_id
 * @property string|null $user_role
 * @property string|null $user_card_code
 * @property string|null $ip
 * @property string|null $user_agent
 *
 * @package App\Models
 */
class Order extends Model
{
	protected $table = 'orders';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'date_init' => 'datetime',
		'status' => 'int',
		'full_sum' => 'float',
		'discount_sum' => 'float',
		'pay_sum' => 'float',
		'bonus' => 'float',
		'cart_weight' => 'float',
		'cart_volume' => 'float',
		'cart_density' => 'float',
		'user_id' => 'int'
	];

	protected $fillable = [
		'order_code',
		'date_init',
		'status',
		'full_sum',
		'discount_sum',
		'pay_sum',
		'bonus',
		'cart_weight',
		'cart_volume',
		'cart_density',
		'name',
		'phone',
		'email',
		'comment_user',
		'tracking_id',
		'checkoutOrderId',
		'user_id',
		'user_role',
		'user_card_code',
		'ip',
		'user_agent'
	];
}
