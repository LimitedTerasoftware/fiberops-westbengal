public function getCompletionTrend(Request $request) {
$user = Session::get('user');
$company_id = $user->company_id;
$state_id = $user->state_id;
$district_id = $user->district_id;

$today = Carbon::today();
// Default last 7 days including today
$startDate = $request->input('from_date') ? Carbon::parse($request->input('from_date')) : $today->copy()->subDays(6);
$endDate = $request->input('to_date') ? Carbon::parse($request->input('to_date')) : $today->copy();

$fromDate = $startDate->toDateString();
$toDate = $endDate->toDateString();

// Generate Date Range Array
$period = new DatePeriod(
new DateTime($fromDate),
new DateInterval('P1D'),
new DateTime($toDate . ' 23:59:59')
);

$dates = [];
foreach ($period as $date) {
$dates[$date->format("Y-m-d")] = [
'date' => $date->format("Y-m-d"),
'frt_assigned' => 0,
'frt_completed' => 0,
'pat_assigned' => 0,
'pat_completed' => 0
];
}

// 1. ASSIGNED TICKETS (Based on master_tickets.downdate)
// Group by Date, Provider Type
$assignedQuery = DB::table('master_tickets')
->join('user_requests', 'user_requests.booking_id', '=', 'master_tickets.ticketid')
->join('providers', 'providers.id', '=', 'user_requests.provider_id')
->where('providers.company_id', $company_id)
->where('providers.state_id', $state_id)
->where('providers.status', 'approved')
->whereBetween('master_tickets.downdate', [$fromDate, $toDate])
->whereIn('providers.type', [2, 5]); // 2=FRT, 5=Patroller

if (!empty($district_id)) {
$assignedQuery->where('providers.district_id', $district_id);
}

$assignedData = $assignedQuery->select(
'master_tickets.downdate as date',
'providers.type',
DB::raw('count(*) as count')
)
->groupBy('master_tickets.downdate', 'providers.type')
->get();

foreach ($assignedData as $row) {
$d = $row->date;
if (isset($dates[$d])) {
if ($row->type == 2) {
$dates[$d]['frt_assigned'] += $row->count;
} elseif ($row->type == 5) {
$dates[$d]['pat_assigned'] += $row->count;
}
}
}

// 2. COMPLETED TICKETS (Based on user_requests.finished_at)
$completedQuery = DB::table('user_requests')
->join('providers', 'providers.id', '=', 'user_requests.provider_id')
->where('providers.company_id', $company_id)
->where('providers.state_id', $state_id)
->where('providers.status', 'approved')
->where('user_requests.status', 'COMPLETED')
->whereDate('user_requests.finished_at', '>=', $fromDate)
->whereDate('user_requests.finished_at', '<=', $toDate) ->whereIn('providers.type', [2, 5]);

    if (!empty($district_id)) {
    $completedQuery->where('providers.district_id', $district_id);
    }

    $completedData = $completedQuery->select(
    DB::raw('DATE(user_requests.finished_at) as date'),
    'providers.type',
    DB::raw('count(*) as count')
    )
    ->groupBy(DB::raw('DATE(user_requests.finished_at)'), 'providers.type')
    ->get();

    foreach ($completedData as $row) {
    $d = $row->date;
    if (isset($dates[$d])) {
    if ($row->type == 2) {
    $dates[$d]['frt_completed'] += $row->count;
    } elseif ($row->type == 5) {
    $dates[$d]['pat_completed'] += $row->count;
    }
    }
    }

    return response()->json(array_values($dates));
    }