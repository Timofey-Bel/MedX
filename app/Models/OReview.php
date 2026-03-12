<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OReview
 * 
 * @property int $id
 * @property string $review_id
 * @property int|null $sku
 * @property int|null $rating
 * @property string|null $text
 * @property Carbon|null $date
 * @property string|null $state
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class OReview extends Model
{
	protected $table = 'o_reviews';

	protected $casts = [
		'sku' => 'int',
		'rating' => 'int',
		'date' => 'datetime'
	];

	protected $fillable = [
		'review_id',
		'sku',
		'rating',
		'text',
		'date',
		'state'
	];
}
