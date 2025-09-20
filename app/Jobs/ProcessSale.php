<?php

namespace App\Jobs;

use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSale implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $saleId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $saleId)
    {
        $this->saleId = $saleId;
    }

    /**
     * Execute the job.
     */
    public function handle(SaleService $saleService): void
    {
        try {
            $saleService->processSale($this->saleId);
        } catch (\Exception $e) {
            Log::error("Erro ao processar venda {$this->saleId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $sale = Sale::find($this->saleId);
        if ($sale) {
            $sale->update(['status' => 'cancelled']);
        }

        Log::error("Job ProcessSale falhou para venda {$this->saleId}: " . $exception->getMessage());
    }
}
