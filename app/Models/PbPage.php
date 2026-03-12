<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PbPage
 * 
 * @property int $id
 * @property string $slug
 * @property string $title
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $published_at
 * @property int|null $author_id
 * @property int|null $sort_order
 *
 * @package App\Models
 */
class PbPage extends Model
{
	protected $table = 'pb_pages';

	protected $casts = [
		'published_at' => 'datetime',
		'author_id' => 'int',
		'sort_order' => 'int'
	];

	protected $fillable = [
		'slug',
		'title',
		'meta_title',
		'meta_description',
		'meta_keywords',
		'status',
		'published_at',
		'author_id',
		'sort_order'
	];
}
