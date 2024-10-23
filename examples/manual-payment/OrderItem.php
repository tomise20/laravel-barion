<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *  The "order item" model which corresponds to "transaction item". 
 */
class OrderItem extends Model
{
    protected $fillable = [];

	public $barion_casts = [
		"name" => "name",
		"description" => "description",
		"quantity" => "quantity",
		"unit" => "unit",
		"unit_price" => "unit_price",
		"item_total" => "item_total",
	];
}
