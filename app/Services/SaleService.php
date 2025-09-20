<?php

namespace App\Services;

use App\Contracts\SaleRepositoryInterface;

class SaleService
{
    protected SaleRepositoryInterface $saleRepository;

    public function __construct(SaleRepositoryInterface $saleRepository)
    {
        $this->saleRepository = $saleRepository;
    }

    /**
     * Registrar uma nova venda
     */
    public function createSale(array $items): array
    {
        return $this->saleRepository->createSale($items);
    }

    /**
     * Obter detalhes de uma venda específica
     */
    public function getSaleDetails(int $saleId): array
    {
        return $this->saleRepository->getSaleDetails($saleId);
    }

    /**
     * Processar venda (atualizar estoque)
     */
    public function processSale(int $saleId): void
    {
        $this->saleRepository->processSale($saleId);
    }
}
