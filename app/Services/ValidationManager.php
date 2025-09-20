<?php

namespace App\Services;

use App\Contracts\InputValidationInterface;
use App\Contracts\FilterValidationInterface;

class ValidationManager implements InputValidationInterface, FilterValidationInterface
{
    protected InputValidationInterface $inputValidation;
    protected FilterValidationInterface $filterValidation;

    public function __construct(
        InputValidationInterface $inputValidation,
        FilterValidationInterface $filterValidation
    ) {
        $this->inputValidation = $inputValidation;
        $this->filterValidation = $filterValidation;
    }

    // Implementações de InputValidationInterface
    public function validateInventoryInput(array $data): array
    {
        return $this->inputValidation->validateInventoryInput($data);
    }

    public function validateSaleInput(array $data): array
    {
        return $this->inputValidation->validateSaleInput($data);
    }

    // Implementações de FilterValidationInterface
    public function validateReportFilters(array $data): array
    {
        return $this->filterValidation->validateReportFilters($data);
    }
}
