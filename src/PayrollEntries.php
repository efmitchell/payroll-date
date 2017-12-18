<?php

namespace App;

use Carbon\Carbon;
use DateTime;

class PayrollEntries
{

	protected $year;
	// On the 1st and 15th of each month the expenses are paid
	protected $expenseDates = [1, 15];

	public function __construct($year)
	{
		$this->year = $year;
	}

	//Returns the salary dates for the year

	public function getSalaryDates()
	{
		$salaryDates = [];
		for ($i = 1; $i <= 12; $i++) {
			$ldom = $this->getDaysInMonth($this->year, $i);
			// checks if day is on a weekend and returns the correct day
			$payDay = $this->getWeekDay($this->year, $i, $ldom);

			$salaryDates[$i] = $payDay;
		}

		return $salaryDates;
	}

	// Calculate the days in every month if we have year and month
	
	public function getDaysInMonth($year, $month)
	{
		return Carbon::createFromDate($year, $month, 1)->daysInMonth;
	}

	// Get week days if type is salary then last weekday and if expenses then next monday
	
	public function getWeekDay($year, $month, $day, $type = 'salary')
	{
		$dt = Carbon::createFromDate($year, $month, $day);
		if ($dt->isWeekend()) {
			if ($type === 'salary') {
				return Carbon::parse('last weekday ' . $year . '-' . $month . '-' . $day)->toDateString();
			}

			return Carbon::parse('next monday ' . $year . '-' . $month . '-' . $day)->toDateString();
		}

		return $dt->toDateString();
	}

	// Get expense dates and assign it to this array

	public function getExpenseDates()
	{
		$expenseDates = [];

		for ($i = 1; $i <= 12; $i++) {
			$monthExpenses = [];
			foreach ($this->expenseDates as $payDate) {
				$expenseDay      = $this->getWeekDay($this->year, $i, $payDate, 'expenses');
				$monthExpenses[] = $expenseDay;
			}

			$expenseDates[$i] = $monthExpenses;
		}

		return $expenseDates;
	}

		 // Calculate and return the salary, expenses dates as table for console

	public function outputAsTable()
	{
		$salaryDates   = $this->getSalaryDates();
		$expensesDates = $this->getExpenseDates();

		$dates = [];

		for ($i = 1; $i <= 12; $i++) {
			$monthName = DateTime::createFromFormat('!m', $i)->format('F');
			$dates[]   = [$monthName, $expensesDates[$i][0], $expensesDates[$i][1], $salaryDates[$i]];
		}

		return $dates;
	}
	
	 // Calculate and output the salary, expenses dates as table for console

	public function outputToFile()
	{
		$salaryDates   = $this->getSalaryDates();
		$expensesDates = $this->getExpenseDates();


		$dates = "Month Name, 1st Expenses Day, 2nd Expenses Day, Salary Day \n";

		for ($i = 1; $i <= 12; $i++) {
			$monthName = DateTime::createFromFormat('!m', $i)->format('F');
			$dates     .= sprintf(
				"%s, %s, %s, %s \n",
				$monthName, $expensesDates[$i][0], $expensesDates[$i][1], $salaryDates[$i]
				);
		}

		return $dates;
	}
}