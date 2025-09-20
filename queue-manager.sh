#!/bin/bash

# 🚀 Queue Manager - Script para gerenciar jobs do Laravel

case "$1" in
    "start")
        echo "🚀 Iniciando worker de filas..."
        docker-compose exec -d app php artisan queue:work --sleep=3 --tries=3
        echo "✅ Worker iniciado em background"
        ;;
    "stop")
        echo "🛑 Parando workers..."
        docker-compose exec app pkill -f "queue:work"
        echo "✅ Workers parados"
        ;;
    "restart")
        echo "🔄 Reiniciando workers..."
        docker-compose exec app pkill -f "queue:work"
        sleep 2
        docker-compose exec -d app php artisan queue:work --sleep=3 --tries=3
        echo "✅ Workers reiniciados"
        ;;
    "status")
        echo "📊 Status dos workers:"
        docker-compose exec app ps aux | grep "queue:work" || echo "❌ Nenhum worker ativo"
        ;;
    "monitor")
        echo "👀 Monitorando filas..."
        docker-compose exec app php artisan queue:monitor default
        ;;
    "failed")
        echo "❌ Jobs falhados:"
        docker-compose exec app php artisan queue:failed
        ;;
    "retry")
        echo "🔄 Reprocessando jobs falhados..."
        docker-compose exec app php artisan queue:retry all
        echo "✅ Jobs reprocessados"
        ;;
    "clear")
        echo "🧹 Limpando jobs falhados..."
        docker-compose exec app php artisan queue:flush
        echo "✅ Jobs limpos"
        ;;
    "logs")
        echo "📝 Logs dos workers:"
        docker-compose exec app tail -f storage/logs/laravel.log | grep -E "(ProcessSale|queue|job)"
        ;;
    "test")
        echo "🧪 Testando job..."
        docker-compose exec app php artisan tinker --execute="
            \$sale = App\Models\Sale::first();
            if (\$sale) {
                App\Jobs\ProcessSale::dispatch(\$sale->id);
                echo 'Job ProcessSale disparado para venda ID: ' . \$sale->id;
            } else {
                echo 'Nenhuma venda encontrada para testar';
            }
        "
        ;;
    "info")
        echo "📊 Informações das filas:"
        echo "=========================="
        docker-compose exec app php artisan queue:monitor default
        echo ""
        echo "📈 Jobs falhados:"
        docker-compose exec app php artisan queue:failed
        echo ""
        echo "🔄 Status dos workers:"
        docker-compose exec app ps aux | grep "queue:work" || echo "❌ Nenhum worker ativo"
        ;;
    *)
        echo "🚀 Queue Manager - Gerenciador de Jobs Laravel"
        echo ""
        echo "Uso: $0 {comando}"
        echo ""
        echo "Comandos disponíveis:"
        echo "  start     - Iniciar worker de filas"
        echo "  stop      - Parar workers"
        echo "  restart   - Reiniciar workers"
        echo "  status    - Ver status dos workers"
        echo "  monitor   - Monitorar filas"
        echo "  failed    - Ver jobs falhados"
        echo "  retry     - Reprocessar jobs falhados"
        echo "  clear     - Limpar jobs falhados"
        echo "  logs      - Ver logs dos workers"
        echo "  test      - Testar job ProcessSale"
        echo "  info      - Informações completas das filas"
        echo ""
        echo "Exemplos:"
        echo "  $0 start"
        echo "  $0 status"
        echo "  $0 monitor"
        ;;
esac
