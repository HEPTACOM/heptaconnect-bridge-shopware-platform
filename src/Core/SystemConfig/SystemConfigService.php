<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Core\SystemConfig;

use Doctrine\DBAL\Connection;
use Heptacom\HeptaConnect\Storage\ShopwareDal\Support\DateTime;
use Ramsey\Uuid\Uuid;
use Shopware\Core\Framework\Bundle;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ConfigJsonField;
use Shopware\Core\System\SystemConfig\Exception\InvalidKeyException;
use Shopware\Core\System\SystemConfig\SystemConfigService as BaseSystemConfigService;

final class SystemConfigService extends BaseSystemConfigService
{
    private const SQL_GET_ID = <<<'SQL'
SELECT id
FROM system_config
WHERE configuration_key = :key
LIMIT 1
SQL;

    private const SQL_GET_VALUE = <<<'SQL'
SELECT configuration_value
FROM system_config
WHERE configuration_key = :key
LIMIT 1
SQL;

    private const SQL_SET_VALUE = <<<'SQL'
INSERT INTO system_config (id, configuration_key, configuration_value, created_at)
VALUES (:id, :key, :value, :now)
ON DUPLICATE KEY UPDATE configuration_value = :value, updated_at = :now
SQL;

    private const SQL_DELETE_VALUE = <<<'SQL'
DELETE FROM system_config WHERE id = :id
SQL;

    /**
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function get(string $key, ?string $salesChannelId = null)
    {
        $result = $this->connection->executeQuery(self::SQL_GET_VALUE, ['key' => $key])->fetchOne();

        if (!\is_string($result)) {
            return null;
        }

        $value = \json_decode($result, true, \JSON_THROW_ON_ERROR);

        return $value[ConfigJsonField::STORAGE_KEY] ?? null;
    }

    public function set(string $key, $value, ?string $salesChannelId = null): void
    {
        $id = $this->getId($key);

        if ($value === null) {
            if ($id !== null) {
                $this->connection->executeStatement(self::SQL_DELETE_VALUE, ['id' => $id]);
            }

            return;
        }

        $value = \json_encode([ConfigJsonField::STORAGE_KEY => $value], \JSON_THROW_ON_ERROR);

        $this->connection->executeStatement(self::SQL_SET_VALUE, [
            'id' => $id === null ? Uuid::uuid4()->getBytes() : $id,
            'key' => $key,
            'value' => $value,
            'now' => DateTime::nowToStorage(),
        ]);
    }

    public function delete(string $key, ?string $salesChannel = null): void
    {
        $this->set($key, null);
    }

    public function all(?string $salesChannelId = null): array
    {
        return [];
    }

    public function getDomain(string $domain, ?string $salesChannelId = null, bool $inherit = false): array
    {
        return [];
    }

    public function savePluginConfiguration(Bundle $bundle, bool $override = false): void
    {
    }

    public function saveConfig(array $config, string $prefix, bool $override): void
    {
    }

    public function deletePluginConfiguration(Bundle $bundle): void
    {
    }

    public function deleteExtensionConfiguration(string $extensionName, array $config): void
    {
    }

    public function trace(string $key, \Closure $param)
    {
        return $param();
    }

    public function getTrace(string $key): array
    {
        return [];
    }

    private function getId(string $key): ?string
    {
        $key = $this->validate($key);

        $id = $this->connection->executeQuery(self::SQL_GET_ID, ['key' => $key])->fetchOne();

        if (!\is_string($id)) {
            return null;
        }

        return $id;
    }

    private function validate(string $key): string
    {
        $key = \trim($key);

        if ($key === '') {
            throw new InvalidKeyException('key may not be empty');
        }

        return $key;
    }
}
