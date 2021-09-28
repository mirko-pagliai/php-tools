# php-tools

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)
[![CI](https://github.com/mirko-pagliai/php-tools/actions/workflows/ci.yml/badge.svg)](https://github.com/mirko-pagliai/php-tools/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/mirko-pagliai/php-tools/branch/master/graph/badge.svg)](https://codecov.io/gh/mirko-pagliai/php-tools)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/d39ca5f3a31c4f619afd8efabaddf2c2)](https://www.codacy.com/manual/mirko.pagliai/php-tools?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=mirko-pagliai/php-tools&amp;utm_campaign=Badge_Grade)
[![CodeFactor](https://www.codefactor.io/repository/github/mirko-pagliai/php-tools/badge)](https://www.codefactor.io/repository/github/mirko-pagliai/php-tools)

*php-tools* adds some useful global functions, classes and methods, trait and exceptions.
Refer to our [API](//mirko-pagliai.github.io/php-tools) to discover them all.

Did you like this plugin? Its development requires a lot of time for me.
Please consider the possibility of making [a donation](//paypal.me/mirkopagliai):
even a coffee is enough! Thank you.

[![Make a donation](https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_paypal_carte.jpg)](//paypal.me/mirkopagliai)

## Installation
You can install the package via composer:

    $ composer require --prefer-dist mirko-pagliai/php-tools

### Installation on older PHP versions
Recent packages and the master branch require at least PHP 7.
Instead, the [php5.6](//github.com/mirko-pagliai/php-tools/tree/php5.6) branch
requires at least PHP 5.6.

In this case, you can install the package as well:
```bash
$ composer require --prefer-dist mirko-pagliai/php-tools:dev-php5.6
```

Note that the `php5.6` branch will no longer be updated as of April 22, 2021,
except for security patches, and it matches the
[1.4.8](https://github.com/mirko-pagliai/php-tools/releases/tag/1.4.8) version.

## Versioning
For transparency and insight into our release cycle and to maintain backward
compatibility, *php-tools* will be maintained under the
[Semantic Versioning guidelines](http://semver.org).
