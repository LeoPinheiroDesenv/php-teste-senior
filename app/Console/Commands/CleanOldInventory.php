<?php

namespace App\Console\Commands;

use App\Models\Inventory;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanOldInventory extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'inventory:clean-old {--days=90 : Número de dias para considerar como antigo}';

    /**
     * The console command description.
     */
    protected $description = 'Remove registros de estoque que não foram atualizados nos últimos X dias';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Limpando registros de estoque não atualizados desde {$cutoffDate->format('Y-m-d H:i:s')}");

        $deletedCount = Inventory::where('last_updated', '<', $cutoffDate)->delete();

        $this->info("Removidos {$deletedCount} registros de estoque antigos.");

        return Command::SUCCESS;
    }
}
