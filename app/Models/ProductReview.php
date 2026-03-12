<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductReview
 * 
 * @property int $id
 * @property string $name
 * @property string $title
 * @property string|null $image
 * @property string|null $content
 * @property int $parent_id
 * @property int $sort
 * @property bool $active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class ProductReview extends Model
{
	protected $table = 'product_reviews';

	protected $casts = [
		'parent_id' => 'int',
		'sort' => 'int',
		'active' => 'bool'
	];

	protected $fillable = [
		'name',
		'title',
		'image',
		'content',
		'parent_id',
		'sort',
		'active'
	];
}
