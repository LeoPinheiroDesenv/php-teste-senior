<?php

namespace App\Repositories;

use App\Contracts\ValidationRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StrictValidationRepository implements ValidationRepositoryInterface
{
    protected ValidationRepositoryInterface $validationRepository;

    public function __construct(ValidationRepositoryInterface $validationRepository)
    {
        $this->validationRepository = $validationRepository;
    }

    /**
     * Validar dados de entrada de estoque (versão mais rigorosa)
     */
    public function validateInventoryInput(array $data): array
    {
        $validator = Validator::make($data, [
            'product_id' => 'required|exists:products,id|integer|min:1',
            'quantity' => 'required|integer|min:1|max:10000', // Limite máximo
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Validações adicionais
        $this->validateProductExists($data['product_id']);
        $this->validateQuantityLimits($data['quantity']);

        return $validator->validated();
    }

    /**
     * Validar dados de entrada de venda (versão mais rigorosa)
     */
    public function validateSaleInput(array $data): array
    {
        $validator = Validator::make($data, [
            'items' => 'required|array|min:1|max:50', // Máximo 50 itens
            'items.*.product_id' => 'required|exists:products,id|integer|min:1',
            'items.*.quantity' => 'required|integer|min:1|max:1000', // Limite por item
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Validações adicionais
        $this->validateUniqueProducts($data['items']);
        $this->validateTotalQuantity($data['items']);

        return $validator->validated();
    }

    /**
     * Validar filtros de relatório (versão mais rigorosa)
     */
    public function validateReportFilters(array $data): array
    {
        $validator = Validator::make($data, [
            'start_date' => 'nullable|date|before_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date|before_or_equal:today',
            'product_id' => 'nullable|exists:products,id|integer|min:1',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Validações adicionais
        $this->validateDateRange($data);

        return $validator->validated();
    }

    /**
     * Validar se produto existe
     */
    protected function validateProductExists(int $productId): void
    {
        if (!\App\Models\Product::where('id', $productId)->exists()) {
            throw new ValidationException(
                Validator::make([], [])->errors()->add('product_id', 'Produto não encontrado')
            );
        }
    }

    /**
     * Validar limites de quantidade
     */
    protected function validateQuantityLimits(int $quantity): void
    {
        if ($quantity > 10000) {
            throw new ValidationException(
                Validator::make([], [])->errors()->add('quantity', 'Quantidade excede o limite máximo')
            );
        }
    }

    /**
     * Validar produtos únicos na venda
     */
    protected function validateUniqueProducts(array $items): void
    {
        $productIds = array_column($items, 'product_id');
        if (count($productIds) !== count(array_unique($productIds))) {
            throw new ValidationException(
                Validator::make([], [])->errors()->add('items', 'Produtos duplicados não são permitidos')
            );
        }
    }

    /**
     * Validar quantidade total
     */
    protected function validateTotalQuantity(array $items): void
    {
        $totalQuantity = array_sum(array_column($items, 'quantity'));
        if ($totalQuantity > 1000) {
            throw new ValidationException(
                Validator::make([], [])->errors()->add('items', 'Quantidade total excede o limite máximo')
            );
        }
    }

    /**
     * Validar intervalo de datas
     */
    protected function validateDateRange(array $data): void
    {
        if (isset($data['start_date']) && isset($data['end_date'])) {
            $startDate = \Carbon\Carbon::parse($data['start_date']);
            $endDate = \Carbon\Carbon::parse($data['end_date']);
            
            if ($endDate->diffInDays($startDate) > 365) {
                throw new ValidationException(
                    Validator::make([], [])->errors()->add('end_date', 'Intervalo de datas não pode exceder 1 ano')
                );
            }
        }
    }
}
