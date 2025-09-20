<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\StockOperationsInterface;
use App\Contracts\StockQueryInterface;
use App\Contracts\InputValidationInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class InventoryController extends Controller
{
    protected StockOperationsInterface $stockOperations;
    protected StockQueryInterface $stockQuery;
    protected InputValidationInterface $inputValidation;

    public function __construct(
        StockOperationsInterface $stockOperations,
        StockQueryInterface $stockQuery,
        InputValidationInterface $inputValidation
    ) {
        $this->stockOperations = $stockOperations;
        $this->stockQuery = $stockQuery;
        $this->inputValidation = $inputValidation;
    }

    /**
     * Registrar entrada de produtos no estoque
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $this->inputValidation->validateInventoryInput($request->all());
            
            $result = $this->stockOperations->addStock(
                $validatedData['product_id'],
                $validatedData['quantity']
            );

            return response()->json([
                'success' => true,
                'message' => 'Estoque atualizado com sucesso',
                'data' => $result
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar estoque',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter situação atual do estoque (otimizada)
     */
    public function index(): JsonResponse
    {
        try {
            $result = $this->stockQuery->getCurrentStock();

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar estoque',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}