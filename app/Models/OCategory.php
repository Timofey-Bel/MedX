<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OCategory
 * 
 * @property int $id
 * @property int $description_category_id
 * @property string $category_name
 * @property int|null $type_id
 * @property string|null $type_name
 * @property int|null $parent_id
 * @property bool|null $disabled
 * @property int|null $level
 * @property string|null $path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class OCategory extends Model
{
	protected $table = 'o_categories';

	protected $casts = [
		'description_category_id' => 'int',
		'type_id' => 'int',
		'parent_id' => 'int',
		'disabled' => 'bool',
		'level' => 'int'
	];

	protected $fillable = [
		'description_category_id',
		'category_name',
		'type_id',
		'type_name',
		'parent_id',
		'disabled',
		'level',
		'path'
	];
}
