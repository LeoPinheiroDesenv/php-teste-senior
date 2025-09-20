<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\SaleCreationInterface;
use App\Contracts\SaleQueryInterface;
use App\Contracts\InputValidationInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    protected SaleCreationInterface $saleCreation;
    protected SaleQueryInterface $saleQuery;
    protected InputValidationInterface $inputValidation;

    public function __construct(
        SaleCreationInterface $saleCreation,
        SaleQueryInterface $saleQuery,
        InputValidationInterface $inputValidation
    ) {
        $this->saleCreation = $saleCreation;
        $this->saleQuery = $saleQuery;
        $this->inputValidation = $inputValidation;
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $this->inputValidation->validateSaleInput($request->all());
            $result = $this->saleCreation->createSale($validatedData['items']);

            return response()->json([
                'success' => true,
                'message' => 'Venda registrada com sucesso',
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
                'message' => 'Erro ao registrar venda',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $result = $this->saleQuery->getSaleDetails($id);

            return response()->json([
                'success' => true,
                'data' => $result['sale']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Venda não encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
