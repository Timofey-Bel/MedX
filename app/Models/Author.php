<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Author
 * 
 * @property int $id
 * @property string $product_id
 * @property string $author_name
 *
 * @package App\Models
 */
class Author extends Model
{
	protected $table = 'authors';
	public $timestamps = false;

	protected $fillable = [
		'product_id',
		'author_name'
	];
}
