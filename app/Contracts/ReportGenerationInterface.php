<?php

namespace App\Contracts;

interface ReportGenerationInterface
{
    public function generateSalesReport(array $filters = []): array;
}
