<?php

declare(strict_types=1);

namespace Tomise\Barion\Services;

use Illuminate\Database\Eloquent\Model;
use Tomise\Barion\Contracts\IPaymentService;
use Tomise\Barion\Exceptions\BarionCastException;

abstract class AbstractBarionService implements IPaymentService
{
    protected function checkModelBarionProperty(Model $model): void
    {
        if(!property_exists($model, 'barion_casts')) {
            Throw new BarionCastException('Model does not have barion_casts property');
        }
    }
}