<?php namespace App\Utilities;

use Exception;

class PercentageCalculator
{
	/**
	 * Calculate the percentage of a given number (x) and return it's percentage value.
	 *
	 * @param float $p The percentage value e.g: 50% of x
	 * @param float $x The value to calculate it's percent. e.g: 20
	 *
	 * @return float|int|false
	 */
	public static function PercentageOfX(float $p, float $x)
	{
		try {
			/* User the formula  P% * X = Y
         where:
         p => Percentage
         x => The value to calculate its percentage
         p => Is the actual computed resoult
         */

			$z = $p / 100;
			$y = $z * $x;
			return round((float) $y, 2);
		} catch (Exception $th) {
			return false;
		}
	}

	/**
	 * How to find X if P (percentage) of it is Y.
	 *
	 * @formular Y / P% = X
	 * @example Question 25 is 20% of What Number ?
	 * @param float $y The value 25
	 * @param float $p The value 20%
	 *
	 * @return float|integer|false
	 */
	public static function Find_X_If_P_Is_Y(float $y, float $p)
	{
		try {
			// 1. convert the problem to a fomular Y/P% =X
			// 2. convert the percentage to a decimal
			$p_dec = $p / 100;
			// 3. subtitute the value of $p_dec in the equation
			// 4. do the maths
			$x = $y / $p_dec;

			return $x;
		} catch (Exception $th) {
			return false;
		}
	}
}