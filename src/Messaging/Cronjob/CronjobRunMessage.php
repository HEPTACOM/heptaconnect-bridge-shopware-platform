<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Messaging\Cronjob;

use Shopware\Core\Framework\Struct\Struct;

class CronjobRunMessage extends Struct
{
    protected string $runId;

    public function __construct(string $runId)
    {
        $this->runId = $runId;
    }

    public function getRunId(): string
    {
        return $this->runId;
    }
}
