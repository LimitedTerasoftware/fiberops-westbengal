<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class AutoLogoutProviders extends Command
{
    protected $signature = 'attendance:autologout';
    protected $description = 'Automatically logout all active providers at 11:45 PM daily';

    public function handle()
    {
        try {
            $today = Carbon::today()->toDateString();

            $attendances = Attendance::whereDate('created_at', $today)
                ->where('status', 'active')
                ->get();

            if ($attendances->count() == 0) {
                Log::info("AutoLogout: No active providers found for {$today}");
                return;
            }

            foreach ($attendances as $attendance) {
                $attendance->update(['status' => 'offline']);
            }

            Log::info("AutoLogout: Successfully logged out {$attendances->count()} providers on {$today}");

        } catch (\Exception $e) {
            Log::error("AutoLogout FAILED: " . $e->getMessage());
        }

    }
}
