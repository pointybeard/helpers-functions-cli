<?php

namespace pointybeard\Helpers\Functions\Cli;

/**
 * Checks if bash can be invoked.
 *
 * Credit to Troels Knak-Nielsen for inspiring this code.
 * (http://www.sitepoint.com/interactive-cli-password-prompt-in-php/)
 *
 * @return bool true if bash can be invoked
 */
if (!function_exists(__NAMESPACE__ . 'can_invoke_bash')) {
    function can_invoke_bash()
    {
        return (strcmp(trim(shell_exec("/usr/bin/env bash -c 'echo OK'")), 'OK') === 0);
    }
}

/**
 * Checks if script is running as root user
 *
 * @return bool true if user is root
 */
if (!function_exists(__NAMESPACE__ . 'is_su')) {
    function is_su()
    {
        $userinfo = posix_getpwuid(posix_geteuid());
        return (bool)($userinfo['uid'] == 0 || $userinfo['name'] == 'root');
    }
}
