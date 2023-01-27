<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Profiling;

use Heptacom\HeptaConnect\Portal\Base\Profiling\ProfilerContract;
use Sourceability\Instrumentation\Profiler\ProfilerInterface;

final class Profiler extends ProfilerContract
{
    private string $prefix;

    public function __construct(
        private ProfilerInterface $profiler,
        ?string $prefix = null
    ) {
        $this->prefix = $prefix ?? '';
    }

    public function start(string $name, ?string $kind = null): void
    {
        $this->profiler->start($name, $this->prefix . ($kind ?? ''));
    }

    public function stop(?\Throwable $exception = null): void
    {
        $this->profiler->stop($exception);
    }

    public function stopAndIgnore(): void
    {
        $this->profiler->stopAndIgnore();
    }
}
