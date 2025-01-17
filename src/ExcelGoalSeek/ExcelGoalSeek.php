<?php

namespace P4lv\ExcelGoalSeek;

use P4lv\ExcelGoalSeek\Exception\GoalNeverReached;
use P4lv\ExcelGoalSeek\Exception\GoalReachedNotEnough;
use Psr\Log\LoggerInterface;

class ExcelGoalSeek
{
    /**
     * @var LoggerInterface|null
     */
    private $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    private function debug($message, array $context = []): void
    {
        if ($this->logger instanceof LoggerInterface) {
            $this->logger->debug($message, $context);
        }
    }

    public function calculate(
        callable $function,
        $goal,
        $decimal_places,
        $incremental_modifier = 1,
        $max_loops_round = 0,
        $max_loops_dec = 0,
        $lock_min = ['num' => null, 'goal' => null],
        $lock_max = ['num' => null, 'goal' => null],
        $slope = null,
        $randomized = false,
        $start_from = 0.1
    )
    {
        //If goal found has more than this difference, return null as it is not found
        $maximum_acceptable_difference = 0.1;
        $max_loops_round++;

        $this->checkLimitRestriction($max_loops_round);

        $this->debug(sprintf("Iteration %d; min value = %s; max value = %s; slope %s", $max_loops_round, $lock_min['num'], $lock_max['num'], $slope));

        //If I have the goal  limited to a unit, I seek decimals
        if ($lock_min['num'] !== null && $lock_max['num'] !== null && abs(abs($lock_max['num']) - abs($lock_min['num'])) <= 1) {

            //No decimal , return result
            if ($lock_min['num'] == $lock_max['num']) {
                return $lock_min['num'];
            }

            //Seek decimals
            foreach (range(1, $decimal_places, 1) as $decimal) {
                $decimal_step = 1 / (10 ** $decimal);

                $difference = abs(round(abs($lock_max['num']), $decimal) - round(abs($lock_min['num']), $decimal));

                while ($difference - ($decimal_step / 10) > $decimal_step && $max_loops_dec < (2000 * $decimal_places)) {
                    $max_loops_dec++;

                    $aux_obj_num = round(($lock_min['num'] + $lock_max['num']) / 2, $decimal);
                    $aux_obj = $function($aux_obj_num);

                    $this->debug(sprintf("Decimal iteration %d; min value = %s; max value = %s; value %s", $max_loops_dec, $lock_min['num'], $lock_max['num'], $aux_obj));

                    //Like when I look without decimals
                    [$lock_min, $lock_max] = $this->lookWithoutDecimals($aux_obj, $goal, $aux_obj_num, $lock_min, $lock_max, $slope);
                    //End Like when I look without decimals
                    $difference = abs(round(abs($lock_max['num']), $decimal) - round(abs($lock_min['num']), $decimal));
                }//End while
            }//End foreach


            if ($max_loops_dec > 2000 * $decimal_places) {
                throw new GoalNeverReached('Goal never reached [2000]');
            }

            if (!is_nan($lock_min['goal']) && abs(abs($lock_min['goal']) - abs($goal)) < $maximum_acceptable_difference) {
                return round($lock_min['num'], $decimal_places - 1);
            }

            throw new GoalReachedNotEnough('Goal reached not enough');
        }

        //First iteration, try with zero
        $aux_obj_num = $this->getAuxObjNum($lock_min['num'], $lock_max['num'], $start_from, $incremental_modifier);

        $aux_obj = $function($aux_obj_num);

        $this->debug(sprintf("Testing (with initial value) %s%d with value %s", $aux_obj_num != $start_from ? '' : '(with initial value)', $aux_obj_num, $aux_obj));

        if ($slope === null) {
            $aux_slope = $function($aux_obj_num + 0.1);

            if (is_nan($aux_slope) || is_nan($aux_obj)) {
                $slope = null; //If slope is null
            } elseif ($aux_slope - $aux_obj > 0) {
                $slope = 1;
            } else {
                $slope = -1;
            }
        }

        //Test if formule can give me non valid values, i.e.: sqrt of negative value
        if (!is_nan($aux_obj)) {
            //Is goal without decimals?
            [$lock_min, $lock_max] = $this->lookWithoutDecimals($aux_obj, $goal, $aux_obj_num, $lock_min, $lock_max, $slope);
        } else {
            if (($lock_min['num'] === null && $lock_max['num'] === null) || $randomized) {
                $nuevo_start_from = random_int(-500, 500);

                return $this->calculate($function, $goal, $decimal_places, $incremental_modifier + 1, $max_loops_round, $max_loops_dec, $lock_min, $lock_max, $slope, true, $nuevo_start_from);
            } //First iteration is null

            if ($lock_min['num'] !== null && abs(abs($aux_obj_num) - abs($lock_min['num'])) < 1) {
                $lock_max['num'] = $aux_obj_num;
            }
            if ($lock_max['num'] !== null && abs(abs($aux_obj_num) - abs($lock_max['num'])) < 1) {
                $lock_min['num'] = $aux_obj_num;
            }

            return $this->calculate($function, $goal, $decimal_places, $incremental_modifier + 1, $max_loops_round, $max_loops_dec, $lock_min, $lock_max, $slope, $randomized, $start_from);
        }

        return $this->calculate($function, $goal, $decimal_places, $incremental_modifier, $max_loops_round, $max_loops_dec, $lock_min, $lock_max, $slope, $randomized, $start_from);
    }

    private function lookWithoutDecimals($aux_obj, $goal, $aux_obj_num, $lock_min, $lock_max, $slope): array
    {
        if ($aux_obj == $goal) {
            $lock_min['num'] = $aux_obj_num;
            $lock_min['goal'] = $aux_obj;

            $lock_max['num'] = $aux_obj_num;
            $lock_max['goal'] = $aux_obj;
        }

        $going_up = false;
        if ($aux_obj < $goal) {
            $going_up = true;
        }
        if ($aux_obj > $goal) {
            $going_up = false;
        }
        if ($slope == -1) {
            $going_up = !$going_up;
        }

        if ($going_up) {
            if ($lock_min['num'] !== null && $aux_obj_num < $lock_min['num']) {
                $lock_max['num'] = $lock_min['num'];
                $lock_max['goal'] = $lock_min['goal'];
            }

            $lock_min['num'] = $aux_obj_num;
            $lock_min['goal'] = $aux_obj;
        }

        if (!$going_up) {
            if ($lock_max['num'] !== null && $lock_max['num'] < $aux_obj_num) {
                $lock_min['num'] = $lock_max['num'];
                $lock_min['goal'] = $lock_max['goal'];
            }

            $lock_max['num'] = $aux_obj_num;
            $lock_max['goal'] = $aux_obj;
        }
        return [$lock_min, $lock_max];
    }

    /**
     * @param $lockMinNum
     * @param $lockMaxNum
     * @param $start_from
     * @param $incremental_modifier
     * @return float|int|mixed
     */
    private function getAuxObjNum($lockMinNum, $lockMaxNum, $start_from, $incremental_modifier)
    {
        $aux_obj_num = null;
        if ($lockMinNum === null && $lockMaxNum === null) {
            $aux_obj_num = $start_from;
        } //Lower limit found, searching higher limit with * 10
        elseif ($lockMinNum !== null && $lockMaxNum === null) {
            if ($lockMinNum == $start_from) {
                $aux_obj_num = 1;
            } else {
                $aux_obj_num = $lockMinNum * (10 / $incremental_modifier);
            }
        } //Higher limit found, searching lower limit with * -10
        elseif ($lockMinNum === null && $lockMaxNum !== null) {
            if ($lockMaxNum == $start_from) {
                $aux_obj_num = -1;
            } else {
                $aux_obj_num = $lockMaxNum * (10 / $incremental_modifier);
            }
        } //I have both limits, searching between them without decimals
        elseif ($lockMinNum !== null && $lockMaxNum !== null) {
            $aux_obj_num = round(($lockMinNum + $lockMaxNum) / 2);
        }
        return $aux_obj_num;
    }

    /**
     * @param int $max_loops_round
     */
    private function checkLimitRestriction(int $max_loops_round): void
    {
        if ($max_loops_round > 100) {
            throw new GoalNeverReached();
        }
    }
}
