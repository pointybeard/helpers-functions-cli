# PHP Helpers: Command-line Functions

-   Version: v1.0.1
-   Date: May 09 2019
-   [Release notes](https://github.com/pointybeard/helpers-functions-cli/blob/master/CHANGELOG.md)
-   [GitHub repository](https://github.com/pointybeard/helpers-functions-cli)

A collection of functions relating to the command-line

## Installation

This library is installed via [Composer](http://getcomposer.org/). To install, use `composer require pointybeard/helpers-functions-cli` or add `"pointybeard/helpers-functions-cli": "~1.0"` to your `composer.json` file.

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

### Requirements

There are no particuar requirements for this library other than PHP 5.6 or greater.

To include all the [PHP Helpers](https://github.com/pointybeard/helpers) packages on your project, use `composer require pointybeard/helpers` or add `"pointybeard/helpers": "~1.0"` to your composer file.

## Usage

This library is a collection convenience function for command-line tasks. They are included by the vendor autoloader automatically. The functions have a namespace of `pointybeard\Helpers\Functions\Cli`

The following functions are provided:

-   `can_invoke_bash() : bool`
-   `is_su() : bool`

Example usage:

```php
<?php

include __DIR__ . '/vendor/autoload.php';

use pointybeard\Helpers\Functions\Cli;

var_dump(Cli\can_invoke_bash());
// bool(true)

var_dump(Cli\is_su());
// bool(false)
```

## Support

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/pointybeard/helpers-functions-cli/issues),
or better yet, fork the library and submit a pull request.

## Contributing

We encourage you to contribute to this project. Please check out the [Contributing documentation](https://github.com/pointybeard/helpers-functions-cli/blob/master/CONTRIBUTING.md) for guidelines about how to get involved.

## License

"PHP Helpers: Command-line Functions" is released under the [MIT License](http://www.opensource.org/licenses/MIT).
