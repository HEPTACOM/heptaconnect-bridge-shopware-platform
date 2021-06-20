<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Profiling;

use Heptacom\HeptaConnect\Portal\Base\Profiling\ProfilerContract;
use Heptacom\HeptaConnect\Portal\Base\Profiling\ProfilerFactoryContract;
use Sourceability\Instrumentation\Profiler\ProfilerInterface;

class ProfilerFactory extends ProfilerFactoryContract
{
    private ProfilerInterface $profiler;

    public function __construct(ProfilerInterface $profiler)
    {
        $this->profiler = $profiler;
    }

    public function factory(?string $prefix = null): ProfilerContract
    {
        return new Profiler($this->profiler, $prefix);
    }
}
