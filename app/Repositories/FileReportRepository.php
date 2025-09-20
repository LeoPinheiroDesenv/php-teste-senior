<?php

namespace App\Repositories;

use App\Contracts\ReportRepositoryInterface;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class FileReportRepository implements ReportRepositoryInterface
{
    protected ReportRepositoryInterface $reportRepository;
    protected string $storagePath;

    public function __construct(ReportRepositoryInterface $reportRepository, string $storagePath = 'reports')
    {
        $this->reportRepository = $reportRepository;
        $this->storagePath = $storagePath;
    }

    /**
     * Gerar relatório de vendas com filtros e salvar em arquivo
     */
    public function generateSalesReport(array $filters = []): array
    {
        // Gerar relatório usando implementação base
        $report = $this->reportRepository->generateSalesReport($filters);
        
        // Salvar relatório em arquivo
        $this->saveReportToFile($report, $filters);
        
        return $report;
    }

    /**
     * Salvar relatório em arquivo
     */
    protected function saveReportToFile(array $report, array $filters): void
    {
        try {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "sales_report_{$timestamp}.json";
            $filepath = "{$this->storagePath}/{$filename}";
            
            $fileContent = [
                'generated_at' => now()->toISOString(),
                'filters' => $filters,
                'report' => $report
            ];
            
            Storage::put($filepath, json_encode($fileContent, JSON_PRETTY_PRINT));
            
            Log::info("Relatório salvo em arquivo: {$filepath}");
            
        } catch (\Exception $e) {
            Log::error("Erro ao salvar relatório em arquivo: " . $e->getMessage());
            // Não falha o relatório se não conseguir salvar o arquivo
        }
    }

    /**
     * Listar relatórios salvos
     */
    public function listSavedReports(): array
    {
        try {
            $files = Storage::files($this->storagePath);
            $reports = [];
            
            foreach ($files as $file) {
                if (str_ends_with($file, '.json')) {
                    $content = Storage::get($file);
                    $data = json_decode($content, true);
                    
                    $reports[] = [
                        'filename' => basename($file),
                        'generated_at' => $data['generated_at'] ?? null,
                        'filters' => $data['filters'] ?? [],
                        'size' => Storage::size($file)
                    ];
                }
            }
            
            return $reports;
            
        } catch (\Exception $e) {
            Log::error("Erro ao listar relatórios salvos: " . $e->getMessage());
            return [];
        }
    }
}
