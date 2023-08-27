<?php

namespace Yansongda\Supports\Logger;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class StdoutHandler extends AbstractProcessingHandler
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * Bootstrap.
     *
     * @param int  $level
     * @param bool $bubble
     */
    public function __construct($level = Logger::DEBUG, $bubble = true, ?OutputInterface $output = null)
    {
        $this->output = $output ?? new ConsoleOutput();
        parent::__construct($level, $bubble);
    }

    /**
     * Writes the record down to the log of the implementing handler.
     */
    protected function write(array $record): void
    {
        $this->output->writeln($record['formatted']);
    }
}
