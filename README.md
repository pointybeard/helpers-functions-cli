# PHP Helpers: Command-line Functions

-   Version: v1.1.4
-   Date: May 24 2019
-   [Release notes](https://github.com/pointybeard/helpers-functions-cli/blob/master/CHANGELOG.md)
-   [GitHub repository](https://github.com/pointybeard/helpers-functions-cli)

A collection of functions relating to the command-line

## Installation

This library is installed via [Composer](http://getcomposer.org/). To install, use `composer require pointybeard/helpers-functions-cli` or add `"pointybeard/helpers-functions-cli": "~1.1"` to your `composer.json` file.

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

### Requirements

This library makes use of the [PHP Helpers: Command-line Input and Input Type Handlers](https://github.com/pointybeard/helpers-cli-input), [PHP Helpers: Flag Functions](https://github.com/pointybeard/helpers-functions-flags) (`pointybeard/helpers-functions-flags`), [PHP Helpers: Command-line Colour](https://github.com/pointybeard/helpers-cli-colours) (`pointybeard/helpers-cli-colours`) and [PHP Helpers: String Functions](https://github.com/pointybeard/helpers-functions-strings) packages. They are installed automatically via composer.

To include all the [PHP Helpers](https://github.com/pointybeard/helpers) packages on your project, use `composer require pointybeard/helpers` or add `"pointybeard/helpers": "~1"` to your composer file.

## Usage

This library is a collection convenience function for command-line tasks. They are included by the vendor autoloader automatically. The functions have a namespace of `pointybeard\Helpers\Functions\Cli`

The following functions are provided:

-   `can_invoke_bash() : bool`
-   `is_su() : bool`
-   `usage(string $name, Cli\Input\InputCollection $collection) : string`
-   `manpage(string $name, string $version, string $description, Input\InputCollection $collection, $foregroundColour=Colour\Colour::FG_DEFAULT, $headingColour=Colour\Colour::FG_WHITE, array $additional=[]): string`
-   `get_window_size(): array`

Example usage:

```php
<?php

declare(strict_types=1);
include __DIR__.'/vendor/autoload.php';

use pointybeard\Helpers\Cli\Input;
use pointybeard\Helpers\Cli\Colour\Colour;
use pointybeard\Helpers\Functions\Cli;

var_dump(Cli\can_invoke_bash());
// bool(true)

var_dump(Cli\is_su());
// bool(false)

var_dump(Cli\get_window_size());
// array(2) {
//   'cols' => string(3) "103"
//   'lines' => string(2) "68"
// }

echo Cli\manpage(
    'test',
    '1.0.0',
    'A simple test command with a really long description. This is an intentionally very long argument description so we can check that word wrapping is working correctly. It should wrap to the window',
    (new Input\InputCollection())
        ->append(new Input\Types\Argument(
            'action',
            Input\AbstractInputType::FLAG_REQUIRED,
            'The name of the action to perform. This is an intentionally very long argument description so we can check that word wrapping is working correctly'
        ))
        ->append(new Input\Types\Option(
            'v',
            null,
            Input\AbstractInputType::FLAG_OPTIONAL | Input\AbstractInputType::FLAG_TYPE_INCREMENTING,
            'verbosity level. -v (errors only), -vv (warnings and errors), -vvv (everything).',
            null,
            0
        ))
        ->append(new Input\Types\Option(
            'd',
            'data',
            Input\AbstractInputType::FLAG_OPTIONAL | Input\AbstractInputType::FLAG_VALUE_REQUIRED,
            'Path to the input JSON data.'
        )),
    Colour::FG_GREEN,
    Colour::FG_WHITE,
    [
        'Examples' => 'php -f test.php -- import -vvv -d test.json'
    ]
).PHP_EOL;

// test 1.0.0, A simple test command with a really long description. This is an intentionally very long argument description so we can check that word wrapping is working correctly. It should wrap to the window
// Usage: test [OPTIONS]... ACTION...
//
// Arguments:
// ACTION              The name of the action to perform. This is an
//                     intentionally very long argument description so we can check
//                     that word wrapping is working correctly
//
// Options:
// -v                            verbosity level. -v (errors only), -vv
//                               (warnings and errors), -vvv (everything).
// -d, --data=VALUE              Path to the input JSON data.
//
// Examples:
// php -f test.php -- import -vvv -d test.json

```

## Support

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/pointybeard/helpers-functions-cli/issues),
or better yet, fork the library and submit a pull request.

## Contributing

We encourage you to contribute to this project. Please check out the [Contributing documentation](https://github.com/pointybeard/helpers-functions-cli/blob/master/CONTRIBUTING.md) for guidelines about how to get involved.

## License

"PHP Helpers: Command-line Functions" is released under the [MIT License](http://www.opensource.org/licenses/MIT).
