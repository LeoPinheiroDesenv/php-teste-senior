<?php

namespace App\Services;

use App\Contracts\ReportGenerationInterface;

class ReportManager implements ReportGenerationInterface
{
    protected ReportGenerationInterface $reportGeneration;

    public function __construct(ReportGenerationInterface $reportGeneration)
    {
        $this->reportGeneration = $reportGeneration;
    }

    // Implementações de ReportGenerationInterface
    public function generateSalesReport(array $filters = []): array
    {
        return $this->reportGeneration->generateSalesReport($filters);
    }
}
