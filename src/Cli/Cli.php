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

namespace pointybeard\Helpers\Functions\Cli;

use Exception;
use pointybeard\Helpers\Cli\Colour;
use pointybeard\Helpers\Cli\Input;
use pointybeard\Helpers\Functions\Arrays;
use pointybeard\Helpers\Functions\Debug;
use pointybeard\Helpers\Functions\Flags;
use pointybeard\Helpers\Functions\Strings;

/*
 * Uses proc_open() to run a command on the shell. Output and errors are captured
 * and returned. If the command "fails" to run (i.e. return code is != 0), this
 * function will throw an exception.
 *
 * Note that some commands will return a non-zero status code to signify, for
 * example, no results found. This function is unable to tell the difference and
 * will trigger an exception regardless. In this instance, It is advised to trap
 * that exception and inspect both $stderr and $stdout to decide if it was
 * actually due to failed command execution.
 *
 * @param string $command the full bash command to run
 * @param string $stdout  (optional) reference to capture output from STDOUT
 * @param string $stderr  (optional) reference to capture output from STDERR
 * #param string $exitCode (options) reference to capture the command exit code
 *
 * @throws RunCommandFailedException
 */
if (!function_exists(__NAMESPACE__.'\run_command')) {
    function run_command(string $command, string &$stdout = null, string &$stderr = null, int &$exitCode = null): void
    {
        $pipes = null;
        $exitCode = null;

        $proc = proc_open(
            "{$command};echo $? >&3",
            [
                0 => ['pipe', 'r'], // STDIN
                1 => ['pipe', 'w'], // STDOUT
                2 => ['pipe', 'w'], // STDERR
                3 => ['pipe', 'w'], // Used to capture the exit code
            ],
            $pipes,
            getcwd(),
            null
        );

        // Close STDIN stream
        fclose($pipes[0]);

        // (guard) proc_open failed to return a resource
        if (false == is_resource($proc)) {
            throw new Exceptions\RunCommandFailedException($command, 'proc_open() returned FALSE.');
        }

        // Get contents of STDOUT and close stream
        $stdout = trim(stream_get_contents($pipes[1]));
        fclose($pipes[1]);

        // Get contents od STDERR and close stream
        $stderr = trim(stream_get_contents($pipes[2]));
        fclose($pipes[2]);

        // Grab the exit code then close the stream
        if (false == feof($pipes[3])) {
            $exitCode = (int) trim(stream_get_contents($pipes[3]));
        }
        fclose($pipes[3]);

        // Close the process we created
        proc_close($proc);

        // (guard) proc_close return indiciated a failure
        if (0 != $exitCode) {
            // There was some kind of error. Throw an exception.
            // If STDERR is empty, in effort to give back something
            // meaningful, grab contents of STDOUT instead
            throw new Exceptions\RunCommandFailedException($command, true == empty(trim($stderr)) ? $stdout : $stderr);
        }
    }
}

/*
 * Returns the pathname for a specified command (or null if it cannot be found)
 *
 * @params $command the name of the command to look for
 *
 * @returns string|null
 */
if (!function_exists(__NAMESPACE__.'\which')) {
    function which(string $command): ?string
    {
        try {
            run_command("which {$command}", $output);
        } catch (Exception $ex) {
            $output = null;
        }

        return $output;
    }
}

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

/*
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

if (!function_exists(__NAMESPACE__.'usage')) {
    function usage(string $name, Input\InputCollection $collection): string
    {
        $arguments = [];
        foreach ($collection->getItemsByType('Argument') as $a) {
            $arguments[] = strtoupper(
                // Wrap with square brackets if it's not required
                Flags\is_flag_set(Input\AbstractInputType::FLAG_OPTIONAL, $a->flags()) ||
                !Flags\is_flag_set(Input\AbstractInputType::FLAG_REQUIRED, $a->flags())
                    ? "[{$a->name()}]"
                    : $a->name()
            );
        }
        $arguments = trim(implode(' ', $arguments));

        return sprintf(
            'Usage: %s [OPTIONS]... %s%s',
            $name,
            $arguments,
            strlen($arguments) > 0 ? '...' : ''
        );
    }
}

if (!function_exists(__NAMESPACE__.'manpage')) {
    function manpage(string $name, string $version, string $description, Input\InputCollection $collection, $foregroundColour = Colour\Colour::FG_DEFAULT, $headingColour = Colour\Colour::FG_WHITE, array $additionalSections = []): string
    {
        // Convienence function for wrapping a heading with colour
        $heading = function (string $input) use ($headingColour) {
            return Colour\Colour::colourise($input, $headingColour);
        };

        // Convienence function for wrapping input in a specified colour
        $colourise = function (string $input) use ($foregroundColour) {
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

        foreach ($collection->getItemsByType('Argument') as $a) {
            $arguments[] = (string) $a;
        }

        foreach ($collection->getItemsExcludeByType('Argument') as $o) {
            $options[] = (string) $o;
        }

        // Add the arguments, if there are any.
        if (false === empty($arguments)) {
            $sections[] = $heading('Arguments:');
            $sections[] = $colourise(implode(PHP_EOL, $arguments)).PHP_EOL;
        }

        // Add the options, if there are any.
        if (false === empty($options)) {
            $sections[] = $heading('Options:');
            $sections[] = $colourise(implode(PHP_EOL, $options)).PHP_EOL;
        }

        // Iterate over all additional items and add them as new sections
        foreach ($additionalSections as $name => $contents) {
            $sections[] = $heading("{$name}:");
            $sections[] = $colourise($contents).PHP_EOL;
        }

        return implode(PHP_EOL, $sections);
    }
}

if (!function_exists(__NAMESPACE__."\display_error_and_exit")) {
    function display_error_and_exit($message, $heading = 'Error', $background = Colour\Colour::BG_RED, ?array $trace = null): void
    {
        $padCharacter = ' ';
        $paddingBufferSize = 0.15; // 15%
        $minimumWindowWidth = 40;
        $edgePaddingLength = 5;
        $edgePadding = str_repeat($padCharacter, $edgePaddingLength);

        // Convenience function for adding the background to a line.
        $add_background = function (string $string, bool $bold = false) use ($edgePadding, $background): string {
            $string = $edgePadding.$string.$edgePadding;

            return Colour\Colour::colourise(
                $string,
                (
                    true == $bold
                        ? Colour\Colour::FG_WHITE
                        : Colour\Colour::FG_DEFAULT
                ),
                $background
            );
        };

        // Get the window dimensions but restrict width to minimum
        // of $minimumWindowWidth
        $window = get_window_size();
        $window['cols'] = max($minimumWindowWidth, $window['cols']);

        // This shrinks the total line length (derived by the window width) by
        // $paddingBufferSize
        $paddingBuffer = (int) ceil($window['cols'] * $paddingBufferSize);

        $lineLength = $window['cols'] - (2 * $edgePaddingLength) - $paddingBuffer;

        $emptyLine = $add_background(str_repeat($padCharacter, $lineLength), true);
        $heading = Strings\mb_str_pad(trim($heading), $lineLength, $padCharacter, \STR_PAD_RIGHT);

        $message = Strings\utf8_wordwrap_array($message, $lineLength, PHP_EOL, true);

        // Remove surrounding whitespace
        $message = array_map('trim', $message);

        // Remove empty elements from the array
        $message = Arrays\array_remove_empty($message);

        // Reset array indicies
        $message = array_values($message);

        // Wrap everything in red
        for ($ii = 0; $ii < count($message); ++$ii) {
            $message[$ii] = $add_background(Strings\mb_str_pad(
                $message[$ii],
                mb_strlen($heading),
                $padCharacter,
                \STR_PAD_RIGHT
            ));
        }

        // Add an empty red line at the end
        array_push($message, $emptyLine);

        // Print the error message, starting with an empty red line
        printf(
            "\r\n%s\r\n%s\r\n%s\r\n%s",
            $emptyLine,
            $add_background($heading, true),
            implode(PHP_EOL, $message),
            !empty($trace) && count($trace) > 0
                ? PHP_EOL.sprintf("Trace\r\n==========\r\n%s\r\n", Debug\readable_debug_backtrace($trace))
                : ''
        );

        exit(1);
    }
}
