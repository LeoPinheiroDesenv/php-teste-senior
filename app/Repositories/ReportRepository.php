<?php

namespace App\Repositories;

use App\Contracts\ReportRepositoryInterface;
use App\Contracts\ReportGenerationInterface;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReportRepository implements ReportRepositoryInterface, ReportGenerationInterface
{
    /**
     * Gerar relatório de vendas com filtros
     */
    public function generateSalesReport(array $filters = []): array
    {
        try {
            $query = Sale::with(['saleItems.product:id,sku,name'])
                ->where('status', 'completed');

            // Aplicar filtros
            $this->applyFilters($query, $filters);

            $sales = $query->orderBy('created_at', 'desc')->get();

            // Calcular estatísticas
            $statistics = $this->calculateStatistics($sales);

            // Obter produtos mais vendidos
            $topProducts = $this->getTopProducts($filters);

            return [
                'success' => true,
                'sales' => $sales,
                'statistics' => $statistics,
                'top_products' => $topProducts,
                'filters_applied' => $filters
            ];

        } catch (\Exception $e) {
            Log::error("Erro ao gerar relatório de vendas: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Aplicar filtros à query
     */
    protected function applyFilters($query, array $filters): void
    {
        // Filtro por período
        if (isset($filters['start_date'])) {
            $startDate = Carbon::parse($filters['start_date'])->startOfDay();
            $query->where('created_at', '>=', $startDate);
        }

        if (isset($filters['end_date'])) {
            $endDate = Carbon::parse($filters['end_date'])->endOfDay();
            $query->where('created_at', '<=', $endDate);
        }

        // Filtro por produto
        if (isset($filters['product_id'])) {
            $query->whereHas('saleItems', function ($q) use ($filters) {
                $q->where('product_id', $filters['product_id']);
            });
        }
    }

    /**
     * Calcular estatísticas do relatório
     */
    protected function calculateStatistics($sales): array
    {
        return [
            'total_sales' => $sales->count(),
            'total_amount' => $sales->sum('total_amount'),
            'total_cost' => $sales->sum('total_cost'),
            'total_profit' => $sales->sum('total_profit'),
            'average_sale_value' => $sales->count() > 0 ? $sales->avg('total_amount') : 0,
            'profit_margin' => $sales->sum('total_amount') > 0 
                ? ($sales->sum('total_profit') / $sales->sum('total_amount')) * 100 
                : 0,
        ];
    }

    /**
     * Obter produtos mais vendidos
     */
    protected function getTopProducts(array $filters): array
    {
        $query = DB::table('sale_items as si')
            ->join('products as p', 'si.product_id', '=', 'p.id')
            ->join('sales as s', 'si.sale_id', '=', 's.id')
            ->where('s.status', 'completed');

        // Aplicar filtros
        if (isset($filters['start_date'])) {
            $query->where('s.created_at', '>=', Carbon::parse($filters['start_date'])->startOfDay());
        }

        if (isset($filters['end_date'])) {
            $query->where('s.created_at', '<=', Carbon::parse($filters['end_date'])->endOfDay());
        }

        if (isset($filters['product_id'])) {
            $query->where('si.product_id', $filters['product_id']);
        }

        return $query->select([
                'p.id',
                'p.sku',
                'p.name',
                DB::raw('SUM(si.quantity) as total_quantity'),
                DB::raw('SUM(si.quantity * si.unit_price) as total_revenue'),
                DB::raw('SUM(si.quantity * si.unit_cost) as total_cost'),
                DB::raw('SUM(si.quantity * (si.unit_price - si.unit_cost)) as total_profit')
            ])
            ->groupBy('p.id', 'p.sku', 'p.name')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }
}
