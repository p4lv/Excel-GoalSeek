# Excel-GoalSeek

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/p4lv/Excel-GoalSeek/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/p4lv/Excel-GoalSeek/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/p4lv/Excel-GoalSeek/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/p4lv/Excel-GoalSeek/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/p4lv/Excel-GoalSeek/badges/build.png?b=master)](https://scrutinizer-ci.com/g/p4lv/Excel-GoalSeek/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/p4lv/Excel-GoalSeek/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

Utility to emulate goalseek function in PHP. This fork was made for very specific project, and there is no promise to keep any BC. 

This library is a fork of [PHP-Excel-GoalSeek](https://github.com/juanjomip/PHP-Excel-GoalSeek)

## Install

Use package from packagist.
```bash
composer require p4/excel-goalseek
```

## Usage

``` php
//Define function which for which the value should be found
$callbackTest = function callbackTest($input) {
    $inputForCallbackTest2 = $input * 8;
    return $inputForCallbackTest2 - 12;
};

//Instantiate goal seek class
$goalseek = new ExcelGoalSeek();
//$goalseek->debug = true;

//I want to know which input needs callbackTest to give me 301
$expected_result = 300;

//Calculate the input to get you goal, with accuracy
$input = $goalseek->calculate($callbackTest, $expected_result, 5);

//Voilá!
echo "\$input: " . $input . "<br />";

//Let's test our input it is close
$actual_result = $callbackTest($input);
//Searched result of function
echo "Searched result of $callbackTest(\$input) = " . $expected_result . "<br />";
//Actual result of function with calculated goalseek
echo "Actual result of $callbackTest(" . $input . ") = " . $actual_result . "<br />";
//If difference is too high, you can improve the class and send me it your modifications ;)
echo "Difference = " . ($actual_result - $expected_result);
```

## Testing

``` bash
$ phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [David Jiménez](https://github.com/davidjr82)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
