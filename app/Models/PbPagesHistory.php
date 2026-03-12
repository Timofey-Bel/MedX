<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PbPagesHistory
 * 
 * @property int $id
 * @property int $page_id
 * @property int|null $user_id
 * @property string|null $action
 * @property string|null $snapshot
 * @property Carbon|null $created_at
 *
 * @package App\Models
 */
class PbPagesHistory extends Model
{
	protected $table = 'pb_pages_history';
	public $timestamps = false;

	protected $casts = [
		'page_id' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'page_id',
		'user_id',
		'action',
		'snapshot'
	];
}
