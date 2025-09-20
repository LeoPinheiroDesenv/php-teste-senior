#!/bin/bash

# Redis Manager - Gerenciador de Redis para Laravel
# Uso: ./redis-manager.sh {comando}

case "$1" in
    "status")
        echo "📊 Status do Redis:"
        echo "=================="
        docker-compose exec redis redis-cli ping
        echo ""
        echo "🔗 Conexões ativas:"
        docker-compose exec redis redis-cli info clients | grep connected_clients
        echo ""
        echo "🌐 Interface Web:"
        echo "   URL: http://localhost:8082"
        echo "   Usuário: admin"
        echo "   Senha: admin123"
        ;;
    "monitor")
        echo "👀 Monitorando Redis (Ctrl+C para sair):"
        docker-compose exec redis redis-cli monitor
        ;;
    "info")
        echo "📈 Informações do Redis:"
        echo "========================"
        docker-compose exec redis redis-cli info server | head -10
        echo ""
        echo "💾 Memória:"
        docker-compose exec redis redis-cli info memory | grep -E "(used_memory|maxmemory)"
        echo ""
        echo "📊 Estatísticas:"
        docker-compose exec redis redis-cli info stats | grep -E "(total_connections|total_commands)"
        ;;
    "keys")
        echo "🔑 Chaves no Redis:"
        docker-compose exec redis redis-cli keys "*"
        ;;
    "clear")
        echo "🧹 Limpando Redis..."
        docker-compose exec redis redis-cli flushall
        echo "✅ Redis limpo!"
        ;;
    "queue")
        echo "📋 Status das filas:"
        echo "==================="
        docker-compose exec redis redis-cli llen "queues:default"
        echo "Jobs na fila 'default': $(docker-compose exec redis redis-cli llen "queues:default")"
        echo ""
        echo "📝 Primeiros 5 jobs:"
        docker-compose exec redis redis-cli lrange "queues:default" 0 4
        ;;
    "cache")
        echo "💾 Cache no Redis:"
        echo "=================="
        docker-compose exec redis redis-cli keys "laravel_cache:*" | head -10
        echo ""
        echo "📊 Total de chaves de cache: $(docker-compose exec redis redis-cli keys "laravel_cache:*" | wc -l)"
        ;;
    "cli")
        echo "🖥️  Abrindo Redis CLI:"
        docker-compose exec redis redis-cli
        ;;
    "test")
        echo "🧪 Testando Redis..."
        docker-compose exec app php test-redis.php
        ;;
    "laravel-test")
        echo "🧪 Testando Laravel + Redis..."
        docker-compose exec app php test-laravel-redis.php
        ;;
    "web")
        echo "🌐 Abrindo interface web do Redis..."
        echo "URL: http://localhost:8082"
        echo "Usuário: admin"
        echo "Senha: admin123"
        echo ""
        echo "Tentando abrir no navegador..."
        if command -v xdg-open > /dev/null; then
            xdg-open http://localhost:8082
        elif command -v open > /dev/null; then
            open http://localhost:8082
        else
            echo "Abra manualmente: http://localhost:8082"
        fi
        ;;
    *)
        echo "🚀 Redis Manager - Gerenciador de Redis"
        echo ""
        echo "Uso: $0 {comando}"
        echo ""
        echo "Comandos disponíveis:"
        echo "  status        - Status do Redis"
        echo "  monitor       - Monitorar comandos Redis"
        echo "  info          - Informações detalhadas"
        echo "  keys          - Listar todas as chaves"
        echo "  clear         - Limpar todo o Redis"
        echo "  queue         - Status das filas"
        echo "  cache         - Status do cache"
        echo "  cli           - Abrir Redis CLI"
        echo "  web           - Abrir interface web"
        echo "  test          - Testar conexão Redis"
        echo "  laravel-test  - Testar Laravel + Redis"
        echo ""
        echo "Exemplos:"
        echo "  $0 status"
        echo "  $0 monitor"
        echo "  $0 queue"
        echo "  $0 cli"
        ;;
esac
