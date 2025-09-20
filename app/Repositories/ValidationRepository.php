<?php

namespace App\Repositories;

use App\Contracts\ValidationRepositoryInterface;
use App\Contracts\InputValidationInterface;
use App\Contracts\FilterValidationInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidationRepository implements ValidationRepositoryInterface, InputValidationInterface, FilterValidationInterface
{
    /**
     * Validar dados de entrada de estoque
     */
    public function validateInventoryInput(array $data): array
    {
        $validator = Validator::make($data, [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Validar dados de entrada de venda
     */
    public function validateSaleInput(array $data): array
    {
        $validator = Validator::make($data, [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Validar filtros de relatório
     */
    public function validateReportFilters(array $data): array
    {
        $validator = Validator::make($data, [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'product_id' => 'nullable|exists:products,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
