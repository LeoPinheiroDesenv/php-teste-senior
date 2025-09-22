# API REST Laravel - Sistema de Gerenciamento de Estoque e Vendas

Este projeto implementa uma API REST completa usando Laravel 11 e PHP 8.3, com funcionalidades de gerenciamento de estoque, processamento de vendas e relatórios. O projeto segue os princípios SOLID para garantir uma arquitetura limpa, testável e manutenível.

## 🚀 Tecnologias Utilizadas

- **Laravel 11** - Framework PHP
- **PHP 8.3** - Linguagem de programação
- **MySQL 8.0** - Banco de dados
- **Redis** - Cache e filas
- **Docker** - Containerização
- **PHPUnit** - Testes unitários
- **Nginx** - Servidor web

## 📋 Funcionalidades

### 1. Gerenciamento de Estoque
- ✅ Registrar entrada de produtos no estoque
- ✅ Consultar situação atual do estoque com valores totais e lucro projetado
- ✅ Consulta otimizada com agregações
- ✅ Tarefa agendada para limpeza de registros antigos (90 dias)

### 2. Processamento de Vendas
- ✅ Registrar vendas com múltiplos itens
- ✅ Cálculo automático de valores totais e margem de lucro
- ✅ Processamento assíncrono via filas
- ✅ Consulta otimizada de detalhes de vendas
- ✅ Verificação de estoque disponível

### 3. Relatórios
- ✅ Relatório de vendas com filtros por período e produto
- ✅ Estatísticas de vendas e produtos mais vendidos
- ✅ Cálculo de margem de lucro

## 🗄️ Estrutura do Banco de Dados

### Tabelas Principais

#### products
- `id` - Chave primária
- `sku` - Código único do produto
- `name` - Nome do produto
- `description` - Descrição do produto
- `cost_price` - Preço de custo
- `sale_price` - Preço de venda
- `created_at`, `updated_at` - Timestamps

#### inventory
- `id` - Chave primária
- `product_id` - FK para products
- `quantity` - Quantidade em estoque
- `last_updated` - Última atualização
- `created_at`, `updated_at` - Timestamps

#### sales
- `id` - Chave primária
- `total_amount` - Valor total da venda
- `total_cost` - Custo total
- `total_profit` - Lucro total
- `status` - Status da venda (pending, completed, cancelled)
- `created_at`, `updated_at` - Timestamps

#### sale_items
- `id` - Chave primária
- `sale_id` - FK para sales
- `product_id` - FK para products
- `quantity` - Quantidade vendida
- `unit_price` - Preço unitário
- `unit_cost` - Custo unitário
- `created_at`, `updated_at` - Timestamps

## 🔌 Endpoints da API

### Gerenciamento de Estoque

#### POST /api/inventory
Registrar entrada de produtos no estoque.

**Request:**
```json
{
    "product_id": 1,
    "quantity": 100
}
```

**Response:**
```json
{
    "success": true,
    "message": "Estoque atualizado com sucesso",
    "data": {
        "product": {...},
        "inventory": {...}
    }
}
```

#### GET /api/inventory
Obter situação atual do estoque.

**Response:**
```json
{
    "success": true,
    "data": {
        "inventory": [...],
        "summary": {
            "total_products": 10,
            "total_quantity": 1000,
            "total_cost": 5000.00,
            "total_value": 10000.00,
            "total_projected_profit": 5000.00
        }
    }
}
```

### Processamento de Vendas

#### POST /api/sales
Registrar uma nova venda.

**Request:**
```json
{
    "items": [
        {
            "product_id": 1,
            "quantity": 5
        },
        {
            "product_id": 2,
            "quantity": 3
        }
    ]
}
```

**Response:**
```json
{
    "success": true,
    "message": "Venda registrada com sucesso",
    "data": {
        "sale_id": 1,
        "status": "processing",
        "total_amount": 150.00,
        "total_profit": 75.00
    }
}
```

#### GET /api/sales/{id}
Obter detalhes de uma venda específica.

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "total_amount": 150.00,
        "total_cost": 75.00,
        "total_profit": 75.00,
        "status": "completed",
        "sale_items": [...]
    }
}
```

### Relatórios

#### GET /api/reports/sales
Gerar relatório de vendas com filtros.

**Query Parameters:**
- `start_date` - Data inicial (YYYY-MM-DD)
- `end_date` - Data final (YYYY-MM-DD)
- `product_id` - ID do produto específico

**Response:**
```json
{
    "success": true,
    "data": {
        "sales": [...],
        "statistics": {
            "total_sales": 50,
            "total_amount": 10000.00,
            "total_cost": 6000.00,
            "total_profit": 4000.00,
            "average_sale_value": 200.00,
            "profit_margin": 40.0
        },
        "top_products": [...],
        "filters_applied": {...}
    }
}
```

## 🐳 Configuração Docker

### Executar o projeto

```bash
# Construir e iniciar os containers
docker-compose up -d --build

# Executar migrations
docker-compose exec app php artisan migrate

# Executar seeders (opcional)
docker-compose exec app php artisan db:seed
```

### Serviços disponíveis

- **API**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
- **MySQL**: localhost:3306
- **Redis**: http://localhost:8082

### Comandos úteis

```bash
# Executar testes
docker-compose exec app php artisan test

# Limpar cache
docker-compose exec app php artisan cache:clear

# Executar comando de limpeza de estoque
docker-compose exec app php artisan inventory:clean-old

# Ver logs
docker-compose logs -f app
```

## 🧪 Testes

O projeto inclui testes unitários e de integração:

```bash
# Executar todos os testes
php artisan test

# Executar testes específicos
php artisan test --filter=InventoryTest
php artisan test --filter=SaleTest
```

## ⚙️ Configurações

### Variáveis de Ambiente

As principais configurações estão no arquivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_password

REDIS_HOST=redis
REDIS_PORT=6379

QUEUE_CONNECTION=database
```

### Agendamento de Tarefas

O sistema inclui uma tarefa agendada para limpeza de registros de estoque antigos:

- **Comando**: `inventory:clean-old`
- **Frequência**: Diariamente às 2:00
- **Parâmetro**: 90 dias (configurável)

## 🔄 Processamento Assíncrono

As vendas são processadas de forma assíncrona usando filas:

1. Venda é registrada com status "pending"
2. Job `ProcessSale` é adicionado à fila
3. Job verifica estoque disponível
4. Se disponível, atualiza estoque e marca como "completed"
5. Se indisponível, marca como "cancelled"

## 📊 Otimizações Implementadas

### Consultas de Estoque
- Uso de JOINs para evitar N+1 queries
- Agregações no banco de dados
- Índices nas colunas de relacionamento

### Consultas de Vendas
- Eager loading de relacionamentos
- Índices em colunas de filtro
- Cache de consultas frequentes

## 🚀 Deploy

Para fazer deploy em produção:

1. Configure as variáveis de ambiente
2. Execute as migrations
3. Configure o agendador de tarefas (cron)
4. Configure o processador de filas
5. Configure o servidor web (Nginx/Apache)

## 📝 Logs

O sistema registra logs importantes:
- Processamento de vendas
- Erros de validação
- Falhas em jobs
- Limpeza de dados antigos

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

### Estrutura de Pastas
```
app/
├── Contracts/                    # Interfaces (Abstrações)
│   ├── InventoryRepositoryInterface.php
│   ├── SaleRepositoryInterface.php
│   ├── StockOperationsInterface.php
│   ├── StockQueryInterface.php
│   ├── SaleCreationInterface.php
│   ├── SaleQueryInterface.php
│   ├── SaleProcessingInterface.php
│   ├── ReportGenerationInterface.php
│   ├── InputValidationInterface.php
│   └── FilterValidationInterface.php
├── Repositories/                 # Implementações concretas
│   ├── InventoryRepository.php
│   ├── SaleRepository.php
│   ├── ReportRepository.php
│   ├── ValidationRepository.php
│   ├── CacheInventoryRepository.php
│   ├── FileReportRepository.php
│   └── StrictValidationRepository.php
├── Services/                     # Camada de serviços
│   ├── InventoryService.php
│   ├── SaleService.php
│   ├── ReportService.php
│   ├── ValidationService.php
│   ├── StockManager.php
│   ├── SaleManager.php
│   ├── ReportManager.php
│   └── ValidationManager.php
├── Http/Controllers/Api/         # Controllers (Thin)
│   ├── InventoryController.php
│   ├── SaleController.php
│   └── ReportController.php
└── Providers/                    # Service Providers
    └── RepositoryServiceProvider.php
```

### Fluxo de Dependências
```
Controller → Service → Repository → Model
     ↓         ↓         ↓
  Interface  Interface  Interface
```

### Implementações Disponíveis

#### 1. Inventory (Estoque)
- **Padrão**: `InventoryRepository` - Consultas diretas ao banco
- **Com Cache**: `CacheInventoryRepository` - Cache de consultas
- **Configuração**: `USE_CACHE_INVENTORY=true`

#### 2. Reports (Relatórios)
- **Padrão**: `ReportRepository` - Geração em memória
- **Com Arquivo**: `FileReportRepository` - Salvamento em arquivos JSON
- **Configuração**: `USE_FILE_REPORTS=true`

#### 3. Validation (Validação)
- **Padrão**: `ValidationRepository` - Validações básicas
- **Rigorosa**: `StrictValidationRepository` - Validações adicionais
- **Configuração**: `USE_STRICT_VALIDATION=true`

### Testes de Unidade
```php
class InventoryServiceTest extends TestCase
{
    public function testAddStock()
    {
        // Mock da interface
        $mockRepository = Mockery::mock(InventoryRepositoryInterface::class);
        $mockRepository->shouldReceive('addStock')
            ->with(1, 10)
            ->andReturn(['success' => true]);
        
        // Injeção da dependência mockada
        $service = new InventoryService($mockRepository);
        
        $result = $service->addStock(1, 10);
        
        $this->assertTrue($result['success']);
    }
}
```

### Testes de Integração
```php
class InventoryControllerTest extends TestCase
{
    public function testStoreInventory()
    {
        // Configuração de implementação específica para teste
        $this->app->bind(StockOperationsInterface::class, function () {
            return new TestInventoryRepository();
        });
        
        $response = $this->postJson('/api/inventory', [
            'product_id' => 1,
            'quantity' => 10
        ]);
        
        $response->assertStatus(201);
    }
}
```

## 🌐 Interfaces Web

### URLs de Acesso
| Serviço | URL | Usuário | Senha | Descrição |
|---------|-----|---------|-------|-----------|
| **Laravel API** | http://localhost:8080 | - | - | API REST Laravel |
| **phpMyAdmin** | http://localhost:8081 | laravel_user | laravel_password | Interface MySQL |
| **Redis Commander** | http://localhost:8082 | admin | admin123 | Interface Redis |

### Funcionalidades da Interface Redis
- **📋 Visualização de Chaves**: Lista todas as chaves organizadas por tipo
- **✏️ Edição de Valores**: Edite valores diretamente na interface
- **➕ Criação de Chaves**: Crie novas chaves com diferentes tipos de dados
- **🗑️ Exclusão**: Delete chaves individualmente ou em lote
- **📊 Estatísticas**: Visualize informações do servidor Redis
- **👀 Monitoramento**: Veja comandos sendo executados em tempo real

### Comandos Docker
```bash
# Ver containers
docker-compose ps

# Logs
docker-compose logs app
docker-compose logs redis
docker-compose logs db

# Reiniciar serviços
docker-compose restart redis
docker-compose restart app
```

## 🎯 Exemplos de Uso

### Configuração Básica (Desenvolvimento)
```env
USE_CACHE_INVENTORY=false
USE_FILE_REPORTS=false
USE_STRICT_VALIDATION=false
```

### Configuração com Cache (Produção)
```env
USE_CACHE_INVENTORY=true
CACHE_TTL_INVENTORY=3600
USE_FILE_REPORTS=false
USE_STRICT_VALIDATION=true
```

### Configuração Completa (Produção Avançada)
```env
USE_CACHE_INVENTORY=true
CACHE_TTL_INVENTORY=3600
USE_FILE_REPORTS=true
REPORTS_STORAGE_PATH=reports
USE_STRICT_VALIDATION=true
VALIDATION_MAX_QUANTITY=10000
