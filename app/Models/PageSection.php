<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PageSection
 * 
 * @property int $id
 * @property string|null $guid
 * @property string $name
 * @property string $slug
 * @property string $html
 * @property string|null $css
 * @property string|null $js
 * @property string|null $thumbnail
 * @property string|null $category
 * @property int|null $sort_order
 * @property bool|null $active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int|null $created_by
 *
 * @package App\Models
 */
class PageSection extends Model
{
	protected $table = 'page_sections';

	protected $casts = [
		'sort_order' => 'int',
		'active' => 'bool',
		'created_by' => 'int'
	];

	protected $fillable = [
		'guid',
		'name',
		'slug',
		'html',
		'css',
		'js',
		'thumbnail',
		'category',
		'sort_order',
		'active',
		'created_by'
	];
}
