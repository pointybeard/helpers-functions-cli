<?php

declare(strict_types=1);

namespace pointybeard\Helpers\Functions\Cli;

use pointybeard\Helpers\Cli\Input;
use pointybeard\Helpers\Functions\Flags;
use pointybeard\Helpers\Functions\Strings;

/*
 * Checks if bash can be invoked.
 *
 * Credit to Troels Knak-Nielsen for inspiring this code.
 * (http://www.sitepoint.com/interactive-cli-password-prompt-in-php/)
 *
 * @return bool true if bash can be invoked
 */
if (!function_exists(__NAMESPACE__.'can_invoke_bash')) {
    function can_invoke_bash(): bool
    {
        return 0 === strcmp(trim(shell_exec("/usr/bin/env bash -c 'echo OK'")), 'OK');
    }
}

/*
 * Checks if script is running as root user
 *
 * @return bool true if user is root
 */
if (!function_exists(__NAMESPACE__.'is_su')) {
    function is_su(): bool
    {
        $userinfo = posix_getpwuid(posix_geteuid());

        return (bool) (0 == $userinfo['uid'] || 'root' == $userinfo['name']);
    }
}

if (!function_exists(__NAMESPACE__.'usage')) {
    function usage(string $name, Input\InputCollection $collection): string
    {
        $arguments = [];
        foreach ($collection->getArguments() as $a) {
            $arguments[] = strtoupper(
                // Wrap with square brackets if it's not required
                Flags\is_flag_set(Input\AbstractInputType::FLAG_OPTIONAL, $a->flags()) ||
                !Flags\is_flag_set(Input\AbstractInputType::FLAG_REQUIRED, $a->flags())
                    ? "[{$a->name()}]"
                    : $a->name()
            );
        }
        $arguments = trim(implode($arguments, ' '));

        return sprintf(
            'Usage: %s [OPTIONS]... %s%s',
            $name,
            $arguments,
            strlen($arguments) > 0 ? '...' : ''
        );
    }
}

if (!function_exists(__NAMESPACE__.'manpage')) {
    function manpage(string $name, string $version, string $description, string $example, Input\InputCollection $collection): string
    {
        $arguments = $options = [];

        foreach ($collection->getArguments() as $a) {
            $arguments[] = (string) $a;
        }

        foreach ($collection->getOptions() as $o) {
            $options[] = (string) $o;
        }

        $arguments = implode($arguments, PHP_EOL.'  ');
        $options = implode($options, PHP_EOL.'  ');

        return sprintf(
            '%s %s, %s
%s

Mandatory values for long options are mandatory for short options too.

Arguments:
  %s

Options:
  %s

Examples:
  %s
',
            $name,
            $version,
            Strings\utf8_wordwrap($description),
            usage($name, $collection),
            $arguments,
            $options,
            $example
    );
    }
}
