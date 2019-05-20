# PHP Helpers: Command-line Functions

-   Version: v1.1.0
-   Date: May 20 2019
-   [Release notes](https://github.com/pointybeard/helpers-functions-cli/blob/master/CHANGELOG.md)
-   [GitHub repository](https://github.com/pointybeard/helpers-functions-cli)

A collection of functions relating to the command-line

## Installation

This library is installed via [Composer](http://getcomposer.org/). To install, use `composer require pointybeard/helpers-functions-cli` or add `"pointybeard/helpers-functions-cli": "~1.1"` to your `composer.json` file.

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

### Requirements

This library makes use of the [PHP Helpers: Command-line Input and Input Type Handlers](https://github.com/pointybeard/helpers-cli-input), [PHP Helpers: Flag Functions](https://github.com/pointybeard/helpers-functions-flags) (`pointybeard/helpers-functions-flags`) and [PHP Helpers: String Functions](https://github.com/pointybeard/helpers-functions-strings) packages. They are installed automatically via composer.

To include all the [PHP Helpers](https://github.com/pointybeard/helpers) packages on your project, use `composer require pointybeard/helpers` or add `"pointybeard/helpers": "~1.1"` to your composer file.

## Usage

This library is a collection convenience function for command-line tasks. They are included by the vendor autoloader automatically. The functions have a namespace of `pointybeard\Helpers\Functions\Cli`

The following functions are provided:

-   `can_invoke_bash() : bool`
-   `is_su() : bool`
-   `usage(string $name, Cli\Input\InputCollection $collection) : string`
-   `manpage(string $name, string $version, string $description, string $example, Cli\Input\InputCollection $collection): string`

Example usage:

```php
<?php

declare(strict_types=1);
include __DIR__.'/vendor/autoload.php';

use pointybeard\Helpers\Cli\Input;
use pointybeard\Helpers\Functions\Cli;

var_dump(Cli\can_invoke_bash());
// bool(true)

var_dump(Cli\is_su());
// bool(false)

echo Cli\manpage(
    'test',
    '1.0.0',
    'A simple test command',
    'php -f test.php -- import -vvv -d test.json',
    (new Input\InputCollection())
        ->append(new Input\Types\Argument(
            'action',
            Input\AbstractInputType::FLAG_REQUIRED,
            'The name of the action to perform'
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
            'Path to the input JSON data'
        ))
).PHP_EOL;

// test 1.0.0, A simple test command
// Usage: test [OPTIONS]... ACTION...
//
// Mandatory values for long options are mandatory for short options too.
//
// Arguments:
//   ACTION              The name of the action to perform
//
// Options:
//   -v                                  verbosity level. -v (errors only), -vv (warnings
//                                       and errors), -vvv (everything).
//   -d, --data=VALUE                    Path to the input JSON data
//
// Examples:
//   php -f test.php -- import -vvv -d test.json

```

## Support

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/pointybeard/helpers-functions-cli/issues),
or better yet, fork the library and submit a pull request.

## Contributing

We encourage you to contribute to this project. Please check out the [Contributing documentation](https://github.com/pointybeard/helpers-functions-cli/blob/master/CONTRIBUTING.md) for guidelines about how to get involved.

## License

"PHP Helpers: Command-line Functions" is released under the [MIT License](http://www.opensource.org/licenses/MIT).
