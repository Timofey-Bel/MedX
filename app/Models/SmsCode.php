<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SmsCode
 * 
 * @property int $id
 * @property string|null $phone
 * @property string|null $email
 * @property string $type
 * @property string $code
 * @property Carbon|null $created_at
 *
 * @package App\Models
 */
class SmsCode extends Model
{
	protected $table = 'sms_codes';
	public $timestamps = false;

	protected $fillable = [
		'phone',
		'email',
		'type',
		'code'
	];
}
