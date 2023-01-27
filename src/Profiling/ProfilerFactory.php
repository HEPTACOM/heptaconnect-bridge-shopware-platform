<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Profiling;

use Heptacom\HeptaConnect\Portal\Base\Profiling\ProfilerContract;
use Heptacom\HeptaConnect\Portal\Base\Profiling\ProfilerFactoryContract;
use Sourceability\Instrumentation\Profiler\ProfilerInterface;

final class ProfilerFactory extends ProfilerFactoryContract
{
    public function __construct(
        private ProfilerInterface $profiler
    ) {
    }

    public function factory(?string $prefix = null): ProfilerContract
    {
        return new Profiler($this->profiler, $prefix);
    }
}
