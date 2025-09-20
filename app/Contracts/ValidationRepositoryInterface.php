<?php

namespace App\Contracts;

interface ValidationRepositoryInterface
{
    public function validateInventoryInput(array $data): array;
    public function validateSaleInput(array $data): array;
    public function validateReportFilters(array $data): array;
}
