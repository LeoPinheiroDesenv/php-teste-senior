<?php

namespace App\Contracts;

interface FilterValidationInterface
{
    public function validateReportFilters(array $data): array;
}
