<?php

namespace App\Services;

use App\Contracts\ValidationRepositoryInterface;

class ValidationService
{
    protected ValidationRepositoryInterface $validationRepository;

    public function __construct(ValidationRepositoryInterface $validationRepository)
    {
        $this->validationRepository = $validationRepository;
    }

    /**
     * Validar dados de entrada de estoque
     */
    public function validateInventoryInput(array $data): array
    {
        return $this->validationRepository->validateInventoryInput($data);
    }

    /**
     * Validar dados de entrada de venda
     */
    public function validateSaleInput(array $data): array
    {
        return $this->validationRepository->validateSaleInput($data);
    }

    /**
     * Validar filtros de relatório
     */
    public function validateReportFilters(array $data): array
    {
        return $this->validationRepository->validateReportFilters($data);
    }
}
