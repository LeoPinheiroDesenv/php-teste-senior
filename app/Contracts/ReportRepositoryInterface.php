<?php

namespace App\Contracts;

interface ReportRepositoryInterface
{
    public function generateSalesReport(array $filters = []): array;
}
