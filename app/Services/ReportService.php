<?php

namespace App\Services;

use App\Contracts\ReportRepositoryInterface;

class ReportService
{
    protected ReportRepositoryInterface $reportRepository;

    public function __construct(ReportRepositoryInterface $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    /**
     * Gerar relatório de vendas com filtros
     */
    public function generateSalesReport(array $filters = []): array
    {
        return $this->reportRepository->generateSalesReport($filters);
    }
}
