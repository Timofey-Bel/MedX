<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ImportReviewsLog
 * 
 * @property int $id
 * @property string|null $last_id
 * @property int|null $page_number
 * @property int|null $imported_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class ImportReviewsLog extends Model
{
	protected $table = 'import_reviews_log';

	protected $casts = [
		'page_number' => 'int',
		'imported_count' => 'int'
	];

	protected $fillable = [
		'last_id',
		'page_number',
		'imported_count'
	];
}
