<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Page
 * 
 * @property int $id
 * @property bool|null $active
 * @property string $name
 * @property string|null $title
 * @property int|null $parent_id
 * @property string|null $content
 * @property int|null $sort
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Page extends Model
{
	protected $table = 'pages';

	protected $casts = [
		'active' => 'bool',
		'parent_id' => 'int',
		'sort' => 'int'
	];

	protected $fillable = [
		'active',
		'name',
		'title',
		'parent_id',
		'content',
		'sort'
	];
}
