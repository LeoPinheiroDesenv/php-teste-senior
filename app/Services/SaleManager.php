<?php

namespace App\Services;

use App\Contracts\SaleCreationInterface;
use App\Contracts\SaleQueryInterface;
use App\Contracts\SaleProcessingInterface;

class SaleManager implements SaleCreationInterface, SaleQueryInterface, SaleProcessingInterface
{
    protected SaleCreationInterface $saleCreation;
    protected SaleQueryInterface $saleQuery;
    protected SaleProcessingInterface $saleProcessing;

    public function __construct(
        SaleCreationInterface $saleCreation,
        SaleQueryInterface $saleQuery,
        SaleProcessingInterface $saleProcessing
    ) {
        $this->saleCreation = $saleCreation;
        $this->saleQuery = $saleQuery;
        $this->saleProcessing = $saleProcessing;
    }

    // Implementações de SaleCreationInterface
    public function createSale(array $items): array
    {
        return $this->saleCreation->createSale($items);
    }

    // Implementações de SaleQueryInterface
    public function getSaleDetails(int $saleId): array
    {
        return $this->saleQuery->getSaleDetails($saleId);
    }

    // Implementações de SaleProcessingInterface
    public function processSale(int $saleId): void
    {
        $this->saleProcessing->processSale($saleId);
    }
}
