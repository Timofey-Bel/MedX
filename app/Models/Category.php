<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Category
 * 
 * @property string $id
 * @property string $name
 * @property string|null $parent_id
 *
 * @package App\Models
 */
class Category extends Model
{
	protected $table = '_categories';
	public $incrementing = false;
	public $timestamps = false;

	protected $fillable = [
		'name',
		'parent_id'
	];
}
