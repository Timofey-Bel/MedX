<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PbBlocksLibrary
 * 
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $category
 * @property string|null $description
 * @property string|null $thumbnail
 * @property string $html_template
 * @property string|null $css_template
 * @property string|null $js_template
 * @property string|null $default_settings
 * @property bool|null $is_system
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class PbBlocksLibrary extends Model
{
	protected $table = 'pb_blocks_library';

	protected $casts = [
		'is_system' => 'bool'
	];

	protected $fillable = [
		'code',
		'name',
		'category',
		'description',
		'thumbnail',
		'html_template',
		'css_template',
		'js_template',
		'default_settings',
		'is_system'
	];
}
