<?php

namespace App\Contracts;

interface InputValidationInterface
{
    public function validateInventoryInput(array $data): array;
    public function validateSaleInput(array $data): array;
}
