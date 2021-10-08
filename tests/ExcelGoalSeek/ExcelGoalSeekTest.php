<?php

namespace P4lv\ExcelGoalSeek;

use P4lv\ExcelGoalSeek\Exception\GoalNeverReached;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class ExcelGoalSeekTest extends TestCase
{

    protected $goalseek;

    protected function setUp(): void
    {
        $this->goalseek = new ExcelGoalSeek();
    }

    protected function tearDown(): void
    {
        unset($this->goalseek);
    }

    public static function provider()
    {
        $callbackTest = function($input) {
            $input = $input * 8;
            return $input - 12;
        };


        return [
            [$callbackTest, 302, 5, 39.25],
            [$callbackTest, 301, 5, 39.125],
            [$callbackTest, 300, 5, 39],
            [$callbackTest, -1, 5, 1.375],
        ];
    }

    public static function provider3()
    {
        $callbackTest3 = function ($input) {
            $input = $input * 8;
            return pow($input, 2);
        };

        return [
            [$callbackTest3, -1, 5, null],
        ];
    }


    /**
     * @dataProvider provider
     */
    public function testCalculated($testFunction, $goalseeked, $accuracy, $expected)
    {
        // Assert
        self::assertEquals($this->goalseek->calculate($testFunction, $goalseeked, $accuracy), $expected);
    }

    /**
     * @dataProvider provider
     */
    public function testCalculatedWithLogger($testFunction, $goalseeked, $accuracy, $expected)
    {
        $this->goalseek = new ExcelGoalSeek(new NullLogger());
        self::assertEquals($this->goalseek->calculate($testFunction, $goalseeked, $accuracy), $expected);
    }

    /**
     * @dataProvider provider3
     */
    public function testCalculated3($testFunction, $goalseeked, $accuracy, $expected)
    {
        $this->expectException(GoalNeverReached::class);
        // Assert
        self::assertEquals($this->goalseek->calculate($testFunction, $goalseeked, $accuracy), $expected);
    }


    public function testException()
    {
        $this->expectException(\TypeError::class);
        $this->goalseek->calculate('', 0, 0);
    }
}
