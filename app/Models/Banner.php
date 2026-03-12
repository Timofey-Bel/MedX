<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Banner
 * 
 * @property int $id
 * @property string|null $url
 * @property string|null $name
 * @property int $sort
 * @property string|null $title
 *
 * @package App\Models
 */
class Banner extends Model
{
	protected $table = 'banners';
	public $timestamps = false;

	protected $casts = [
		'sort' => 'int'
	];

	protected $fillable = [
		'url',
		'name',
		'sort',
		'title'
	];
}
