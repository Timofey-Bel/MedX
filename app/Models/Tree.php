<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Tree
 * 
 * @property string $id
 * @property string $name
 * @property string|null $parent_id
 *
 * @package App\Models
 */
class Tree extends Model
{
	protected $table = 'tree';
	public $incrementing = false;
	public $timestamps = false;

	protected $fillable = [
		'name',
		'parent_id'
	];
}
