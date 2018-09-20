<?php

namespace Llaski\NovaServerMetrics\Http\Controllers;

use Illuminate\Routing\Controller;

class MetricsController extends Controller
{
    public function index()
    {
        return [
            'disk_usage' => $this->getDiskUsage(),
            'memory_usage' => $this->getMemoryUsage(),
            'cpu_usage' => $this->getCpuUsage()
        ];
    }

    private function getDiskUsage()
    {
        $sizePrefixes = ['B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB'];
        $base = 1024;

        $totalDiskSpace = disk_total_space('/');
        $freeDiskSpace = disk_free_space('/');
        $usedDiskSpace = $totalDiskSpace - $freeDiskSpace;
        $usePercentage = round(($usedDiskSpace / $totalDiskSpace) * 100);

        $size = min((int) log($totalDiskSpace, $base), count($sizePrefixes) - 1);

        return [
            'total_space' => round($totalDiskSpace / pow($base, $size)),
            'used_space' => round($usedDiskSpace / pow($base, $size)),
            'use_percentage' => $usePercentage,
            'unit' => $sizePrefixes[$size],
        ];
    }

    private function getMemoryUsage()
    {
        if (!file_exists('/proc/meminfo')) {
            return null;
        }

        $sizePrefixes = ['KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB'];
        $base = 1024;

        foreach (file('/proc/meminfo') as $result) {
            $array = explode(':', str_replace(' ', '', $result));
            $value = preg_replace('/kb/i', '', $array[1]);
            if (preg_match('/^MemTotal/', $result)) {
                $totalMemory = str_replace("\n", '', $value); //500044
            } elseif (preg_match('/^MemFree/', $result)) {
                $freeMemory = str_replace("\n", '', $value); //57140
            }
        }

        $usedMemory = $totalMemory - $freeMemory;
        $usePercentage = round(($usedMemory / $totalMemory) * 100);

        $size = min((int) log($totalMemory, $base), count($sizePrefixes) - 1);

        return [
            'total_memory' => round($totalMemory / pow($base, $size)),
            'used_memory' => round($usedMemory / pow($base, $size)),
            'use_percentage' => $usePercentage,
            'unit' => $sizePrefixes[$size],
        ];
    }

    private function getCpuUsage()
    {
        $load = sys_getloadavg();

        return [
            'use_percentage' => round($load[0])
        ];
    }
}
