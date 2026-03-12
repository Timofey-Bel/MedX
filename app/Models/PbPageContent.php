<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PbPageContent
 * 
 * @property int $id
 * @property int $page_id
 * @property int $block_id
 * @property int $sort_order
 * @property string|null $settings
 * @property string|null $html_content
 * @property string|null $css_content
 * @property bool|null $is_visible
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class PbPageContent extends Model
{
	protected $table = 'pb_page_content';

	protected $casts = [
		'page_id' => 'int',
		'block_id' => 'int',
		'sort_order' => 'int',
		'is_visible' => 'bool'
	];

	protected $fillable = [
		'page_id',
		'block_id',
		'sort_order',
		'settings',
		'html_content',
		'css_content',
		'is_visible'
	];
}
