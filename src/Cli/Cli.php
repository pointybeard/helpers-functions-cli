<?php

declare(strict_types=1);

namespace pointybeard\Helpers\Functions\Cli;

use pointybeard\Helpers\Cli\Input;
use pointybeard\Helpers\Cli\Colour;
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

/**
 * Uses tput to find out the size of the window (columns and lines)
 * @return array an array containing exactly 2 items: 'cols' and 'lines'
 */
if (!function_exists(__NAMESPACE__.'get_window_size')) {
    function get_window_size(): array
    {
        return [
            'cols' => exec('tput cols'),
            'lines' => exec('tput lines'),
        ];
    }
}

if (!function_exists(__NAMESPACE__.'manpage')) {
    function manpage(string $name, string $version, string $description, Input\InputCollection $collection, $foregroundColour=Colour\Colour::FG_DEFAULT, $headingColour=Colour\Colour::FG_WHITE, array $additionalSections=[]): string
    {
        // Convienence function for wrapping a heading with colour
        $heading = function(string $input) use ($headingColour) {
            return Colour\Colour::colourise($input, $headingColour);
        };

        // Convienence function for wrapping input in a specified colour
        $colourise = function(string $input) use ($foregroundColour) {
            return Colour\Colour::colourise($input, $foregroundColour);
        };

        $sections = [
            $colourise(sprintf(
                "%s %s, %s\r\n%s\r\n",
                $name,
                $version,
                $description,
                usage($name, $collection)
            )),
        ];

        $arguments = [];
        $options = [];

        foreach ($collection->getArguments() as $a) {
            $arguments[] = (string) $a;
        }

        foreach ($collection->getOptions() as $o) {
            $options[] = (string) $o;
        }

        // Add the arguments, if there are any.
        if(false === empty($arguments)){
            $sections[] = $heading("Arguments:");
            $sections[] = $colourise(implode($arguments, PHP_EOL)) . PHP_EOL;
        }

        // Add the options, if there are any.
        if(false === empty($options)){
            $sections[] = $heading("Options:");
            $sections[] = $colourise(implode($options, PHP_EOL)) . PHP_EOL;
        }

        // Iterate over all additional items and add them as new sections
        foreach($additionalSections as $name => $contents) {
            $sections[] = $heading("{$name}:");
            $sections[] = $colourise($contents) . PHP_EOL;
        }

        return implode($sections, PHP_EOL);
    }
}
