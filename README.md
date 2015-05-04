# PHP-Excel-GoalSeek

[![Latest Version](https://img.shields.io/github/release/davidjr82/PHP-Excel-GoalSeek.svg?style=flat-square)](https://github.com/davidjr82/PHP-Excel-GoalSeek/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/davidjr82/PHP-Excel-GoalSeek/master.svg?style=flat-square)](https://travis-ci.org/davidjr82/PHP-Excel-GoalSeek)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/davidjr82/PHP-Excel-GoalSeek.svg?style=flat-square)](https://scrutinizer-ci.com/g/davidjr82/PHP-Excel-GoalSeek/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/davidjr82/PHP-Excel-GoalSeek.svg?style=flat-square)](https://scrutinizer-ci.com/g/davidjr82/PHP-Excel-GoalSeek)
[![Total Downloads](https://img.shields.io/packagist/dt/league/PHP-Excel-GoalSeek.svg?style=flat-square)](https://packagist.org/packages/league/PHP-Excel-GoalSeek)


Utility to emulate goalseek function in PHP

## Install

Just download and check simpletest.php for a quick start

## Usage

``` php
//First, wrap your functions in your own class that extends PHPExcelGoalSeek
class GoalSeek extends \davidjr82\PHPExcelGoalSeek\PHPExcelGoalSeek {

    function callbackTest($input) {
        $inputForCallbackTest2 = $input * 8;
        return $this->callbackTest2($inputForCallbackTest2);
    }

    function callbackTest2($input) {
        $solution = $input - 12;
        return $solution;
    }
}

//Instantiate your class
$goalseek = new GoalSeek();
//$goalseek->debug = true;

//I want to know which input needs callbackTest to give me 301
$expected_result = 300;

//Calculate the input to get you goal, with accuracy
$input = $goalseek->calculate('callbackTest', $expected_result, 5);
```

## Testing

``` bash
$ phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email djimenez@e-datta.com instead of using the issue tracker.

## Credits

- [David Jiménez](https://github.com/davidjr82)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
