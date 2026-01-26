# Zabbix API Symfony Bundle

Modern Symfony bundle for Zabbix API integration with persistent authentication, type-safe actions, and history data retrieval.

## Features

✨ **Persistent Session Management** - Automatic token caching with retry-on-failure
🏭 **Factory Pattern** - Type-safe action instantiation
📊 **History Data Retrieval** - Query monitoring data with type-specific methods
🔒 **Self-Contained Actions** - No globals, clean architecture
🎯 **PHP 8.3+ Ready** - Full strict typing, readonly properties, enums
⚡ **Zero Configuration** - Works out of the box with environment variables
📦 **Complete API Coverage** - Host, HostGroup, Item, History, Graph, Trigger, and more

## Installation

```bash
composer require bytes-commerce/zabbix-api
```

Bundle auto-registers via Symfony Flex.

## Quick Start

### 1. Configure

Create `.env.local`:

```bash
ZABBIX_API_BASE_URI=https://zabbix.example.com/api_jsonrpc.php
ZABBIX_USERNAME=monitoring_user
ZABBIX_PASSWORD=secure_password
ZABBIX_AUTH_TTL=3600  # Optional: cache TTL in seconds
```

### 2. Use Actions (Recommended)

```php
use BytesCommerce\ZabbixApi\ZabbixServiceInterface;
use BytesCommerce\ZabbixApi\Actions\History;
use BytesCommerce\ZabbixApi\Actions\HostGroup;
use BytesCommerce\ZabbixApi\Actions\Dto\GetHostGroupDto;
use BytesCommerce\ZabbixApi\Enums\HistoryTypeEnum;

class MonitoringController
{
    public function __construct(
        private readonly ZabbixServiceInterface $zabbix
    ) {}

    public function cpuMetrics(): array
    {
        // Factory pattern - type-safe instantiation
        $history = $this->zabbix->action(History::class);

        // Get last 24 hours of CPU data (float values)
        return $history->getLast24Hours(
            itemIds: ['12345'],
            historyType: HistoryTypeEnum::NUMERIC_FLOAT,
            limit: 100
        );
    }

    public function getHostGroups(): array
    {
        // Get all host groups
        $hostGroup = $this->zabbix->action(HostGroup::class);
        $dto = new GetHostGroupDto(
            groupids: null,
            hostids: null,
            filter: null,
            output: 'extend',
            selectHosts: true,
            selectTemplates: null,
            sortfield: null,
            sortorder: null,
            limit: null,
            preservekeys: null,
        );

        return $hostGroup->get($dto)->hostGroups;
    }
}
```

## Core Concepts

### Actions (Self-Contained API Modules)

Each action represents a Zabbix API namespace:

| Action Class | API Prefix | Methods Example |
|-------------|------------|------------------|
| `History` | `history` | `get()`, `getLast24Hours()`, `getLatest()` |
| `Host` | `host` | `get()`, `create()`, `update()`, `delete()` |
| `HostGroup` | `hostgroup` | `get()`, `create()`, `update()`, `delete()`, `exists()`, `getObjects()`, `isReadable()`, `isWritable()`, `massAdd()`, `massRemove()`, `massUpdate()` |
| `Dashboard` | `dashboard` | `get()`, `create()`, `update()`, `delete()` |
| `Item` | `item` | `get()`, `create()`, `update()`, `delete()` |
| `User` | `user` | `login()` |

### Factory Pattern

```php
// Instantiate actions via factory
$hostAction = $zabbix->action(Host::class);
$hosts = $hostAction->get(['output' => 'extend']);

// Type-safe - IDE autocomplete works
$history = $zabbix->action(History::class);
$data = $history->getLast24Hours(['itemId']);
```

### Persistent Authentication

**Authentication is automatic** - no manual login needed:

1. First request checks cache (`zabbix_bearer_token`)
2. If missing: auto-login via `user.login`, store token with TTL
3. If expired during request: invalidate → re-login → retry once
4. Token sent as `Authorization: Bearer <token>` header

```php
// No manual auth - handled transparently
$history = $zabbix->action(History::class);
$data = $history->getLatest(['12345']); // Auto-authenticates if needed
```

## Usage Examples

### History Data Retrieval

```php
use BytesCommerce\ZabbixApi\Actions\History;
use BytesCommerce\ZabbixApi\Enums\HistoryTypeEnum;

$history = $zabbix->action(History::class);

// Latest values (default: last 10)
$latest = $history->getLatest(
    itemIds: ['10084', '10085'],
    historyType: HistoryTypeEnum::NUMERIC_FLOAT
);

// Custom time range
$data = $history->get(
    itemIds: ['10084'],
    historyType: HistoryTypeEnum::NUMERIC_UNSIGNED,
    timeFrom: strtotime('-7 days'),
    timeTill: time(),
    limit: 500
);

// Last 24 hours (convenience method)
$recent = $history->getLast24Hours(
    itemIds: ['10084'],
    historyType: HistoryTypeEnum::LOG
);
```

### History Data Types

| Enum | Value | Use Case |
|------|-------|----------|
| `NUMERIC_FLOAT` | 0 | CPU load, temperature, percentages |
| `CHARACTER` | 1 | Short strings |
| `LOG` | 2 | Log file entries |
| `NUMERIC_UNSIGNED` | 3 | Disk space, counts (default) |
| `TEXT` | 4 | Long text values |
| `BINARY` | 5 | Base64-encoded binary (Zabbix 6.0+) |

### Host Management

```php
use BytesCommerce\ZabbixApi\Actions\Host;

$host = $zabbix->action(Host::class);

// Get hosts with filters
$hosts = $host->get([
    'output' => ['hostid', 'name', 'status'],
    'filter' => ['status' => 0],  // Active hosts only
    'limit' => 50
]);

// Create host
$result = $host->create([[
    'host' => 'New Server',
    'groups' => [['groupid' => '2']],
    'interfaces' => [[
        'type' => 1,
        'main' => 1,
        'useip' => 1,
        'ip' => '192.168.1.100',
        'dns' => '',
        'port' => '10050'
    ]]
]]);

// Update host
$host->update([[
    'hostid' => '10084',
    'status' => 0
]]);

// Delete hosts
$host->delete(['10084', '10085']);
```

### HostGroup Management

```php
use BytesCommerce\ZabbixApi\Actions\HostGroup;
use BytesCommerce\ZabbixApi\Actions\Dto\GetHostGroupDto;

$hostGroup = $zabbix->action(HostGroup::class);

// Get host groups with DTO
$dto = new GetHostGroupDto(
    groupids: ['15'],
    hostids: null,
    filter: null,
    output: 'extend',
    selectHosts: true,
    selectTemplates: null,
    sortfield: null,
    sortorder: null,
    limit: null,
    preservekeys: null,
);
$groups = $hostGroup->get($dto)->hostGroups;

// Create host group
$createDto = new CreateHostGroupDto([
    ['name' => 'Linux Servers'],
    ['name' => 'Windows Servers'],
]);
$result = $hostGroup->create($createDto);
echo "Created group IDs: " . implode(', ', $result->groupids);

// Update host group
$updateDto = new UpdateHostGroupDto([
    [
        'groupid' => '15',
        'name' => 'Linux Production Servers',
    ],
]);
$result = $hostGroup->update($updateDto);

// Delete host groups
$deleteDto = new DeleteHostGroupDto(['15', '16']);
$hostGroup->delete($deleteDto);

// Check if host group exists
$existsDto = new ExistsHostGroupDto(
    groupids: ['15'],
    hostids: null,
    filter: null,
    name: null,
);
$exists = $hostGroup->exists($existsDto)->exists;

// Get host groups by filters
$getObjectsDto = new GetObjectsHostGroupDto(
    groupids: null,
    hostids: null,
    filter: ['name' => 'Linux'],
    name: null,
);
$groups = $hostGroup->getObjects($getObjectsDto)->hostGroups;

// Check if host groups are readable
$isReadableDto = new IsReadableHostGroupDto(
    groupids: ['15'],
    hostids: null,
);
$isReadable = $hostGroup->isReadable($isReadableDto)->isReadable;

// Check if host groups are writable
$isWritableDto = new IsWritableHostGroupDto(
    groupids: ['15'],
    hostids: null,
);
$isWritable = $hostGroup->isWritable($isWritableDto)->isWritable;

// Mass add hosts to host groups
$massAddDto = new MassAddHostGroupDto(
    groups: ['15'],
    hosts: ['10084', '10085'],
    templates: null,
);
$result = $hostGroup->massAdd($massAddDto);

// Mass remove hosts from host groups
$massRemoveDto = new MassRemoveHostGroupDto(
    groupids: ['15'],
    hostids: ['10084'],
    templateids: null,
);
$result = $hostGroup->massRemove($massRemoveDto);

// Mass update host groups
$massUpdateDto = new MassUpdateHostGroupDto(
    groups: ['15'],
    hosts: ['10084', '10085'],
    templates: null,
);
$result = $hostGroup->massUpdate($massUpdateDto);
```

### Dashboard Management

```php
use BytesCommerce\ZabbixApi\Actions\Dashboard;
use BytesCommerce\ZabbixApi\Actions\Dto\GetDashboardDto;

$dashboard = $zabbix->action(Dashboard::class);

// Get dashboards with DTO
$dto = new GetDashboardDto(
    dashboardids: ['15'],
    filter: null,
    output: 'extend',
    selectPages: true,
    selectUsers: true,
    selectUserGroups: null,
    sortfield: null,
    sortorder: null,
    limit: null,
    preservekeys: null,
);
$dashboards = $dashboard->get($dto)->dashboards;

// Create dashboard
$createDto = new CreateDashboardDto([
    [
        'name' => 'Production Dashboard',
        'pages' => [
            [
                'name' => 'Overview',
                'widgets' => [],
            ],
        ],
    ],
]);
$result = $dashboard->create($createDto);
echo "Created dashboard IDs: " . implode(', ', $result->dashboardids);

// Update dashboard
$updateDto = new UpdateDashboardDto([
    [
        'dashboardid' => '15',
        'name' => 'Updated Dashboard',
    ],
]);
$result = $dashboard->update($updateDto);

// Delete dashboards
$deleteDto = new DeleteDashboardDto(['15', '16']);
$dashboard->delete($deleteDto);
```

### Item Operations

```php
use BytesCommerce\ZabbixApi\Actions\Item;
use BytesCommerce\ZabbixApi\Actions\Dto\GetItemDto;

$item = $zabbix->action(Item::class);

// Get items with DTO
$dto = new GetItemDto();
$dto->hostids = ['10084'];
$dto->output = 'extend';

$items = $item->get($dto)->items;

// Create item
$createDto = new CreateItemDto();
$createDto->items = [
    new CreateSingleItemDto(
        name: 'CPU Load',
        key_: 'system.cpu.load',
        hostid: '10084',
        type: ItemTypeEnum::ZABBIX_AGENT,
        value_type: ValueTypeEnum::NUMERIC_FLOAT,
        delay: '60s'
    )
];
$item->create($createDto);
```

### Manual AuthenticationUser Action

While automatic, you can manually trigger login if needed:

```php
use BytesCommerce\ZabbixApi\Actions\User;

$user = $zabbix->action(User::class);
$token = $user->login('username', 'password');
// Token is now cached automatically
```

## Advanced Usage

### ActionService (Backward Compatible)

For dynamic method calls:

```php
use BytesCommerce\ZabbixApi\ActionServiceInterface;
use BytesCommerce\ZabbixApi\Actions\History;

public function __construct(
    private readonly ActionServiceInterface $actionService
) {}

public function dynamicCall(): mixed
{
    return $this->actionService->call(History::class, [
        'method' => 'get',
        'params' => [
            'itemids' => ['10084'],
            'history' => 3,
            'limit' => 100
        ]
    ]);
}
```

### Direct Low-Level Client

For raw API calls:

```php
use BytesCommerce\ZabbixApi\ZabbixClientInterface;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;

public function __construct(
    private readonly ZabbixClientInterface $client
) {}

public function rawCall(): mixed
{
    return $this->client->call(
        ZabbixAction::HISTORY_GET,
        ['itemids' => ['10084'], 'history' => 0, 'limit' => 10]
    );
}
```

## Error Handling

All errors throw `ZabbixApiException` with context:

```php
use BytesCommerce\ZabbixApi\ZabbixApiException;

try {
    $result = $history->get(['10084']);
} catch (ZabbixApiException $e) {
    echo "Error: {$e->getMessage()}\n";
    echo "Code: {$e->getErrorCode()}\n";
    
    if ($data = $e->getErrorData()) {
        echo "Data: {$data}\n";
    }
}
```

### Auto-Retry on Auth Failure

Authentication errors (-32602, "Session terminated") trigger automatic recovery:

1. Cache invalidated
2. Fresh login performed
3. Original request retried once
4. If still fails: exception thrown

## Configuration Reference

### YAML (Optional)

`config/packages/zabbix_api.yaml`:

```yaml
zabbix_api:
    base_uri: '%env(ZABBIX_API_BASE_URI)%'
    username: '%env(ZABBIX_USERNAME)%'
    password: '%env(ZABBIX_PASSWORD)%'
    auth_ttl: 3600  # seconds, default: 3600
```

### Environment Variables

```bash
# Required
ZABBIX_API_BASE_URI=https://zabbix.example.com/api_jsonrpc.php
ZABBIX_USERNAME=monitoring_user
ZABBIX_PASSWORD=secure_password

# Optional
ZABBIX_AUTH_TTL=3600  # Token cache TTL in seconds
```

## Creating Custom Actions

Extend `AbstractAction` and implement `getActionPrefix()`:

```php
namespace App\Zabbix\Actions;

use BytesCommerce\ZabbixApi\Actions\AbstractAction;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;

final class CustomAction extends AbstractAction
{
    public static function getActionPrefix(): string
    {
        return 'custom';  // API prefix: custom.method
    }

    public function myMethod(array $params): array
    {
        $result = $this->client->call(
            ZabbixAction::from('custom.myMethod'),
            $params
        );

        return is_array($result) ? $result : [];
    }
}

// Usage
$custom = $zabbix->action(CustomAction::class);
$result = $custom->myMethod(['param' => 'value']);
```

## Testing

```bash
# Run tests inside Docker
docker compose exec php bash -c "vendor/bin/phpunit"

# With coverage
docker compose exec php bash -c "XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-text"

# Static analysis
docker compose exec php bash -c "vendor/bin/phpstan analyse --level=max src"
```

## Architecture

- **ZabbixService**: Factory for action instantiation
- **ZabbixClient**: Low-level HTTP client with auth management
- **Actions**: Self-contained API modules (Host, HostGroup, Dashboard, Item, History, Graph, Trigger, etc.)
- **Enums**: Type-safe constants (HistoryTypeEnum, ZabbixAction, etc.)
- **DTOs**: Structured request/response objects

## Requirements

- PHP 8.3+
- Symfony 7.4+
- Zabbix API 7+ (tested with 8.0)

## License

MIT

## Support

For issues and feature requests, please use the GitHub issue tracker.
