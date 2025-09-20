#!/bin/bash

echo "🚀 Configurando projeto Laravel API..."

# Verificar se Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "❌ Docker não está instalado. Por favor, instale o Docker primeiro."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose não está instalado. Por favor, instale o Docker Compose primeiro."
    exit 1
fi

echo "✅ Docker e Docker Compose encontrados"

# Construir e iniciar containers
echo "🔨 Construindo e iniciando containers..."
docker-compose up -d --build

# Aguardar MySQL estar pronto
echo "⏳ Aguardando MySQL estar pronto..."
sleep 30

# Executar migrations
echo "📊 Executando migrations..."
docker-compose exec -T app php artisan migrate --force

# Executar seeders para dados de teste
echo "🌱 Populando banco com dados de teste..."
docker-compose exec -T app php artisan db:seed --force

# Executar testes
echo "🧪 Executando testes..."
docker-compose exec -T app php artisan test

echo "✅ Setup concluído!"
echo ""
echo "🌐 Serviços disponíveis:"
echo "   - API: http://localhost:8080"
echo "   - phpMyAdmin: http://localhost:8081"
echo ""
echo "📋 Comandos úteis:"
echo "   - Ver logs: docker-compose logs -f"
echo "   - Executar testes: docker-compose exec app php artisan test"
echo "   - Acessar container: docker-compose exec app bash"
echo "   - Parar containers: docker-compose down"
