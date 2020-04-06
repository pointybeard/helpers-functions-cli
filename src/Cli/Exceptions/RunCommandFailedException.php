<?php

declare(strict_types=1);

namespace pointybeard\Helpers\Functions\Cli\Exceptions;

use pointybeard\Helpers\Exceptions\ReadableTrace;

class RunCommandFailedException extends ReadableTrace\ReadableTraceException
{
    private $command;
    private $error;

    public function __construct(string $command, string $error, int $code = 0, \Exception $previous = null)
    {
        $this->command = $command;
        $this->error = $error;
        parent::__construct("Failed to run command. Returned: {$error}", $code, $previous);
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getError(): string
    {
        return $this->error;
    }
}
