<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\ReportGenerationInterface;
use App\Contracts\FilterValidationInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    protected ReportGenerationInterface $reportGeneration;
    protected FilterValidationInterface $filterValidation;

    public function __construct(
        ReportGenerationInterface $reportGeneration,
        FilterValidationInterface $filterValidation
    ) {
        $this->reportGeneration = $reportGeneration;
        $this->filterValidation = $filterValidation;
    }

    public function sales(Request $request): JsonResponse
    {
        try {
            $validatedFilters = $this->filterValidation->validateReportFilters($request->all());
            $result = $this->reportGeneration->generateSalesReport($validatedFilters);

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Filtros inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar relatório',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
