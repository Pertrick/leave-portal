<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Holiday extends Model
{
    protected $fillable = [
        'name',
        'date',
        'type',
        'location_id',
        'description',
        'is_recurring',
        'is_active',
        'recurrence_type',
        'recurrence_day',
        'recurrence_month',
        'easter_offset',
        'custom_rule'
    ];

    protected $casts = [
        'date' => 'date',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
        'custom_rule' => 'array'
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the actual date of the holiday for a given year
     *
     * @param int|null $year The year to calculate for, defaults to current year
     * @return Carbon
     */
    public function getDateForYear(?int $year = null): Carbon
    {
        $year = $year ?? Carbon::now()->year;

        if (!$this->is_recurring) {
            return $this->date;
        }

        return match ($this->recurrence_type) {
            'fixed' => $this->calculateFixedDate($year),
            'easter' => $this->calculateEasterDate($year),
            'lunar' => $this->calculateLunarDate($year),
            'custom' => $this->calculateCustomDate($year),
            default => $this->date
        };
    }

    /**
     * Calculate date for fixed recurring holidays (e.g., New Year's Day)
     */
    private function calculateFixedDate(int $year): Carbon
    {
        return Carbon::create($year, $this->recurrence_month, $this->recurrence_day);
    }

    /**
     * Calculate date for Easter-based holidays
     */
    private function calculateEasterDate(int $year): Carbon
    {
        $easterSunday = $this->calculateEasterSunday($year);
        return $easterSunday->addDays($this->easter_offset);
    }

    /**
     * Calculate Easter Sunday for a given year
     * Using Meeus/Jones/Butcher algorithm
     */
    public function calculateEasterSunday(int $year): Carbon
    {
        $a = $year % 19;
        $b = floor($year / 100);
        $c = $year % 100;
        $d = floor($b / 4);
        $e = $b % 4;
        $f = floor(($b + 8) / 25);
        $g = floor(($b - $f + 1) / 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = floor($c / 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = floor(($a + 11 * $h + 22 * $l) / 451);
        $month = floor(($h + $l - 7 * $m + 114) / 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;

        return Carbon::create($year, $month, $day);
    }

    /**
     * Calculate date for lunar-based holidays
     */
    private function calculateLunarDate(int $year): Carbon
    {
        // This is a placeholder. Implement lunar calendar calculations
        // based on your specific requirements
        return Carbon::now();
    }

    /**
     * Calculate date for custom rule-based holidays
     */
    private function calculateCustomDate(int $year): Carbon
    {
        if (!isset($this->custom_rule['type'])) {
            throw new \InvalidArgumentException('Custom rule type is required');
        }

        $rule = $this->custom_rule;
        $month = $rule['month'] ?? 1;
        $dayOfWeek = $rule['day'] ?? 1; // 1 = Monday, 7 = Sunday

        return match ($rule['type']) {
            'first' => $this->getFirstDayOfMonth($year, $month, $dayOfWeek),
            'last' => $this->getLastDayOfMonth($year, $month, $dayOfWeek),
            'nth' => $this->getNthDayOfMonth($year, $month, $dayOfWeek, $rule['n'] ?? 1),
            default => throw new \InvalidArgumentException("Invalid custom rule type: {$rule['type']}")
        };
    }

    private function getFirstDayOfMonth(int $year, int $month, int $dayOfWeek): Carbon
    {
        $date = Carbon::create($year, $month, 1);
        $daysToAdd = ($dayOfWeek - $date->dayOfWeek + 7) % 7;
        return $date->addDays($daysToAdd);
    }

    private function getLastDayOfMonth(int $year, int $month, int $dayOfWeek): Carbon
    {
        $date = Carbon::create($year, $month, 1)->endOfMonth();
        $daysToSubtract = ($date->dayOfWeek - $dayOfWeek + 7) % 7;
        return $date->subDays($daysToSubtract);
    }

    private function getNthDayOfMonth(int $year, int $month, int $dayOfWeek, int $n): Carbon
    {
        $date = $this->getFirstDayOfMonth($year, $month, $dayOfWeek);
        return $date->addWeeks($n - 1);
    }
} 