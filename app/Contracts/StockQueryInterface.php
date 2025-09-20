<?php

namespace App\Contracts;

interface StockQueryInterface
{
    public function getCurrentStock(): array;
}
