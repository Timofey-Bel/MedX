<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PopularCategory
 * 
 * @property int $id
 * @property string $category_id
 * @property int $sort
 * @property string|null $image
 * @property bool $active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class PopularCategory extends Model
{
	protected $table = 'popular_categories';

	protected $casts = [
		'sort' => 'int',
		'active' => 'bool'
	];

	protected $fillable = [
		'category_id',
		'sort',
		'image',
		'active'
	];
}
