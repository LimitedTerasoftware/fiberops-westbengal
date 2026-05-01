<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Holiday extends Model
{
    protected $fillable = [
        'name', 'date', 'type', 'applies_to',
        'state_id', 'district_id', 'block_id',
        'is_recurring', 'duration', 'substitute_date', 'description'
    ];

    protected $casts = [
        'date'            => 'date',
        'substitute_date' => 'date',
        'is_recurring'    => 'boolean',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    /**
     * Check holiday using provider directly
     * Checks all levels: national → state → district → block
     */
    public static function isHolidayForProvider($date, Provider $provider): bool
    {
        $carbon = Carbon::parse($date);

        return self::where(function ($q) use ($carbon) {
                $q->where('date', $carbon->toDateString())
                  ->orWhere(function ($q2) use ($carbon) {
                      $q2->where('is_recurring', true)
                         ->whereMonth('date', $carbon->month)
                         ->whereDay('date', $carbon->day);
                  });
            })
            ->where(function ($q) use ($provider) {
                // National — applies to everyone
                $q->where('applies_to', 'all')
                  // State level
                  ->orWhere(function ($q2) use ($provider) {
                      $q2->where('applies_to', 'state')
                         ->where('state_id', $provider->state_id);
                  })
                  // District level
                  ->orWhere(function ($q2) use ($provider) {
                      $q2->where('applies_to', 'district')
                         ->where('district_id', $provider->district_id);
                  })
                  // Block level
                  ->orWhere(function ($q2) use ($provider) {
                      $q2->where('applies_to', 'block')
                         ->where('block_id', $provider->block_id);
                  });
            })
            ->exists();
    }

    /**
     * Get all holidays for a provider in a given month
     */
    public static function getForProvider(Provider $provider, $month, $year)
    {
        return self::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where(function ($q) use ($provider) {
                $q->where('applies_to', 'all')
                  ->orWhere(function ($q2) use ($provider) {
                      $q2->where('applies_to', 'state')
                         ->where('state_id', $provider->state_id);
                  })
                  ->orWhere(function ($q2) use ($provider) {
                      $q2->where('applies_to', 'district')
                         ->where('district_id', $provider->district_id);
                  })
                  ->orWhere(function ($q2) use ($provider) {
                      $q2->where('applies_to', 'block')
                         ->where('block_id', $provider->block_id);
                  });
            })
            ->get();
    }
}

