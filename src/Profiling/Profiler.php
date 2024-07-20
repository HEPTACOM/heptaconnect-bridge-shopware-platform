<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Profiling;

use Heptacom\HeptaConnect\Portal\Base\Profiling\ProfilerContract;
use Sourceability\Instrumentation\Profiler\ProfilerInterface;

final class Profiler extends ProfilerContract
{
    public function __construct(
        private readonly ProfilerInterface $profiler,
        private readonly string $prefix = '',
    ) {
    }

    #[\Override]
    public function start(string $name, ?string $kind = null): void
    {
        $this->profiler->start($name, $this->prefix . ($kind ?? ''));
    }

    #[\Override]
    public function stop(?\Throwable $exception = null): void
    {
        $this->profiler->stop($exception);
    }

    #[\Override]
    public function stopAndIgnore(): void
    {
        $this->profiler->stopAndIgnore();
    }
}
