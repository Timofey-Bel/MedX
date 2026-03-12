<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VOProductCategory
 * 
 * @property int|null $description_category_id
 * @property int|null $type_id
 * @property int $cnt
 *
 * @package App\Models
 */
class VOProductCategory extends Model
{
	protected $table = 'v_o_product_categories';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'description_category_id' => 'int',
		'type_id' => 'int',
		'cnt' => 'int'
	];

	protected $fillable = [
		'description_category_id',
		'type_id',
		'cnt'
	];
}
