<?php

declare(strict_types=1);

/*
 * This file is part of the "PHP Helpers: Command-line Functions" repository.
 *
 * Copyright 2019-2021 Alannah Kearney <hi@alannahkearney.com>
 *
 * For the full copyright and license information, please view the LICENCE
 * file that was distributed with this source code.
 */

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
