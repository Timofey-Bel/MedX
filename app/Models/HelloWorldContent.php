<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HelloWorldContent
 * 
 * @property int $id
 * @property string $content
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class HelloWorldContent extends Model
{
	protected $table = 'hello_world_content';
	public $timestamps = false;

	protected $fillable = [
		'content'
	];
}
