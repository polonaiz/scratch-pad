<?php

namespace ScratchPad\Logger;

use PHPUnit\Framework\TestCase;

class CompositeLoggerTest extends TestCase
{
    public function test()
    {
        $logger = new CompositeLogger(
            [
                'defaults' => [
                    'timestamp' => CompositeLogger::getTimeStamper(),
                    'host' => gethostname(),
                    'program' => 'testcase',
                    'pid' => getmypid()
                ],
                'loggerFilterPairs' => [
                    [
                        'logger' => $memoryLoggerAll = new MemoryLogger(),
                        'filter' => CompositeLogger::getSelectorAll()
                    ],
                    [
                        'logger' => $memoryLoggerNoticeOrCritical = new MemoryLogger(),
                        'filter' => CompositeLogger::getSelectorLevel(['notice', 'critical'])
                    ],
                    [
                        'logger' => $memoryLoggerCritical = new MemoryLogger(),
                        'filter' => CompositeLogger::getSelectorLevelCritical()
                    ]]
            ]);
        $logger->log(['noLevel' => '1']);
        $logger->log(['level' => 'info', 'type' => 'inform']);
        $logger->log(['level' => 'notice', 'type' => 'noticeMessage']);
        $logger->log(['level' => 'critical', 'type' => 'criticalLog']);

//        echo json_encode($memoryLoggerAll->logs, JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL;
//        echo json_encode($memoryLoggerCritical->logs) . PHP_EOL . PHP_EOL;
        $this->assertEquals(4, count($memoryLoggerAll->logs));
        $this->assertEquals(2, count($memoryLoggerNoticeOrCritical->logs));
        $this->assertEquals(1, count($memoryLoggerCritical->logs));
    }
}
