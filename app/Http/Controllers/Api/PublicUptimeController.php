<?php

namespace App\Http\Controllers\Api;

use App\BlockRouterUptime;
use App\GpRouterUptime;
use App\Http\Controllers\Controller;
use App\OltUptime;
use App\OntUptime;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PublicUptimeController extends Controller
{
    public function states()
    {
        $states = DB::table('states')
            ->select('state_id as id', 'state_id', 'state_name as name')
            ->orderBy('state_name', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'type' => 'filter',
            'source' => 'states',
            'data' => $states,
            'averages' => null,
            'pagination' => null,
        ]);
    }

    public function districts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'state_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $districts = DB::table('districts')
            ->select('id', 'state_id', 'name')
            ->where('state_id', $request->get('state_id'))
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'type' => 'filter',
            'source' => 'districts',
            'filters' => [
                'state_id' => $request->get('state_id'),
            ],
            'data' => $districts,
            'averages' => null,
            'pagination' => null,
        ]);
    }

    public function blocks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'district_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $blocks = DB::table('blocks')
            ->select('id', 'district_id', 'name')
            ->where('district_id', $request->get('district_id'))
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'type' => 'filter',
            'source' => 'blocks',
            'filters' => [
                'district_id' => $request->get('district_id'),
            ],
            'data' => $blocks,
            'averages' => null,
            'pagination' => null,
        ]);
    }

    public function ontDashboard(Request $request)
    {
        return $this->dashboard($request, 'ont');
    }

    public function ontPerformance(Request $request)
    {
        return $this->performance($request, 'ont');
    }

    public function oltDashboard(Request $request)
    {
        return $this->dashboard($request, 'olt');
    }

    public function oltPerformance(Request $request)
    {
        return $this->performance($request, 'olt');
    }

    public function samriddhDashboard(Request $request)
    {
        return $this->dashboard($request, 'samriddh');
    }

    public function samriddhPerformance(Request $request)
    {
        return $this->performance($request, 'samriddh');
    }

    public function gpRouterDashboard(Request $request)
    {
        return $this->dashboard($request, 'gp_router');
    }

    public function gpRouterPerformance(Request $request)
    {
        return $this->performance($request, 'gp_router');
    }

    public function blockRouterDashboard(Request $request)
    {
        return $this->dashboard($request, 'block_router');
    }

    public function blockRouterPerformance(Request $request)
    {
        return $this->performance($request, 'block_router');
    }

    private function dashboard(Request $request, $source)
    {
        $validation = $this->validatePublicRequest($request);
        if ($validation) {
            return $validation;
        }

        $config = $this->sourceConfig($source);
        $periodSql = $this->getPeriodSql($request->get('period', 'day'), $config['date_column']);
        $query = $this->baseQuery($source);

        $this->applyLocationFilters($query, $request, $config);
        $this->applyDateFilters($query, $request, $config['date_column']);

        $uptime = $config['uptime_column'];
        $query
            ->selectRaw($periodSql['label'] . ' as label')
            ->selectRaw('MIN(DATE(' . $config['date_column'] . ')) as day')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('ROUND(AVG(' . $uptime . '), 2) as avg_uptime')
            ->selectRaw('SUM(CASE WHEN ' . $uptime . ' >= 98 THEN 1 ELSE 0 END) as gte98')
            ->selectRaw('SUM(CASE WHEN ' . $uptime . ' >= 90 AND ' . $uptime . ' < 98 THEN 1 ELSE 0 END) as gte90')
            ->selectRaw('SUM(CASE WHEN ' . $uptime . ' >= 75 AND ' . $uptime . ' < 90 THEN 1 ELSE 0 END) as gte75')
            ->selectRaw('SUM(CASE WHEN ' . $uptime . ' >= 50 AND ' . $uptime . ' < 75 THEN 1 ELSE 0 END) as gte50')
            ->selectRaw('SUM(CASE WHEN ' . $uptime . ' >= 20 AND ' . $uptime . ' < 50 THEN 1 ELSE 0 END) as gte20')
            ->selectRaw('SUM(CASE WHEN ' . $uptime . ' < 20 THEN 1 ELSE 0 END) as lt20')
            ->selectRaw('SUM(CASE WHEN ' . $uptime . ' >= 90 THEN 1 ELSE 0 END) as gte90_total')
            ->selectRaw('SUM(CASE WHEN ' . $uptime . ' >= 50 AND ' . $uptime . ' < 90 THEN 1 ELSE 0 END) as gte50_lt90')
            ->selectRaw('SUM(CASE WHEN ' . $uptime . ' < 50 THEN 1 ELSE 0 END) as lt50_total')
            ->selectRaw('ROUND(SUM(CASE WHEN ' . $uptime . ' >= 98 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as pct_gte98')
            ->selectRaw('ROUND(SUM(CASE WHEN ' . $uptime . ' >= 90 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as pct_gte90')
            ->selectRaw('ROUND(SUM(CASE WHEN ' . $uptime . ' >= 50 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as pct_gte50')
            ->selectRaw('ROUND(SUM(CASE WHEN ' . $uptime . ' < 50 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as pct_lt50');

        if ($config['supports_router_metrics']) {
            $query
                ->selectRaw('SUM(CASE WHEN ' . $uptime . ' > 0 AND ' . $uptime . ' < 98 THEN 1 ELSE 0 END) as lt98')
                ->selectRaw('SUM(CASE WHEN ' . $uptime . ' = 0 THEN 1 ELSE 0 END) as zero_availability')
                ->selectRaw($this->integrationSql($config['table']) . ' as integration');
        }

        $data = $query
            ->groupBy(DB::raw($periodSql['group']), DB::raw($periodSql['label']))
            ->orderByRaw($periodSql['sort'] . ' asc')
            ->get();

        return $this->successResponse('dashboard', $source, $request, $data, $this->averages($data, $config['supports_router_metrics']), null);
    }

    private function performance(Request $request, $source)
    {
        $validation = $this->validatePublicRequest($request, true);
        if ($validation) {
            return $validation;
        }

        $config = $this->sourceConfig($source);
        $query = $this->baseQuery($source);

        $this->applyLocationFilters($query, $request, $config);
        $this->applyDateFilters($query, $request, $config['date_column']);

        foreach ($config['performance_selects'] as $select) {
            $query->addSelect(DB::raw($select));
        }

        $perPage = min((int) $request->get('per_page', 10), 100);
        $records = $query
            ->orderBy($config['date_column'], 'desc')
            ->paginate($perPage);

        return $this->successResponse(
            'performance',
            $source,
            $request,
            collect($records->items())->values(),
            null,
            $this->pagination($records)
        );
    }

    private function validatePublicRequest(Request $request, $allowPagination = false)
    {
        $rules = [
            'state_id' => 'required|integer',
            'district_id' => 'nullable|integer',
            'block_id' => 'nullable|integer',
            'month' => 'nullable|date_format:Y-m',
            'fromDate' => 'nullable|date',
            'toDate' => 'nullable|date',
            'period' => 'nullable|in:day,week,month,year',
        ];

        if ($allowPagination) {
            $rules['page'] = 'nullable|integer|min:1';
            $rules['per_page'] = 'nullable|integer|min:1|max:100';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
      if ($request->has('fromDate') xor $request->has('toDate')) {
            return response()->json([
                'status' => false,
                'message' => 'Both fromDate and toDate are required when filtering by date range.',
            ], 422);
        }

        return null;
    }

    private function sourceConfig($source)
    {
        $configs = [
            'ont' => [
                'table' => 'ont_uptime',
                'date_column' => 'ont_uptime.record_date',
                'uptime_column' => 'ont_uptime.uptime_percent',
                'state_column' => 'gp_list.state_id',
                'district_column' => 'gp_list.district_id',
                'block_column' => 'gp_list.block_id',
                'supports_router_metrics' => false,
                'performance_selects' => [
                    "'ont' as source",
                    'ont_uptime.id',
                    'gp_list.state_id',
                    'gp_list.district_id',
                    'gp_list.block_id',
                    'districts.name as district_name',
                    'blocks.name as block_name',
                    'gp_list.gp_name as asset_name',
                    'gp_list.gp_name',
                    'gp_list.lgd_code',
                    'ont_uptime.uptime_percent',
                    'ont_uptime.record_date',
                ],
            ],
            'samriddh' => [
                'table' => 'ont_uptime',
                'date_column' => 'ont_uptime.record_date',
                'uptime_column' => 'ont_uptime.uptime_percent',
                'state_column' => 'gp_list.state_id',
                'district_column' => 'gp_list.district_id',
                'block_column' => 'gp_list.block_id',
                'supports_router_metrics' => false,
                'performance_selects' => [
                    "'samriddh' as source",
                    'ont_uptime.id',
                    'gp_list.state_id',
                    'gp_list.district_id',
                    'gp_list.block_id',
                    'districts.name as district_name',
                    'blocks.name as block_name',
                    'gp_list.gp_name as asset_name',
                    'gp_list.gp_name',
                    'gp_list.lgd_code',
                    'ont_uptime.uptime_percent',
                    'ont_uptime.record_date',
                ],
            ],
            'olt' => [
                'table' => 'olt_uptime',
                'date_column' => 'olt_uptime.record_date',
                'uptime_column' => 'olt_uptime.uptime_percent',
                'state_column' => 'olt_locations.state_id',
                'district_column' => 'olt_locations.district_id',
                'block_column' => 'olt_locations.block_id',
                'supports_router_metrics' => false,
                'performance_selects' => [
                    "'olt' as source",
                    'olt_uptime.id',
                    'olt_locations.state_id',
                    'olt_locations.district_id',
                    'olt_locations.block_id',
                    'districts.name as district_name',
                    'blocks.name as block_name',
                    'olt_locations.olt_location as asset_name',
                    'olt_locations.olt_location',
                    'olt_locations.no_of_gps',
                    'olt_uptime.lgd_code',
                    'olt_uptime.uptime_percent',
                    'olt_uptime.record_date',
                ],
            ],
            'gp_router' => [
                'table' => 'gp_router_uptime',
                'date_column' => 'gp_router_uptime.record_date',
                'uptime_column' => 'gp_router_uptime.uptime_percent',
                'state_column' => 'gp_list.state_id',
                'district_column' => 'gp_list.district_id',
                'block_column' => 'gp_list.block_id',
                'supports_router_metrics' => true,
                'performance_selects' => [
                    "'gp_router' as source",
                    'gp_router_uptime.id',
                    'gp_list.state_id',
                    'gp_list.district_id',
                    'gp_list.block_id',
                    'districts.name as district_name',
                    'blocks.name as block_name',
                    'gp_list.gp_name as asset_name',
                    'gp_list.gp_name',
                    'gp_list.lgd_code',
                    'gp_router_uptime.uptime_percent',
                    'gp_router_uptime.record_date',
                ],
            ],
            'block_router' => [
                'table' => 'block_router_uptime',
                'date_column' => 'block_router_uptime.record_date',
                'uptime_column' => 'block_router_uptime.uptime_percent',
                'state_column' => 'districts.state_id',
                'district_column' => 'blocks.district_id',
                'block_column' => 'blocks.id',
                'supports_router_metrics' => true,
                'performance_selects' => [
                    "'block_router' as source",
                    'block_router_uptime.id',
                    'districts.state_id',
                    'blocks.district_id',
                    'blocks.id as block_id',
                    'districts.name as district_name',
                    'blocks.name as block_name',
                    'blocks.name as asset_name',
                    'block_router_uptime.lgd_code',
                    'block_router_uptime.uptime_percent',
                    'block_router_uptime.record_date',
                ],
            ],
        ];

        return $configs[$source];
    }

    private function baseQuery($source)
    {
        switch ($source) {
            case 'olt':
                return OltUptime::query()
                    ->join('olt_locations', 'olt_locations.lgd_code', '=', 'olt_uptime.lgd_code')
                    ->join('districts', 'olt_locations.district_id', '=', 'districts.id')
                    ->join('blocks', 'olt_locations.block_id', '=', 'blocks.id');

            case 'gp_router':
                return GpRouterUptime::query()
                    ->join('gp_list', 'gp_list.lgd_code', '=', 'gp_router_uptime.lgd_code')
                    ->join('districts', 'gp_list.district_id', '=', 'districts.id')
                    ->join('blocks', 'gp_list.block_id', '=', 'blocks.id');

            case 'block_router':
                return BlockRouterUptime::query()
                    ->join('blocks', 'blocks.routercode', '=', 'block_router_uptime.lgd_code')
                    ->join('districts', 'blocks.district_id', '=', 'districts.id');

            case 'samriddh':
                return OntUptime::query()
                    ->join('gp_list', 'gp_list.lgd_code', '=', 'ont_uptime.lgd_code')
                    ->join('districts', 'gp_list.district_id', '=', 'districts.id')
                    ->join('blocks', 'gp_list.block_id', '=', 'blocks.id')
                    ->where('gp_list.samridh_stat', 1);

            case 'ont':
            default:
                return OntUptime::query()
                    ->join('gp_list', 'gp_list.lgd_code', '=', 'ont_uptime.lgd_code')
                    ->join('districts', 'gp_list.district_id', '=', 'districts.id')
                    ->join('blocks', 'gp_list.block_id', '=', 'blocks.id');
        }
    }

    private function applyLocationFilters($query, Request $request, array $config)
    {
        $query->where($config['state_column'], $request->get('state_id'));

        if ($request->has('district_id') && !empty($request->get('district_id'))) {
            $query->where($config['district_column'], $request->get('district_id'));
        }

        if ($request->has('block_id') && !empty($request->get('block_id'))) {
            $query->where($config['block_column'], $request->get('block_id'));
        }
    }

    private function applyDateFilters($query, Request $request, $dateColumn)
    {
        if ($request->has('month') && !empty($request->get('month'))) {
            $start = Carbon::createFromFormat('Y-m', $request->get('month'))->startOfMonth()->toDateString();
            $end = Carbon::createFromFormat('Y-m', $request->get('month'))->endOfMonth()->toDateString();
            $query->whereBetween($dateColumn, [$start, $end]);
            return;
        }

        if ($request->has('fromDate') && !empty($request->get('fromDate')) && $request->has('toDate') && !empty($request->get('toDate'))) {
            $query->whereBetween($dateColumn, [$request->get('fromDate'), $request->get('toDate')]);
            return;
        }

        $query->whereBetween($dateColumn, [
            Carbon::now()->subDays(6)->toDateString(),
            Carbon::now()->toDateString(),
        ]);
    }

    private function getPeriodSql($period, $dateColumn)
    {
        switch ($period) {
            case 'week':
                return [
                    'group' => 'YEARWEEK(' . $dateColumn . ', 1)',
                    'label' => "CONCAT(FLOOR(YEARWEEK($dateColumn, 1) / 100), '-W', LPAD(MOD(YEARWEEK($dateColumn, 1), 100), 2, '0'))",
                    'sort' => 'YEARWEEK(' . $dateColumn . ', 1)',
                ];

            case 'month':
                return [
                    'group' => "DATE_FORMAT($dateColumn, '%Y-%m')",
                    'label' => "DATE_FORMAT($dateColumn, '%Y-%m')",
                    'sort' => "DATE_FORMAT($dateColumn, '%Y-%m')",
                ];

            case 'year':
                return [
                    'group' => 'YEAR(' . $dateColumn . ')',
                    'label' => 'YEAR(' . $dateColumn . ')',
                    'sort' => 'YEAR(' . $dateColumn . ')',
                ];

            case 'day':
            default:
                return [
                    'group' => 'DATE(' . $dateColumn . ')',
                    'label' => 'DATE(' . $dateColumn . ')',
                    'sort' => 'DATE(' . $dateColumn . ')',
                ];
        }
    }

    private function integrationSql($table)
    {
        return "
            SUM(
                CASE
                    WHEN NOT EXISTS (
                        SELECT 1
                        FROM $table as prev
                        WHERE prev.lgd_code = $table.lgd_code
                        AND DATE(prev.record_date) = DATE_SUB(DATE($table.record_date), INTERVAL 1 DAY)
                    )
                    THEN 1
                    ELSE 0
                END
            )
        ";
    }

    private function averages($data, $includeRouterMetrics)
    {
        $averages = [
            'total' => round($data->avg('total'), 2),
            'avg_uptime' => round($data->avg('avg_uptime'), 2),
            'gte98' => round($data->avg('gte98'), 2),
            'gte90' => round($data->avg('gte90'), 2),
            'gte75' => round($data->avg('gte75'), 2),
            'gte50' => round($data->avg('gte50'), 2),
            'gte20' => round($data->avg('gte20'), 2),
            'lt20' => round($data->avg('lt20'), 2),
            'gte90_total' => round($data->avg('gte90_total'), 2),
            'gte50_lt90' => round($data->avg('gte50_lt90'), 2),
            'lt50_total' => round($data->avg('lt50_total'), 2),
            'pct_gte98' => round($data->avg('pct_gte98'), 2),
            'pct_gte90' => round($data->avg('pct_gte90'), 2),
            'pct_gte50' => round($data->avg('pct_gte50'), 2),
            'pct_lt50' => round($data->avg('pct_lt50'), 2),
        ];

        if ($includeRouterMetrics) {
            $averages['lt98'] = round($data->avg('lt98'), 2);
            $averages['zero_availability'] = round($data->avg('zero_availability'), 2);
            $averages['integration'] = round($data->avg('integration'), 2);
        }

        return $averages;
    }

    private function pagination($records)
    {
        return [
            'current_page' => $records->currentPage(),
            'last_page' => $records->lastPage(),
            'per_page' => $records->perPage(),
            'total' => $records->total(),
            'from' => $records->firstItem(),
            'to' => $records->lastItem(),
        ];
    }

    private function successResponse($type, $source, Request $request, $data, $averages, $pagination)
    {
        return response()->json([
            'status' => true,
            'type' => $type,
            'source' => $source,
            'filters' => [
                'state_id' => $request->get('state_id'),
                'district_id' => $request->get('district_id'),
                'block_id' => $request->get('block_id'),
                'month' => $request->get('month'),
                'fromDate' => $request->get('fromDate'),
                'toDate' => $request->get('toDate'),
                'period' => $request->get('period', 'day'),
            ],
            'data' => $data,
            'averages' => $averages,
            'pagination' => $pagination,
        ]);
    }
}
