# Excel-GoalSeek

Utility to emulate goalseek function in PHP. This fork was made for very specific project, and there is no promise to keep any BC. 

## Install

Use package from packagist.

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

//Voilá!
echo "\$input: " . $input . "<br />";

//Let's test our input it is close
$actual_result = $goalseek->callbackTest($input);
//Searched result of function
echo "Searched result of callbackTest(\$input) = " . $expected_result . "<br />";
//Actual result of function with calculated goalseek
echo "Actual result of callbackTest(" . $input . ") = " . $actual_result . "<br />";
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
