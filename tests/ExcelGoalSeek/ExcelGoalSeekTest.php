<?php

namespace P4lv\ExcelGoalSeek;

use P4lv\ExcelGoalSeek\Exception\ExcelGoalSeekException;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class GoalSeek extends ExcelGoalSeek {

    function callbackTest($input) {
        $inputForCallbackTest2 = $input * 8;
        return $this->callbackTest2($inputForCallbackTest2);
    }
    function callbackTest2($input) {
        $solution = $input - 12;
        return $solution;
    }

    function callbackTest3($input) {
        $inputForCallbackTest4 = $input * 8;
        return $this->callbackTest4($inputForCallbackTest4);
    }
    function callbackTest4($input) {
        $solution = pow($input, 2);
        return $solution;
    }

}

class ExcelGoalSeekTest extends TestCase
{

    protected $goalseek;

    protected function setUp(): void
    {
        $this->goalseek = new GoalSeek();
    }

    protected function tearDown(): void
    {
        unset($this->goalseek);
    }

    public static function provider()
    {
        return array(
            array('callbackTest', 302, 5, 39.25),
            array('callbackTest', 301, 5, 39.125),
            array('callbackTest', 300, 5, 39),
            array('callbackTest', -1, 5, 1.375),
            );
    }

    public static function provider3()
    {
        return array(
            array('callbackTest3', -1, 5, null),
            );
    }


    /**
     * @dataProvider provider
     */
    public function testCalculated($testFunction, $goalseeked, $accuracy, $expected)
    {
        // Assert
        $this->assertEquals($this->goalseek->calculate($testFunction, $goalseeked, $accuracy), $expected);
    }    /**
     * @dataProvider provider
     */
    public function testCalculatedWithLogger($testFunction, $goalseeked, $accuracy, $expected)
    {
        $this->goalseek = new GoalSeek(new NullLogger());
        $this->assertEquals($this->goalseek->calculate($testFunction, $goalseeked, $accuracy), $expected);
    }

    /**
     * @dataProvider provider3
     */
    public function testCalculated3($testFunction, $goalseeked, $accuracy, $expected)
    {
        // Assert
        $this->assertEquals($this->goalseek->calculate($testFunction, $goalseeked, $accuracy), $expected);
    }


    public function testException()
    {
        $this->expectException(ExcelGoalSeekException::class);
        $this->goalseek->calculate('', 0, 0);
    }
}
