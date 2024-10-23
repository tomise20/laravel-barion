<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *  The "order" model which corresponds to "transaction". 
 */
class Order extends Model
{

    protected $fillable = [];

	public $casts = [];

	public $barion_casts = [
		// base data
		'payment_request_id' => 'id',
		'payer_hint' => 'email',
		'order_number' => 'reservation_number',
		'phone_number' => 'phone_number',
		'total' => 'total_price',
	];
}
