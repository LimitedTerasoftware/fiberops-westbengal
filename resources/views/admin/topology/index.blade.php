@extends('admin.layout.base')

@section('title', 'Network Topology ')

@section('styles')
{{-- Same base styles / background as the GP Trends (uptime) page --}}
<link rel="stylesheet" href="{{ asset('/css/olt.css') }}">
<style type="text/css">
    .terrasoft-page-container { position: relative; }
    .terrasoft-header-content { flex-wrap: wrap; gap: 12px; }
    .topo-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .topo-actions .terrasoft-btn { padding: 9px 16px; }
    .topo-actions .terrasoft-btn[disabled] { opacity: .5; cursor: not-allowed; }

    .topo-crumb { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.1); padding: 12px 20px; margin-bottom: 24px; font-size: 14px; color: #64748b; }
    .topo-crumb a { cursor: pointer; color: #3b82f6; }
    .topo-crumb .sep { color: #cbd5e1; margin: 0 8px; }
    .topo-crumb .cur { font-weight: 600; color: #1e293b; }

    /* Metric cards — same look as .terrasoft-metric-card on the uptime page */
    /* One row on normal screens: simple cards take 1 share, BR/GP split cards 2 shares */
    .topo-cards { display: flex; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; }
    .topo-cards > .alert { flex: 1 1 100%; }
    .topo-card { flex: 1 1 120px; min-width: 120px; background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
    .topo-card .lbl { font-size: 14px; font-weight: 500; color: #64748b; display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    .topo-card .lbl i { font-size: 18px; }
    .topo-card .val { font-size: 30px; font-weight: 700; color: #1e293b; line-height: 1.1; margin-bottom: 6px; }
    .topo-card .hint { font-size: 12px; color: #64748b; min-height: 16px; }
    .topo-card.c-total .lbl i   { color: #3b82f6; }
    .topo-card.c-up .lbl i,      .topo-card.c-up .val      { color: #28a745; }
    .topo-card.c-down .lbl i,    .topo-card.c-down .val    { color: #dc3545; }
    .topo-card.c-unreach .lbl i, .topo-card.c-unreach .val { color: #fd7e14; }
    .topo-card.c-pct .lbl i     { color: #17a2b8; }
    .topo-card.c-alarm .lbl i,   .topo-card.c-alarm .val   { color: #6f42c1; }
    /* Block Router / GP split cards */
    .topo-card.c-split { flex: 2 1 250px; min-width: 250px; }
    .topo-card.c-split .val small { font-size: 14px; font-weight: 700; margin-left: 6px; }
    .topo-card.c-split .topo-block-counts { margin-top: 6px; }
    .topo-card .val .topo-sdot { width: 14px; height: 14px; vertical-align: middle; margin-right: 6px; }
    .topo-cards-foot { flex: 1 1 100%; margin-top: -6px; font-size: 12px; color: #64748b; }
    @media (max-width: 576px) { .topo-card, .topo-card.c-split { flex-basis: 100%; min-width: 0; } }

    /* Compact BR / GP rows (district table, block cards) */
    .topo-mini { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #64748b; margin-top: 4px; white-space: nowrap; }
    .topo-mini .k { display: inline-block; min-width: 22px; font-size: 10px; font-weight: 700; color: #475569; background: #f1f5f9; border-radius: 4px; padding: 0 4px; text-align: center; }
    .topo-mini b { font-weight: 700; }
    .topo-mini .pct { margin-left: auto; font-weight: 700; }
    .topo-sdot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; }

    /* Panels — same look as .terrasoft-chart-card / tab container */
    .topo-panel { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.1); margin-bottom: 24px; overflow: hidden; }
    .topo-panel-head { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; padding: 14px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 16px; font-weight: 600; color: #1e293b; }
    .topo-panel-head input { border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; }
    .topo-panel-body { padding: 0; position: relative; }

    .topo-graph { width: 100%; height: 580px; position: relative; }
    .topo-legend { font-size: 12px; font-weight: normal; }
    .topo-legend span { display: inline-block; margin-left: 10px; }
    .topo-legend i { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 4px; vertical-align: middle; }

    .topo-tip { position: absolute; z-index: 20; pointer-events: none; display: none; background: rgba(33,37,41,.95); color: #fff; font-size: 12px; padding: 8px 10px; border-radius: 4px; min-width: 170px; }
    .topo-tip b { display: block; margin-bottom: 4px; font-size: 13px; }
    .topo-tip table td { padding: 1px 8px 1px 0; }

    .topo-table { font-size: 13px; margin: 0; color: #1e293b; }
    .topo-table th { white-space: nowrap; cursor: pointer; user-select: none; position: sticky; top: 0; background: #f8fafc; color: #475569; font-weight: 600; border-top: 0; }
    .topo-table tbody tr { cursor: pointer; }
    .topo-table tbody tr:hover { background: #f1f7ff; }
    .topo-table-wrap { max-height: 580px; overflow-y: auto; }
    .topo-bar { height: 6px; background: #e9ecef; border-radius: 3px; overflow: hidden; display: flex; min-width: 70px; }
    .topo-bar div { height: 100%; }

    .topo-msg { padding: 40px 15px; text-align: center; color: #6c757d; }
    .topo-loading { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,.7); z-index: 15; display: none; align-items: center; justify-content: center; }
    .topo-loading.on { display: flex; }

    .s-up { color: #28a745; } .s-down { color: #dc3545; } .s-unreach { color: #fd7e14; } .s-unknown { color: #6c757d; }

    /* Last sync / stale warning (VPN to HFCL drops) */
    .topo-sync.stale { color: #b45309; font-weight: 600; }
    .topo-stale { display: none; background: #fffbeb; border: 1px solid #fcd34d; color: #92400e; border-radius: 12px; padding: 10px 16px; margin-bottom: 24px; font-size: 13px; }
    .topo-stale.on { display: block; }

    /* Level 2: blocks */
    .topo-graph-2 { height: 460px; }
    .topo-blocks { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 16px; padding: 20px; }
    .topo-block { background: #fff; border: 1px solid #e2e8f0; border-left: 5px solid #adb5bd; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.08); padding: 14px 16px; cursor: pointer; transition: box-shadow .15s ease, transform .15s ease; }
    .topo-block:hover, .topo-block.hl { box-shadow: 0 4px 12px rgba(59,130,246,.25); transform: translateY(-1px); }
    .topo-block.router-down { box-shadow: 0 0 0 2px #dc3545 inset, 0 1px 3px rgba(0,0,0,.08); }
    .topo-block-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 8px; }
    .topo-block-name { font-weight: 700; color: #1e293b; font-size: 14px; word-break: break-word; }
    .topo-block-router { font-size: 11px; color: #64748b; }
    .topo-block-router b.down { color: #dc3545; }
    .topo-block-pct { font-size: 22px; font-weight: 700; line-height: 1; margin-bottom: 8px; }
    .topo-block-pct small { font-size: 12px; font-weight: 500; color: #64748b; }
    .topo-block-counts { display: grid; grid-template-columns: repeat(4, 1fr); gap: 4px; text-align: center; font-size: 11px; color: #64748b; margin-top: 10px; }
    .topo-block-counts b { display: block; font-size: 15px; }
    .topo-alarm-badge { background: #6f42c1; color: #fff; border-radius: 10px; padding: 2px 8px; font-size: 11px; font-weight: 600; white-space: nowrap; }
    .topo-alarm-badge.zero { background: #e2e8f0; color: #64748b; }

    /* Level 3: block graph */
    .topo-graph-3 { height: 640px; }
    .topo-tools { display: flex; flex-wrap: wrap; align-items: center; gap: 12px; font-size: 13px; font-weight: normal; color: #475569; }
    .topo-tools select, .topo-tools input[type=text] { border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; }
    .topo-check input { vertical-align: middle; margin-right: 4px; }
    .topo-search { display: flex; align-items: center; gap: 6px; }
    .topo-search input { width: 200px; }
    .topo-search span { font-size: 12px; color: #64748b; white-space: nowrap; }
    .topo-btn-sm { padding: 6px 12px !important; font-size: 13px !important; }
    .topo-tip-wide { max-width: 380px; }
    .topo-tip .al { border-top: 1px solid rgba(255,255,255,.2); margin-top: 6px; padding-top: 4px; }
    .topo-tip .al div { margin-top: 2px; }
    .sev-CRITICAL { color: #ff6b6b; font-weight: 700; } .sev-MAJOR { color: #ffb347; font-weight: 700; }

    .topo-side { position: absolute; top: 0; right: 0; bottom: 0; width: 380px; max-width: 100%; background: #fff; border-left: 1px solid #e2e8f0; box-shadow: -4px 0 12px rgba(0,0,0,.08); overflow-y: auto; z-index: 18; display: none; font-size: 13px; }
    .topo-side.on { display: block; }
    .topo-side-head { position: sticky; top: 0; background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 12px 16px; display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; }
    .topo-side-head h5 { margin: 0; font-size: 15px; font-weight: 700; color: #1e293b; word-break: break-word; }
    .topo-side-close { background: none; border: 0; font-size: 20px; line-height: 1; color: #64748b; cursor: pointer; }
    .topo-side-sec { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; }
    .topo-side-sec h6 { font-size: 12px; text-transform: uppercase; letter-spacing: .5px; color: #64748b; margin: 0 0 8px; }
    .topo-kv { width: 100%; }
    .topo-kv td { padding: 3px 0; vertical-align: top; }
    .topo-kv td:first-child { color: #64748b; width: 110px; }
    .topo-status { display: inline-block; padding: 1px 8px; border-radius: 10px; color: #fff; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .topo-alarm { border: 1px solid #e2e8f0; border-left: 4px solid #94a3b8; border-radius: 6px; padding: 6px 10px; margin-bottom: 6px; }
    .topo-alarm.CRITICAL { border-left-color: #7f1d1d; } .topo-alarm.MAJOR { border-left-color: #f59e0b; }
    .topo-alarm .t { font-weight: 600; color: #1e293b; }
    .topo-alarm .m { font-size: 11px; color: #64748b; }
    .topo-nb { cursor: pointer; } .topo-nb:hover td { background: #f1f7ff; }

    .topo-note { margin: 0; padding: 10px 20px; background: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 12px; color: #475569; }
    .topo-legend-3 { display: flex; flex-wrap: wrap; gap: 6px 24px; padding: 12px 20px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #475569; }
    .topo-legend-3 b { margin-right: 6px; color: #1e293b; }
    .topo-legend-3 span { display: inline-flex; align-items: center; margin-right: 12px; }
    .topo-legend-3 i { display: inline-block; margin-right: 5px; }
    .lg-br  { width: 20px; height: 12px; border-radius: 3px; background: #28a745; }
    .lg-gp  { width: 12px; height: 12px; border-radius: 50%; background: #28a745; }
    .lg-unk { width: 9px; height: 9px; background: #adb5bd; transform: rotate(45deg); }
    .lg-ext { width: 12px; height: 12px; border-radius: 50%; background: #28a745; opacity: .45; border: 2px dashed #475569; }
    .lg-dot { width: 12px; height: 12px; border-radius: 50%; }
    .lg-ring { background: #fff; border: 3px solid; }
    .lg-line { width: 26px; height: 0; }
</style>
@endsection

@section('content')
<div class="terrasoft-main-content">
<div class="terrasoft-page-container">

    {{-- Page Header (same style as GP Trends / uptime page) --}}
    <div class="terrasoft-page-header">
        <div class="terrasoft-header-content">
            <div class="terrasoft-header-info">
                <div class="terrasoft-header-icon">
                    <i class="ti-share"></i>
                </div>
                <div>
                    <h1 class="terrasoft-page-title">Network Topology</h1>
                    <p class="terrasoft-page-subtitle">HFCL NMS &middot; Block &amp; GP routers &middot; <span class="topo-sync" id="topo-sync">Last sync: <span id="topo-last-sync">&ndash;</span></span></p>
                </div>
            </div>
            <div class="terrasoft-header-actions topo-actions" id="topo-mode">
                <button type="button" class="terrasoft-btn terrasoft-btn-primary" data-mode="graph"><i class="ti-share"></i> Graph</button>
                <button type="button" class="terrasoft-btn terrasoft-btn-secondary" data-mode="map" disabled title="Coming soon"><i class="ti-map-alt"></i> Map</button>
                <button type="button" class="terrasoft-btn terrasoft-btn-secondary" id="topo-refresh"><i class="fa fa-refresh"></i> Refresh</button>
            </div>
        </div>
    </div>

    <div class="topo-crumb" id="topo-crumb"></div>

    <div class="topo-stale" id="topo-stale">
        <i class="ti-alert"></i> Data may be out of date: last HFCL sync was <b id="topo-stale-age"></b> ago
        (the VPN link to HFCL NMS may be down). Statuses shown are the last known values.
    </div>

    <div class="topo-cards" id="topo-cards"></div>

    {{-- Level 1: districts --}}
    <div class="row" id="topo-level-1" style="display:none">
        <div class="col-lg-7">
            <div class="topo-panel">
                <div class="topo-panel-head">
                    <span>Districts</span>
                    <span class="topo-legend">
                        <span><i style="background:#28a745"></i>Up</span>
                        <span><i style="background:#dc3545"></i>Down</span>
                        <span><i style="background:#fd7e14"></i>Unreach</span>
                        <span><i style="background:#adb5bd"></i>Unknown</span>
                        <span><i style="background:#fff;border:2px solid #6f42c1"></i>Alarms</span>
                    </span>
                </div>
                <div class="topo-panel-body">
                    <div class="topo-graph" id="topo-cy-1"></div>
                    <div class="topo-tip" id="topo-tip-1"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="topo-panel">
                <div class="topo-panel-head">
                    <span>District summary</span>
                    <input type="text" class="form-control form-control-sm" id="topo-filter-1" placeholder="Filter..." style="width:160px">
                </div>
                <div class="topo-panel-body topo-table-wrap">
                    <table class="table table-sm table-hover topo-table" id="topo-table-1">
                        <thead>
                            <tr>
                                <th data-sort="name">District</th>
                                <th data-sort="blocks" class="text-right">Blocks</th>
                                <th data-sort="devices" class="text-right">Devices</th>
                                <th data-sort="up" class="text-right s-up">Up</th>
                                <th data-sort="down" class="text-right s-down">Down</th>
                                <th data-sort="unreach" class="text-right s-unreach">Unr.</th>
                                <th data-sort="up_pct" class="text-right">Up %</th>
                                <th data-sort="alarms" class="text-right">Alarms</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Level 2: blocks in one district --}}
    <div id="topo-level-2" style="display:none">
        <div class="topo-panel">
            <div class="topo-panel-head">
                <span id="topo-level-2-title">Blocks</span>
                <span class="topo-legend">
                    <span><i style="background:#28a745"></i>100%</span>
                    <span><i style="background:#fd7e14"></i>50&ndash;99%</span>
                    <span><i style="background:#dc3545"></i>&lt;50%</span>
                    <span><i style="background:#adb5bd"></i>No devices</span>
                    <span><i style="background:#fff;border:3px solid #dc3545"></i>Block router down</span>
                </span>
            </div>
            <div class="topo-panel-body">
                <div class="topo-graph topo-graph-2" id="topo-cy-2"></div>
                <div class="topo-tip" id="topo-tip-2"></div>
            </div>
        </div>
        <div class="topo-panel">
            <div class="topo-panel-head">
                <span>Blocks <small class="text-muted" style="font-weight:normal">(worst first)</small></span>
                <input type="text" class="form-control form-control-sm" id="topo-filter-2" placeholder="Filter..." style="width:160px">
            </div>
            <div class="topo-panel-body">
                <div class="topo-blocks" id="topo-blocks-2"></div>
            </div>
        </div>
    </div>

    {{-- Level 3: one block network graph --}}
    <div id="topo-level-3" style="display:none">
        <div class="topo-panel">
            <div class="topo-panel-head">
                <span id="topo-level-3-title">Block</span>
                <div class="topo-tools">
                    <label class="mb-0">Layout
                        <select class="form-control form-control-sm d-inline-block" id="topo-layout-3" style="width:auto">
                            <option value="hier">Hierarchical</option>
                            <option value="cose">Force-directed</option>
                            <option value="concentric">Concentric</option>
                            <option value="geo">Geographic</option>
                        </select>
                    </label>
                    <label class="mb-0 topo-check"><input type="checkbox" id="topo-ports-3" checked> Show port labels</label>
                    <div class="topo-search">
                        <input type="text" class="form-control form-control-sm" id="topo-search-3" placeholder="Find device (name or ID)">
                        <span id="topo-search-info-3"></span>
                    </div>
                    <button type="button" class="terrasoft-btn terrasoft-btn-secondary topo-btn-sm" id="topo-fit-3" title="Zoom to fit"><i class="ti-fullscreen"></i> Fit</button>
                </div>
            </div>
            <div class="topo-panel-body">
                <div class="topo-graph topo-graph-3" id="topo-cy-3"></div>
                <div class="topo-tip topo-tip-wide" id="topo-tip-3"></div>
                <div class="topo-side" id="topo-side-3"></div>
            </div>
            <div class="topo-note" id="topo-nolink-3" style="display:none"></div>
            <div class="topo-legend-3">
                <div><b>Nodes</b>
                    <span><i class="lg-br"></i>Block router</span>
                    <span><i class="lg-gp"></i>GP router</span>
                    <span><i class="lg-unk"></i>Unknown device</span>
                    <span><i class="lg-ext"></i>Other block (faded, dashed)</span>
                </div>
                <div><b>Status</b>
                    <span><i class="lg-dot" style="background:#28a745"></i>Up</span>
                    <span><i class="lg-dot" style="background:#dc3545"></i>Down</span>
                    <span><i class="lg-dot" style="background:#fd7e14"></i>Unreach</span>
                    <span><i class="lg-dot" style="background:#adb5bd"></i>Unknown</span>
                </div>
                <div><b>Alarms</b>
                    <span><i class="lg-dot lg-ring" style="border-color:#7f1d1d"></i>Critical (thick border, &#9888; count)</span>
                    <span><i class="lg-dot lg-ring" style="border-color:#f59e0b"></i>Major</span>
                </div>
                <div><b>Links</b>
                    <span><i class="lg-line" style="border-top:2px solid #28a745"></i>Up</span>
                    <span><i class="lg-line" style="border-top:2px solid #fd7e14"></i>Partial</span>
                    <span><i class="lg-line" style="border-top:2px dashed #dc3545"></i>Down</span>
                    <span><i class="lg-line" style="border-top:2px dashed #94a3b8;opacity:.6"></i>Stale (last known)</span>
                    <span><i class="lg-line" style="border-top:5px solid #64748b"></i>Thick = interface alarm</span>
                </div>
            </div>
        </div>
    </div>

    <div class="topo-loading" id="topo-loading"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
</div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cytoscape/3.28.1/cytoscape.min.js"></script>
<script type="text/javascript">
(function ($) {
    'use strict';

    var Topo = {
        urls: {
            state:   "{{ url('admin/topology/data/state') }}",
            cluster: "{{ url('admin/topology/data/cluster') }}",
            block:   "{{ url('admin/topology/data/block') }}"
        },
        colors: { up: '#28a745', down: '#dc3545', unreach: '#fd7e14', unknown: '#adb5bd', alarm: '#6f42c1', root: '#0275d8' },
        route: { level: 1, cluster: null, block: null },
        data: { state: null },
        cy: {},
        sort1: { key: 'name', dir: 1 },
        refreshMs: 5 * 60 * 1000
    };
    window.Topo = Topo; // later steps (8b-8e) hook in here

    /* ---------------- helpers ---------------- */

    function esc(s) {
        return String(s === null || s === undefined ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
    function upper(s) { return String(s || '').toUpperCase(); }
    function num(n) { return (n || 0).toLocaleString('en-IN'); }
    function pctColor(p) {
        if (p >= 90) return Topo.colors.up;
        if (p >= 70) return '#ffc107';
        return Topo.colors.down;
    }
    Topo.esc = esc;
    Topo.upper = upper;

    function loading(on) { $('#topo-loading').toggleClass('on', !!on); }

    function getJSON(url, params) {
        loading(true);
        return $.ajax({ url: url, data: params || {}, dataType: 'json', cache: false })
            .always(function () { loading(false); })
            .fail(function (xhr) {
                var msg = xhr.status === 200
                    ? 'Session expired or no access. Please reload the page.'
                    : 'Failed to load data (' + xhr.status + ').';
                showError(msg);
            });
    }
    Topo.getJSON = getJSON;

    function showError(msg) {
        $('#topo-cards').html('<div class="alert alert-danger mb-0" style="grid-column:1/-1">' + esc(msg) + '</div>');
    }

    /* "YYYY-MM-DD HH:MM:SS" (app timezone, IST) -> Date in the browser's local time */
    function parseTs(ts) {
        var m = /^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2}):(\d{2})/.exec(ts || '');
        return m ? new Date(+m[1], m[2] - 1, +m[3], +m[4], +m[5], +m[6]) : null;
    }
    function ageText(mins) {
        if (mins < 60) return mins + ' min';
        if (mins < 48 * 60) return Math.floor(mins / 60) + ' h ' + (mins % 60) + ' min';
        return Math.floor(mins / 1440) + ' days';
    }

    Topo.staleMinutes = 30;
    function setLastSync(ts) {
        var d = parseTs(ts), mins = d ? Math.floor((Date.now() - d.getTime()) / 60000) : null;
        var stale = !ts || (mins !== null && mins > Topo.staleMinutes);
        $('#topo-last-sync').text(ts ? ts + (mins !== null && mins >= 0 ? ' (' + ageText(mins) + ' ago)' : '') : 'never');
        $('#topo-sync').toggleClass('stale', stale);
        $('#topo-stale-age').text(ts && mins !== null ? ageText(mins) : 'never');
        $('#topo-stale').toggleClass('on', stale);
    }
    Topo.setLastSync = setLastSync;

    /* ---------------- routing: (none) = L1, ?cluster=x = L2, ?cluster=x&block=y = L3 ---------------- */

    Topo.stateName = {!! json_encode($stateName, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!};

    function getParam(name) {
        var re = new RegExp('[?&]' + name + '=([^&#]*)'), m = re.exec(location.search);
        return m ? decodeURIComponent(m[1].replace(/\+/g, ' ')) : null;
    }

    function parseRoute() {
        var r = { level: 1, cluster: getParam('cluster'), block: getParam('block') };
        if (r.cluster) r.level = r.block ? 3 : 2;
        else r.block = null;
        return r;
    }

    function routeUrl(cluster, block) {
        var q = '';
        if (cluster) q += '?cluster=' + encodeURIComponent(cluster);
        if (cluster && block) q += '&block=' + encodeURIComponent(block);
        return location.pathname + q;
    }

    Topo.go = function (cluster, block) {
        var url = routeUrl(cluster, block);
        if (url !== location.pathname + location.search) {
            history.pushState({ cluster: cluster || null, block: block || null }, '', url);
        }
        render();
        window.scrollTo(0, 0);
    };

    function renderCrumb() {
        var r = Topo.route, sep = '<span class="sep">&rsaquo;</span>';
        var parts = [
            { label: 'Block Overview', go: '' },
            { label: Topo.stateName, go: '' }
        ];
        if (r.level >= 2) parts.push({ label: upper(r.cluster), go: r.cluster });
        if (r.level >= 3) parts.push({ label: upper(r.block), go: r.cluster, block: r.block });

        var html = [];
        for (var i = 0; i < parts.length; i++) {
            var p = parts[i], last = i === parts.length - 1;
            html.push('<a class="' + (last ? 'cur' : '') + '" data-go="' + esc(p.go) + '"'
                + (p.block ? ' data-block="' + esc(p.block) + '"' : '') + '>' + esc(p.label) + '</a>');
        }
        $('#topo-crumb').html(html.join(sep));
    }

    function render() {
        Topo.route = parseRoute();
        renderCrumb();
        $('#topo-level-1, #topo-level-2, #topo-level-3').hide();
        hideTip();

        if (Topo.route.level === 1) { loadLevel1(); }
        else if (Topo.route.level === 2) { Topo.renderLevel2(Topo.route.cluster); }
        else { Topo.renderLevel3(Topo.route.cluster, Topo.route.block); }
    }
    Topo.render = render;

    /* ---------------- summary cards (shared by all levels) ---------------- */

    /* ---- Block Router / GP split: {total, up, down, unreach, unknown, up_pct} ---- */

    function emptySplit() { return { total: 0, up: 0, down: 0, unreach: 0, unknown: 0, up_pct: 0 }; }
    function splitOf(r, key) { return (r && r[key]) ? r[key] : emptySplit(); }
    function addSplit(acc, s) {
        acc.total += s.total || 0; acc.up += s.up || 0; acc.down += s.down || 0;
        acc.unreach += s.unreach || 0; acc.unknown += s.unknown || 0;
        return acc;
    }
    function finishSplit(s) { s.up_pct = s.total ? Math.round(s.up * 1000 / s.total) / 10 : 0; return s; }
    /* Status of a single device from a split with total 1 */
    function splitStatus(s) {
        if (s.up) return 'up';
        if (s.down) return 'down';
        if (s.unreach) return 'unreach';
        return 'unknown';
    }
    function statusDot(st) {
        return '<span class="topo-sdot" style="background:' + (Topo.colors[st] || Topo.colors.unknown) + '"></span>';
    }
    Topo.splitOf = splitOf;

    /* ---- KPI cards (shared by all levels) ---- */

    function simpleCard(cls, label, icon, value, hint, valColor) {
        return '<div class="topo-card ' + cls + '">'
            + '<div class="lbl"><span>' + esc(label) + '</span><i class="' + icon + '"></i></div>'
            + '<div class="val"' + (valColor ? ' style="color:' + valColor + '"' : '') + '>' + value + '</div>'
            + '<div class="hint">' + esc(hint || '') + '</div></div>';
    }

    function splitCounts(s) {
        return '<div class="topo-block-counts">'
            + '<div><b class="s-up">' + num(s.up) + '</b>Up</div>'
            + '<div><b class="s-down">' + num(s.down) + '</b>Down</div>'
            + '<div><b class="s-unreach">' + num(s.unreach) + '</b>Unreach</div>'
            + '<div><b class="s-unknown">' + num(s.unknown) + '</b>Unknown</div></div>';
    }

    function splitCard(label, icon, s) {
        return '<div class="topo-card c-total c-split">'
            + '<div class="lbl"><span>' + esc(label) + '</span><i class="' + icon + '"></i></div>'
            + '<div class="val">' + num(s.total)
            + (s.total ? '<small style="color:' + pctColor(s.up_pct) + '">' + s.up_pct + '% up</small>' : '<small class="text-muted">none</small>')
            + '</div>' + splitCounts(s) + '</div>';
    }

    /* Single block router: status dot instead of numbers */
    function routerCard(s, name) {
        if (s.total !== 1) return splitCard('Block Routers', 'ti-server', s);
        var st = splitStatus(s);
        return '<div class="topo-card c-total">'
            + '<div class="lbl"><span>Block Router</span><i class="ti-server"></i></div>'
            + '<div class="val" style="color:' + (Topo.colors[st] || Topo.colors.unknown) + ';text-transform:uppercase">'
            + statusDot(st) + st + '</div>'
            + '<div class="hint">' + esc(name || '') + '</div></div>';
    }

    function devicesFoot(t) {
        var pct = t.devices ? Math.round(t.up * 1000 / t.devices) / 10 : 0;
        return '<div class="topo-cards-foot">All devices: <b>' + num(t.devices) + '</b> &middot; '
            + '<span class="s-up">' + num(t.up) + ' up</span> &middot; '
            + '<span class="s-down">' + num(t.down) + ' down</span> &middot; '
            + '<span class="s-unreach">' + num(t.unreach) + ' unreach</span> &middot; '
            + '<span class="s-unknown">' + num(t.unknown) + ' unknown</span> &middot; ' + pct + '% up'
            + (t.footExtra ? ' &middot; ' + esc(t.footExtra) : '') + '</div>';
    }

    Topo.renderKpis = function (cardsHtml, t) {
        $('#topo-cards').html(cardsHtml.join('') + (t ? devicesFoot(t) : ''));
    };
    Topo.simpleCard = simpleCard;
    Topo.splitCard = splitCard;

    function sumRows(rows) {
        var t = { devices: 0, up: 0, down: 0, unreach: 0, unknown: 0, alarms: 0, blocks: 0,
                  block_routers: emptySplit(), gps: emptySplit() };
        for (var i = 0; i < rows.length; i++) {
            t.devices += rows[i].devices; t.up += rows[i].up; t.down += rows[i].down;
            t.unreach += rows[i].unreach; t.unknown += rows[i].unknown; t.alarms += rows[i].alarms;
            t.blocks  += rows[i].blocks || 0;
            addSplit(t.block_routers, splitOf(rows[i], 'block_routers'));
            addSplit(t.gps, splitOf(rows[i], 'gps'));
        }
        finishSplit(t.block_routers);
        finishSplit(t.gps);
        return t;
    }
    Topo.sumRows = sumRows;

    /* Compact "BR" / "GP" row: up / down / unreach (+ unknown) and up %; single BR shows a status dot */
    function miniRow(key, s, dotIfSingle) {
        var html = '<div class="topo-mini"><span class="k">' + key + '</span>';
        if (!s.total) return html + '<span>none</span></div>';
        if (dotIfSingle && s.total === 1) {
            var st = splitStatus(s);
            return html + statusDot(st) + '<span style="color:' + (Topo.colors[st] || Topo.colors.unknown) + ';font-weight:700">' + st + '</span></div>';
        }
        return html
            + '<span title="Up"><b class="s-up">' + num(s.up) + '</b> up</span>'
            + '<span title="Down"><b class="s-down">' + num(s.down) + '</b> dn</span>'
            + '<span title="Unreach"><b class="s-unreach">' + num(s.unreach) + '</b> unr</span>'
            + (s.unknown ? '<span title="Unknown"><b class="s-unknown">' + num(s.unknown) + '</b> unk</span>' : '')
            + '<span class="pct" style="color:' + pctColor(s.up_pct) + '">' + s.up_pct + '%</span></div>';
    }
    Topo.miniRow = miniRow;

    /* ---------------- tooltip ---------------- */

    function showTip($tip, html, pos, $container) {
        pos = pos || { x: 10, y: 10 };
        var w = $container.width(), x = pos.x + 14, y = pos.y + 14;
        $tip.html(html).show();
        if (x + $tip.outerWidth() > w) x = pos.x - $tip.outerWidth() - 14;
        if (y + $tip.outerHeight() > $container.height()) y = pos.y - $tip.outerHeight() - 14;
        $tip.css({ left: Math.max(0, x), top: Math.max(0, y) });
    }
    function hideTip() { $('.topo-tip').hide(); }
    Topo.showTip = showTip;
    Topo.hideTip = hideTip;

    /* Tooltip text for a BR / GP split (dark background colours) */
    var TIP = { up: '#5cd87a', down: '#ff6b7a', unreach: '#ffa94d', unknown: '#ced4da' };
    function splitTip(s) {
        if (!s.total) return 'none';
        if (s.total === 1) {
            var st = splitStatus(s);
            return '<b style="color:' + TIP[st] + '">' + st + '</b>';
        }
        return num(s.total) + ': '
            + '<span style="color:' + TIP.up + '">' + num(s.up) + ' up</span> / '
            + '<span style="color:' + TIP.down + '">' + num(s.down) + ' down</span> / '
            + '<span style="color:' + TIP.unreach + '">' + num(s.unreach) + ' unr</span>'
            + (s.unknown ? ' / <span style="color:' + TIP.unknown + '">' + num(s.unknown) + ' unk</span>' : '')
            + ' &middot; ' + s.up_pct + '%';
    }

    function countsTip(title, d) {
        var br = splitOf(d, 'block_routers'), gp = splitOf(d, 'gps');
        return '<b>' + esc(title) + '</b><table>'
            + (d.blocks !== undefined ? '<tr><td>Blocks</td><td>' + num(d.blocks) + '</td></tr>' : '')
            + '<tr><td>' + (br.total === 1 ? 'Block router' : 'Block routers') + '</td><td>' + splitTip(br) + '</td></tr>'
            + '<tr><td>GPs</td><td>' + splitTip(gp) + '</td></tr>'
            + '<tr><td>All devices</td><td>' + num(d.devices) + ' &middot; ' + d.up_pct + '% up</td></tr>'
            + '<tr><td>Alarms</td><td>' + num(d.alarms) + '</td></tr></table>';
    }
    Topo.countsTip = countsTip;

    /* ---------------- Level 1: districts ---------------- */

    function loadLevel1() {
        $('#topo-level-1').show();
        if (Topo.data.state) { drawLevel1(Topo.data.state); }   // instant from cache, then refresh
        getJSON(Topo.urls.state).done(function (res) {
            Topo.data.state = res;
            if (Topo.route.level === 1) drawLevel1(res);
        });
    }

    function drawLevel1(res) {
        var rows = res.clusters || [];
        setLastSync(res.last_sync);

        var t = sumRows(rows);
        Topo.renderKpis([
            simpleCard('c-total', 'Districts', 'ti-map', num(rows.length), Topo.stateName),
            simpleCard('c-total', 'Blocks', 'ti-layout-grid2', num(t.blocks), ''),
            splitCard('Block Routers', 'ti-server', t.block_routers),
            splitCard('GPs', 'ti-signal', t.gps),
            simpleCard('c-alarm', 'Active alarms', 'ti-bell', num(t.alarms), '')
        ], t);

        drawLevel1Graph(rows, t);
        drawLevel1Table();
    }

    /* Pie styles: each district node shows its up/down/unreach/unknown split */
    function pieData(r) {
        var d = r.devices || 1;
        return {
            p1: r.up * 100 / d, p2: r.down * 100 / d,
            p3: r.unreach * 100 / d, p4: r.unknown * 100 / d
        };
    }

    function drawLevel1Graph(rows, total) {
        var el = document.getElementById('topo-cy-1');
        if (!rows.length) {
            if (Topo.cy[1]) { Topo.cy[1].destroy(); Topo.cy[1] = null; }
            $(el).html('<div class="topo-msg">No topology data yet. Waiting for the first HFCL sync.</div>');
            return;
        }

        var maxDev = 1, i;
        for (i = 0; i < rows.length; i++) if (rows[i].devices > maxDev) maxDev = rows[i].devices;

        var totalPct = total.devices ? Math.round(total.up * 1000 / total.devices) / 10 : 0;
        var els = [{
            group: 'nodes',
            data: $.extend({ id: '__wb', label: Topo.stateName + '\n' + totalPct + '%', root: 1, size: 90 },
                           pieData(total))
        }];

        for (i = 0; i < rows.length; i++) {
            var r = rows[i], id = 'd:' + r.name;
            els.push({
                group: 'nodes',
                data: $.extend({
                    id: id, name: r.name, row: r,
                    label: upper(r.name) + '\n' + r.up_pct + '%' + (r.alarms ? '  ⚠' + r.alarms : ''),
                    size: 34 + Math.round(36 * Math.sqrt(r.devices / maxDev)),
                    alarms: r.alarms ? 1 : 0
                }, pieData(r))
            });
            els.push({ group: 'edges', data: { id: 'e:' + r.name, source: '__wb', target: id } });
        }

        if (Topo.cy[1]) Topo.cy[1].destroy();
        $(el).empty();

        var cy = cytoscape({
            container: el,
            elements: els,
            minZoom: 0.3,
            maxZoom: 3,
            wheelSensitivity: 0.2,
            style: [
                { selector: 'node', style: {
                    'width': 'data(size)', 'height': 'data(size)',
                    'background-color': '#ffffff',
                    'border-width': 2, 'border-color': '#ced4da',
                    'pie-size': '100%',
                    'pie-1-background-color': Topo.colors.up,      'pie-1-background-size': 'data(p1)',
                    'pie-2-background-color': Topo.colors.down,    'pie-2-background-size': 'data(p2)',
                    'pie-3-background-color': Topo.colors.unreach, 'pie-3-background-size': 'data(p3)',
                    'pie-4-background-color': Topo.colors.unknown, 'pie-4-background-size': 'data(p4)',
                    'label': 'data(label)', 'text-wrap': 'wrap',
                    'text-valign': 'bottom', 'text-margin-y': 4,
                    'font-size': 10, 'font-weight': 600, 'color': '#343a40',
                    'text-outline-color': '#fff', 'text-outline-width': 2
                } },
                { selector: 'node[alarms = 1]', style: { 'border-color': Topo.colors.alarm, 'border-width': 4 } },
                { selector: 'node[root = 1]', style: {
                    'border-color': Topo.colors.root, 'border-width': 4,
                    'text-valign': 'center', 'text-margin-y': 0, 'font-size': 13
                } },
                { selector: 'edge', style: {
                    'width': 1, 'line-color': '#dee2e6', 'curve-style': 'straight'
                } },
                { selector: 'node.hover', style: { 'border-color': '#212529', 'border-width': 4 } },
                { selector: 'node.dim', style: { 'opacity': 0.25 } },
                { selector: 'edge.dim', style: { 'opacity': 0.15 } }
            ],
            layout: {
                name: 'concentric',
                concentric: function (n) { return n.data('root') ? 2 : 1; },
                levelWidth: function () { return 1; },
                minNodeSpacing: 18,
                padding: 30,
                animate: false
            }
        });
        Topo.cy[1] = cy;

        var $c = $(el), $tip = $('#topo-tip-1');
        cy.on('mouseover', 'node', function (e) {
            var n = e.target;
            n.addClass('hover');
            $c.css('cursor', n.data('root') ? 'default' : 'pointer');
            var html = n.data('root')
                ? countsTip(Topo.stateName, $.extend({ up_pct: totalPct }, total))
                : countsTip(upper(n.data('name')), n.data('row'));
            showTip($tip, html, e.renderedPosition, $c);
        });
        cy.on('mousemove', 'node', function (e) {
            showTip($tip, $tip.html(), e.renderedPosition, $c);
        });
        cy.on('mouseout', 'node', function (e) {
            e.target.removeClass('hover');
            $c.css('cursor', 'default');
            hideTip();
        });
        cy.on('tap', 'node', function (e) {
            if (!e.target.data('root')) Topo.go(e.target.data('name'));
        });
    }

    function highlightDistrict(name) {
        var cy = Topo.cy[1];
        if (!cy) return;
        cy.elements().removeClass('dim');
        if (!name) return;
        cy.elements().addClass('dim');
        var n = cy.getElementById('d:' + name);
        n.removeClass('dim');
        cy.getElementById('__wb').removeClass('dim');
        n.connectedEdges().removeClass('dim');
    }

    function drawLevel1Table() {
        var res = Topo.data.state;
        if (!res) return;
        var rows = (res.clusters || []).slice();
        var f = $.trim($('#topo-filter-1').val() || '').toLowerCase();
        var s = Topo.sort1;

        if (f) rows = $.grep(rows, function (r) { return r.name.indexOf(f) !== -1; });
        rows.sort(function (a, b) {
            var x = a[s.key], y = b[s.key];
            if (x < y) return -s.dir;
            if (x > y) return s.dir;
            return a.name < b.name ? -1 : 1;
        });

        var html = '';
        for (var i = 0; i < rows.length; i++) {
            var r = rows[i], d = r.devices || 1;
            html += '<tr data-name="' + esc(r.name) + '">'
                + '<td><b>' + esc(upper(r.name)) + '</b>'
                + '<div class="topo-bar">'
                +   '<div style="width:' + (r.up * 100 / d) + '%;background:' + Topo.colors.up + '"></div>'
                +   '<div style="width:' + (r.down * 100 / d) + '%;background:' + Topo.colors.down + '"></div>'
                +   '<div style="width:' + (r.unreach * 100 / d) + '%;background:' + Topo.colors.unreach + '"></div>'
                +   '<div style="width:' + (r.unknown * 100 / d) + '%;background:' + Topo.colors.unknown + '"></div>'
                + '</div>'
                + miniRow('BR', splitOf(r, 'block_routers'), false)
                + miniRow('GP', splitOf(r, 'gps'), false)
                + '</td>'
                + '<td class="text-right">' + num(r.blocks) + '</td>'
                + '<td class="text-right">' + num(r.devices) + '</td>'
                + '<td class="text-right s-up">' + num(r.up) + '</td>'
                + '<td class="text-right s-down">' + num(r.down) + '</td>'
                + '<td class="text-right s-unreach">' + num(r.unreach) + '</td>'
                + '<td class="text-right" style="color:' + pctColor(r.up_pct) + ';font-weight:600">' + r.up_pct + '%</td>'
                + '<td class="text-right">' + (r.alarms ? '<span class="badge" style="background:' + Topo.colors.alarm + ';color:#fff">' + num(r.alarms) + '</span>' : '0') + '</td>'
                + '</tr>';
        }
        if (!rows.length) html = '<tr><td colspan="8" class="text-center text-muted">No districts</td></tr>';
        $('#topo-table-1 tbody').html(html);

        $('#topo-table-1 th').each(function () {
            var k = $(this).data('sort'), $t = $(this);
            $t.find('.fa').remove();
            if (k === s.key) $t.append(' <i class="fa fa-caret-' + (s.dir > 0 ? 'up' : 'down') + '"></i>');
        });
    }

    $('#topo-table-1').on('click', 'th[data-sort]', function () {
        var k = $(this).data('sort');
        if (Topo.sort1.key === k) { Topo.sort1.dir = -Topo.sort1.dir; }
        else { Topo.sort1 = { key: k, dir: k === 'name' ? 1 : -1 }; }
        drawLevel1Table();
    });
    $('#topo-table-1 tbody')
        .on('click', 'tr[data-name]', function () { Topo.go($(this).attr('data-name')); })
        .on('mouseenter', 'tr[data-name]', function () { highlightDistrict($(this).attr('data-name')); })
        .on('mouseleave', 'tr[data-name]', function () { highlightDistrict(null); });
    $('#topo-filter-1').on('input', drawLevel1Table);

    /* ---------------- Level 2: blocks in one district ---------------- */

    Topo.data.cluster = {};   // cache by district name

    /* 100% green, 50-99% orange, <50% red, no devices grey */
    function blockColor(b) {
        if (!b.devices) return Topo.colors.unknown;
        if (b.up === b.devices) return Topo.colors.up;
        if (b.up_pct >= 50) return Topo.colors.unreach;
        return Topo.colors.down;
    }
    Topo.blockColor = blockColor;

    Topo.renderLevel2 = function (cluster) {
        $('#topo-level-2-title').text('Blocks in ' + upper(cluster));
        $('#topo-level-2').show();

        var cached = Topo.data.cluster[cluster];
        if (cached) drawLevel2(cached);
        else {
            $('#topo-cards').empty();
            $('#topo-blocks-2').empty();
            if (Topo.cy[2]) { Topo.cy[2].destroy(); Topo.cy[2] = null; }
            $('#topo-cy-2').empty();
        }

        getJSON(Topo.urls.cluster, { name: cluster }).done(function (res) {
            Topo.data.cluster[cluster] = res;
            if (Topo.route.level === 2 && Topo.route.cluster === cluster) drawLevel2(res);
        });
    };

    function drawLevel2(res) {
        var blocks = res.blocks || [];
        setLastSync(res.last_sync);

        var t = sumRows(blocks);
        var noRouter = 0;
        for (var i = 0; i < blocks.length; i++) if (!splitOf(blocks[i], 'block_routers').total) noRouter++;
        Topo.renderKpis([
            simpleCard('c-total', 'Blocks', 'ti-layout-grid2', num(blocks.length),
                       noRouter ? noRouter + ' without a block router in HFCL' : upper(Topo.route.cluster)),
            splitCard('Block Routers', 'ti-server', t.block_routers),
            splitCard('GPs', 'ti-signal', t.gps),
            simpleCard('c-alarm', 'Active alarms', 'ti-bell', num(t.alarms), '')
        ], t);

        if (!blocks.length) {
            if (Topo.cy[2]) { Topo.cy[2].destroy(); Topo.cy[2] = null; }
            var msg = '<div class="topo-msg"><i class="ti-info-alt"></i> No blocks found for <b>'
                + esc(upper(Topo.route.cluster)) + '</b>. Check the district name, or HFCL has not reported devices for it yet.'
                + '<br><a href="#" data-go-home>Back to ' + esc(Topo.stateName) + '</a></div>';
            $('#topo-cy-2').html(msg);
            $('#topo-blocks-2').html('<div class="topo-msg" style="grid-column:1/-1">No blocks to show.</div>');
            return;
        }

        drawLevel2Graph(blocks, t);
        drawLevel2Cards();
    }

    function drawLevel2Graph(blocks, total) {
        var el = document.getElementById('topo-cy-2');
        var cluster = Topo.route.cluster, i;
        var maxDev = 1;
        for (i = 0; i < blocks.length; i++) if (blocks[i].devices > maxDev) maxDev = blocks[i].devices;

        var totalPct = total.devices ? Math.round(total.up * 1000 / total.devices) / 10 : 0;
        var els = [{
            group: 'nodes',
            data: { id: '__district', root: 1, label: upper(cluster) + '\n' + totalPct + '%' }
        }];
        for (i = 0; i < blocks.length; i++) {
            var b = blocks[i], id = 'b:' + b.name;
            els.push({
                group: 'nodes',
                data: {
                    id: id, name: b.name, row: b,
                    label: upper(b.name) + '\n' + b.up_pct + '%' + (b.alarms ? '  \u26a0' + b.alarms : ''),
                    color: blockColor(b),
                    size: 30 + Math.round(26 * Math.sqrt(b.devices / maxDev)),
                    rdown: b.router_status === 'down' ? 1 : 0
                }
            });
            els.push({ group: 'edges', data: { id: 'e:' + b.name, source: '__district', target: id, color: blockColor(b) } });
        }

        if (Topo.cy[2]) Topo.cy[2].destroy();
        $(el).empty();

        var cy = cytoscape({
            container: el,
            elements: els,
            minZoom: 0.2,
            maxZoom: 3,
            wheelSensitivity: 0.2,
            style: [
                { selector: 'node', style: {
                    'width': 'data(size)', 'height': 'data(size)',
                    'background-color': 'data(color)',
                    'border-width': 2, 'border-color': '#ffffff',
                    'label': 'data(label)', 'text-wrap': 'wrap',
                    'text-valign': 'bottom', 'text-margin-y': 4,
                    'font-size': 10, 'font-weight': 600, 'color': '#343a40',
                    'text-outline-color': '#fff', 'text-outline-width': 2
                } },
                { selector: 'node[rdown = 1]', style: { 'border-color': Topo.colors.down, 'border-width': 6 } },
                { selector: 'node[root = 1]', style: {
                    'shape': 'round-rectangle', 'width': 150, 'height': 46,
                    'background-color': Topo.colors.root, 'border-width': 0,
                    'color': '#fff', 'text-outline-width': 0,
                    'text-valign': 'center', 'text-margin-y': 0, 'font-size': 13
                } },
                { selector: 'edge', style: {
                    'width': 1.5, 'line-color': 'data(color)', 'opacity': 0.5, 'curve-style': 'bezier'
                } },
                { selector: 'node.hover', style: { 'border-color': '#212529', 'border-width': 4 } },
                { selector: 'node[rdown = 1].hover', style: { 'border-color': Topo.colors.down, 'border-width': 8 } },
                { selector: 'node.dim', style: { 'opacity': 0.25 } },
                { selector: 'edge.dim', style: { 'opacity': 0.1 } }
            ],
            layout: {
                name: 'breadthfirst',
                directed: true,
                roots: '#__district',
                spacingFactor: 1.1,
                padding: 30,
                animate: false
            }
        });
        Topo.cy[2] = cy;

        var $c = $(el), $tip = $('#topo-tip-2');
        cy.on('mouseover', 'node', function (e) {
            var n = e.target;
            n.addClass('hover');
            $c.css('cursor', n.data('root') ? 'default' : 'pointer');
            var html;
            if (n.data('root')) {
                html = countsTip(upper(cluster), $.extend({ up_pct: totalPct }, total, { blocks: blocks.length }));
            } else {
                var r = n.data('row');
                html = countsTip(upper(r.name), r);
            }
            showTip($tip, html, e.renderedPosition, $c);
            if (!n.data('root')) $('#topo-blocks-2 .topo-block[data-name="' + cssEsc(n.data('name')) + '"]').addClass('hl');
        });
        cy.on('mousemove', 'node', function (e) {
            showTip($tip, $tip.html(), e.renderedPosition, $c);
        });
        cy.on('mouseout', 'node', function (e) {
            e.target.removeClass('hover');
            $c.css('cursor', 'default');
            $('#topo-blocks-2 .topo-block').removeClass('hl');
            hideTip();
        });
        cy.on('tap', 'node', function (e) {
            if (!e.target.data('root')) Topo.go(cluster, e.target.data('name'));
        });
    }

    /* Escape a value for use inside a jQuery attribute selector */
    function cssEsc(s) { return String(s).replace(/(["\\])/g, '\\$1'); }

    function highlightBlock(name) {
        var cy = Topo.cy[2];
        if (!cy) return;
        cy.elements().removeClass('dim');
        if (!name) return;
        cy.elements().addClass('dim');
        var n = cy.getElementById('b:' + name);
        n.removeClass('dim');
        cy.getElementById('__district').removeClass('dim');
        n.connectedEdges().removeClass('dim');
    }

    function drawLevel2Cards() {
        var res = Topo.data.cluster[Topo.route.cluster];
        if (!res) return;
        var rows = (res.blocks || []).slice();
        var f = $.trim($('#topo-filter-2').val() || '').toLowerCase();
        if (f) rows = $.grep(rows, function (r) { return String(r.name).indexOf(f) !== -1; });

        // Worst first: lowest up %, blocks with no devices last; ties -> more down, then name
        rows.sort(function (a, b) {
            if (!a.devices !== !b.devices) return a.devices ? -1 : 1;
            if (a.up_pct !== b.up_pct) return a.up_pct - b.up_pct;
            if (a.down !== b.down) return b.down - a.down;
            return a.name < b.name ? -1 : 1;
        });

        var html = '';
        for (var i = 0; i < rows.length; i++) {
            var r = rows[i], c = blockColor(r), rdown = r.router_status === 'down';
            html += '<div class="topo-block' + (rdown ? ' router-down' : '') + '" data-name="' + esc(r.name) + '" style="border-left-color:' + c + '">'
                + '<div class="topo-block-head">'
                +   '<div><div class="topo-block-name">' + esc(upper(r.name)) + '</div>'
                +   '</div>'
                +   '<span class="topo-alarm-badge' + (r.alarms ? '' : ' zero') + '" title="Active alarms"><i class="ti-bell"></i> ' + num(r.alarms) + '</span>'
                + '</div>'
                + '<div class="topo-block-pct" style="color:' + c + '">' + (r.devices ? r.up_pct + '%' : '&ndash;')
                +   ' <small>up of ' + num(r.devices) + ' devices</small></div>'
                + '<div class="topo-bar">'
                +   '<div style="width:' + (r.devices ? r.up * 100 / r.devices : 0) + '%;background:' + Topo.colors.up + '"></div>'
                +   '<div style="width:' + (r.devices ? r.down * 100 / r.devices : 0) + '%;background:' + Topo.colors.down + '"></div>'
                +   '<div style="width:' + (r.devices ? r.unreach * 100 / r.devices : 0) + '%;background:' + Topo.colors.unreach + '"></div>'
                +   '<div style="width:' + (r.devices ? r.unknown * 100 / r.devices : 0) + '%;background:' + Topo.colors.unknown + '"></div>'
                + '</div>'
                + '<div style="margin-top:8px">'
                +   miniRow('BR', splitOf(r, 'block_routers'), true)
                +   miniRow('GP', splitOf(r, 'gps'), false)
                + '</div></div>';
        }
        if (!rows.length) html = '<div class="topo-msg" style="grid-column:1/-1">No blocks match the filter.</div>';
        $('#topo-blocks-2').html(html);
    }

    $('#topo-blocks-2')
        .on('click', '.topo-block[data-name]', function () { Topo.go(Topo.route.cluster, $(this).attr('data-name')); })
        .on('mouseenter', '.topo-block[data-name]', function () { highlightBlock($(this).attr('data-name')); })
        .on('mouseleave', '.topo-block[data-name]', function () { highlightBlock(null); });
    $('#topo-filter-2').on('input', drawLevel2Cards);
    $('#topo-cy-2').on('click', 'a[data-go-home]', function (e) { e.preventDefault(); Topo.go(null); });

    /* ================= Level 3: block network graph ================= */

    Topo.data.block = {};   // cache by "cluster|block"
    /* Current block model. Map view (8d) and Network analysis (8e) read Topo.l3.model
       and listen for the 'topo:block' event fired after every load. */
    Topo.l3 = { key: null, sig: null, model: null, sideId: null, portsTouched: false, search: { q: '', i: 0 } };
    Topo.sevColors = { CRITICAL: '#7f1d1d', MAJOR: '#f59e0b', OTHER: '#64748b' };

    function blockKey(c, b) { return c + '|' + b; }
    function normSt(s) { return (s === 'up' || s === 'down' || s === 'unreach') ? s : 'unknown'; }
    function kindOf(level) {
        var l = String(level || '').toLowerCase();
        if (l.indexOf('block') !== -1) return 'br';
        if (l.indexOf('gp') !== -1) return 'gp';
        return 'unk';
    }
    function shortName(s, max) {
        s = String(s || ''); max = max || 18;
        return s.length > max ? s.slice(0, max - 1) + '…' : s;
    }
    function ringName(r) {
        if (r === null || r === undefined) return '';
        if (typeof r === 'object') return String(r.name || r.ring_name || r.ring || r.id || '');
        return String(r);
    }
    function ringsText(rings) {
        var out = [];
        for (var i = 0; rings && i < rings.length; i++) { var n = ringName(rings[i]); if (n) out.push(n); }
        return out.join(', ');
    }
    function agoText(ts) {
        var d = parseTs(ts);
        if (!d || d.getFullYear() <= 2000) return 'time unknown';
        var mins = Math.floor((Date.now() - d.getTime()) / 60000);
        return mins < 1 ? 'just now' : ageText(mins) + ' ago';
    }
    function sevCls(s) { return (s === 'CRITICAL' || s === 'MAJOR') ? s : 'x'; }
    function statusBadge(st) {
        st = normSt(st);
        return '<span class="topo-status" style="background:' + Topo.colors[st] + '">' + st + '</span>';
    }
    function kvRow(k, v, raw) {
        var val = (v === null || v === undefined || v === '') ? '&ndash;' : (raw ? v : esc(v));
        return '<tr><td>' + k + '</td><td>' + val + '</td></tr>';
    }
    Topo.agoText = agoText;
    Topo.ringsText = ringsText;

    /* ---------- data model ---------- */

    function buildModel(res) {
        var m = { res: res, nodes: {}, links: {}, adj: {}, internal: [], external: [], nolink: [], linkCount: 0 };
        var i, nodes = res.nodes || [], links = res.links || [];
        for (i = 0; i < nodes.length; i++) {
            var n = nodes[i];
            m.nodes[n.id] = n;
            m.adj[n.id] = [];
            (n.external ? m.external : m.internal).push(n);
        }
        for (i = 0; i < links.length; i++) {
            var l = links[i];
            if (!m.nodes[l.source] || !m.nodes[l.target]) continue;
            m.links[l.id] = l;
            m.linkCount++;
            m.adj[l.source].push({ link: l, other: l.target });
            m.adj[l.target].push({ link: l, other: l.source });
        }
        for (i = 0; i < m.internal.length; i++) {
            if (!m.adj[m.internal[i].id].length) m.nolink.push(m.internal[i]);
        }
        return m;
    }

    function signature(m) {
        return Object.keys(m.nodes).sort().join(',') + '#' + Object.keys(m.links).sort().join(',');
    }

    /* Totals from the nodes array, excluding external = true (neighbours in other blocks) */
    function level3Totals(m) {
        var t = { devices: 0, up: 0, down: 0, unreach: 0, unknown: 0, alarms: 0, linksDown: 0,
                  block_routers: emptySplit(), gps: emptySplit(), other: 0, brNames: [] }, stale = 0, i;
        for (i = 0; i < m.internal.length; i++) {
            var n = m.internal[i], st = normSt(n.effective_status), kind = kindOf(n.level);
            t.devices++;
            t[st]++;
            t.alarms += n.alarm_count || 0;
            if (kind === 'br') { t.block_routers.total++; t.block_routers[st]++; t.brNames.push(n.name || n.id); }
            else if (kind === 'gp') { t.gps.total++; t.gps[st]++; }
            else t.other++;
        }
        finishSplit(t.block_routers);
        finishSplit(t.gps);
        for (var k in m.links) {
            if (!m.links.hasOwnProperty(k)) continue;
            if (m.links[k].status === 'down') t.linksDown++;
            if (m.links[k].stale) stale++;
        }
        var extra = [];
        if (t.other) extra.push(t.other + ' other device(s)');
        if (m.external.length) extra.push(m.external.length + ' neighbour(s) in other blocks (not counted)');
        t.footExtra = extra.join(' · ');
        t.linksHint = m.linkCount + ' links' + (stale ? ' · ' + stale + ' stale' : '');
        return t;
    }

    function renderLevel3Kpis(m) {
        var t = level3Totals(m);
        Topo.renderKpis([
            routerCard(t.block_routers, t.brNames.join(', ')),
            splitCard('GPs', 'ti-signal', t.gps),
            simpleCard('c-alarm', 'Active alarms', 'ti-bell', num(t.alarms), ''),
            simpleCard('c-down', 'Links down', 'ti-unlink', num(t.linksDown), t.linksHint)
        ], t);
    }

    /* ---------- cytoscape elements & style ---------- */

    function nodeData(n) {
        var label = n.external
            ? shortName(n.name || n.id, 16) + (n.block ? ' (' + n.block + ')' : '')
            : shortName(n.name || n.id);
        if (n.alarm_count) label += '  ⚠' + n.alarm_count;
        var sev = '';
        if (n.alarm_count) sev = (n.max_severity === 'CRITICAL' || n.max_severity === 'MAJOR') ? n.max_severity : 'OTHER';
        return { id: n.id, label: label, kind: kindOf(n.level), st: normSt(n.effective_status), ext: n.external ? 1 : 0, sev: sev };
    }

    function edgeData(l) {
        return {
            id: 'l:' + l.id, lid: l.id, source: l.source, target: l.target,
            st: l.status || 'down', stale: l.stale ? 1 : 0, al: (l.alarms && l.alarms.length) ? 1 : 0,
            plabel: (l.a_port || '?') + ' ↔ ' + (l.b_port || '?')
        };
    }

    function level3Style() {
        var C = Topo.colors, S = Topo.sevColors;
        return [
            { selector: 'node', style: {
                'shape': 'ellipse', 'width': 26, 'height': 26,
                'background-color': C.unknown, 'border-width': 2, 'border-color': '#ffffff',
                'label': 'data(label)', 'font-size': 9, 'color': '#1e293b',
                'text-valign': 'bottom', 'text-margin-y': 3,
                'text-outline-color': '#fff', 'text-outline-width': 2, 'min-zoomed-font-size': 6
            } },
            { selector: 'node[st = "up"]',      style: { 'background-color': C.up } },
            { selector: 'node[st = "down"]',    style: { 'background-color': C.down } },
            { selector: 'node[st = "unreach"]', style: { 'background-color': C.unreach } },
            { selector: 'node[kind = "br"]', style: {
                'shape': 'round-rectangle', 'width': 66, 'height': 34, 'font-size': 11, 'font-weight': 700
            } },
            { selector: 'node[kind = "unk"]', style: { 'shape': 'diamond', 'width': 16, 'height': 16, 'background-color': C.unknown } },
            { selector: 'node[ext = 1]', style: {
                'opacity': 0.45, 'border-style': 'dashed', 'border-color': '#475569', 'border-width': 2
            } },
            { selector: 'node[sev = "OTHER"]',    style: { 'border-color': S.OTHER, 'border-width': 4 } },
            { selector: 'node[sev = "MAJOR"]',    style: { 'border-color': S.MAJOR, 'border-width': 5 } },
            { selector: 'node[sev = "CRITICAL"]', style: { 'border-color': S.CRITICAL, 'border-width': 6 } },
            { selector: 'node[grp = 1]', style: {
                'shape': 'round-rectangle', 'background-color': '#f8fafc', 'opacity': 1,
                'border-width': 1, 'border-style': 'dashed', 'border-color': '#94a3b8',
                'label': 'data(label)', 'text-valign': 'top', 'text-halign': 'center', 'text-margin-y': -4,
                'font-size': 11, 'font-weight': 600, 'color': '#475569', 'padding': 18
            } },
            { selector: 'edge', style: {
                'width': 2, 'line-color': C.up, 'curve-style': 'bezier',
                'font-size': 8, 'color': '#334155', 'text-rotation': 'autorotate',
                'text-background-color': '#ffffff', 'text-background-opacity': 0.85, 'text-background-padding': 1,
                'min-zoomed-font-size': 7
            } },
            { selector: 'edge[st = "partial"]', style: { 'line-color': C.unreach } },
            { selector: 'edge[st = "down"]',    style: { 'line-color': C.down, 'line-style': 'dashed' } },
            { selector: 'edge[stale = 1]',      style: { 'line-color': '#94a3b8', 'line-style': 'dashed', 'opacity': 0.5 } },
            { selector: 'edge[al = 1]',         style: { 'width': 5 } },
            { selector: 'edge.ports',           style: { 'label': 'data(plabel)' } },
            { selector: '.hover', style: { 'overlay-color': '#0f172a', 'overlay-opacity': 0.12, 'overlay-padding': 6 } },
            { selector: 'node.found', style: { 'overlay-color': '#3b82f6', 'overlay-opacity': 0.3, 'overlay-padding': 10 } },
            { selector: 'node.sel',   style: { 'overlay-color': '#1d4ed8', 'overlay-opacity': 0.4, 'overlay-padding': 12 } }
        ];
    }

    /* ---------- render / load ---------- */

    Topo.renderLevel3 = function (cluster, block) {
        var key = blockKey(cluster, block);
        $('#topo-level-3-title').text(upper(block) + ' · ' + upper(cluster));
        $('#topo-level-3').show();
        if (Topo.cy[3]) Topo.cy[3].resize();

        var cached = Topo.data.block[key];
        if (cached) drawLevel3(cached);
        else if (Topo.l3.key !== key) resetLevel3();
        loadLevel3(cluster, block);
    };

    function loadLevel3(cluster, block) {
        getJSON(Topo.urls.block, { cluster: cluster, block: block }).done(function (res) {
            Topo.data.block[blockKey(cluster, block)] = res;
            var r = Topo.route;
            if (r.level === 3 && r.cluster === cluster && r.block === block) drawLevel3(res);
        });
    }

    function resetLevel3() {
        if (Topo.cy[3]) { Topo.cy[3].destroy(); Topo.cy[3] = null; }
        Topo.l3.key = null; Topo.l3.sig = null; Topo.l3.model = null;
        $('#topo-cards').empty();
        $('#topo-cy-3').empty();
        $('#topo-nolink-3').hide();
        closeSide();
    }

    function drawLevel3(res) {
        var m = buildModel(res), key = blockKey(Topo.route.cluster, Topo.route.block), sig = signature(m);
        setLastSync(res.last_sync);
        renderLevel3Kpis(m);
        Topo.l3.model = m;

        if (Topo.cy[3] && Topo.l3.key === key && Topo.l3.sig === sig) {
            updateLevel3(m);            // same topology: keep positions / zoom, refresh statuses
        } else {
            Topo.l3.sig = sig;
            buildLevel3(m, key);
        }
        $(document).trigger('topo:block', [m]);
    }

    function buildLevel3(m, key) {
        var el = document.getElementById('topo-cy-3');
        if (Topo.cy[3]) { Topo.cy[3].destroy(); Topo.cy[3] = null; }
        closeSide();
        Topo.l3.search = { q: '', i: 0 };
        $('#topo-search-info-3').text('');
        $(el).empty();
        Topo.l3.key = key;

        if (!m.internal.length) {
            $(el).html('<div class="topo-msg"><i class="ti-info-alt"></i> No devices found for block <b>'
                + esc(upper(Topo.route.block)) + '</b> in <b>' + esc(upper(Topo.route.cluster)) + '</b>.</div>');
            $('#topo-nolink-3').hide();
            return;
        }

        // "No link data" groups by first ring
        var groups = {}, groupOf = {}, i, g;
        for (i = 0; i < m.nolink.length; i++) {
            g = ringName((m.nolink[i].rings || [])[0]) || '(no ring)';
            (groups[g] = groups[g] || []).push(m.nolink[i]);
            groupOf[m.nolink[i].id] = g;
        }
        var gnames = Object.keys(groups).sort(), els = [];
        for (i = 0; i < gnames.length; i++) {
            g = gnames[i];
            els.push({ group: 'nodes', data: { id: 'grp:' + g, grp: 1, gname: g,
                label: 'No link data · ' + g + ' (' + groups[g].length + ')' } });
        }
        var nodes = m.res.nodes || [];
        for (i = 0; i < nodes.length; i++) {
            var d = nodeData(nodes[i]);
            if (groupOf.hasOwnProperty(d.id)) d.parent = 'grp:' + groupOf[d.id];
            els.push({ group: 'nodes', data: d });
        }
        for (var k in m.links) if (m.links.hasOwnProperty(k)) els.push({ group: 'edges', data: edgeData(m.links[k]) });

        var cy = cytoscape({
            container: el, elements: els, style: level3Style(),
            layout: { name: 'preset' }, minZoom: 0.1, maxZoom: 4, wheelSensitivity: 0.2
        });
        Topo.cy[3] = cy;
        Topo.l3.groups = gnames;

        if (!Topo.l3.portsTouched) $('#topo-ports-3').prop('checked', m.linkCount <= 80);
        applyPorts();
        bindLevel3Events(cy);
        runLayout();
        renderNoLinkNote(m, gnames.length);
    }

    function updateLevel3(m) {
        var cy = Topo.cy[3];
        cy.batch(function () {
            var nodes = m.res.nodes || [], i;
            for (i = 0; i < nodes.length; i++) {
                var d = nodeData(nodes[i]);
                delete d.id;
                cy.getElementById(nodes[i].id).data(d);
            }
            for (var k in m.links) {
                if (!m.links.hasOwnProperty(k)) continue;
                var e = edgeData(m.links[k]), id = e.id;
                delete e.id; delete e.source; delete e.target;
                cy.getElementById(id).data(e);
            }
        });
        if (Topo.l3.sideId) openSide(Topo.l3.sideId);
        renderNoLinkNote(m, (Topo.l3.groups || []).length);
    }

    function renderNoLinkNote(m, groupCount) {
        if (!m.nolink.length) { $('#topo-nolink-3').hide(); return; }
        $('#topo-nolink-3').html('<i class="ti-info-alt"></i> <b>' + m.nolink.length + ' device(s) have no link data</b>'
            + ' and are shown in the dashed "No link data" box(es) below the graph, grouped by their first ring ('
            + groupCount + ' group' + (groupCount === 1 ? '' : 's') + '). HFCL only reports LLDP neighbours for devices it can reach,'
            + ' so unreachable GP routers usually have no links; their actual position in the ring is not known from HFCL data.').show();
    }

    /* ---------- layouts ---------- */

    function mainElements(cy) {
        var nodes = cy.nodes().filter(function (n) { return !n.data('grp') && !n.isChild(); });
        return nodes.union(cy.edges());
    }

    /* One root per connected component: the block router if present, else the highest-degree node */
    function layoutRoots(main) {
        var roots = cy3Collection(), comps = main.components();
        for (var i = 0; i < comps.length; i++) {
            var c = comps[i].nodes(), r = c.filter(function (n) { return n.data('kind') === 'br' && !n.data('ext'); });
            if (!r.length) r = c.filter(function (n) { return n.data('kind') === 'br'; });
            if (!r.length) {
                var best = null;
                c.forEach(function (n) { if (!best || n.degree() > best.degree()) best = n; });
                r = best;
            }
            roots = roots.union(r);
        }
        return roots;
    }
    function cy3Collection() { return Topo.cy[3].collection(); }

    function bfsDepth(main, roots) {
        var depth = {}, q = [], max = 0;
        roots.forEach(function (r) { depth[r.id()] = 0; q.push(r); });
        while (q.length) {
            var n = q.shift();
            n.neighborhood().nodes().forEach(function (x) {
                if (depth[x.id()] === undefined) {
                    depth[x.id()] = depth[n.id()] + 1;
                    if (depth[x.id()] > max) max = depth[x.id()];
                    q.push(x);
                }
            });
        }
        main.nodes().forEach(function (n) { if (depth[n.id()] === undefined) depth[n.id()] = max + 1; });
        return { depth: depth, max: max + 1 };
    }

    function validCoord(lat, lng) {
        return lat !== null && lng !== null && isFinite(lat) && isFinite(lng)
            && lat >= 5 && lat <= 38 && lng >= 67 && lng <= 99;   // India incl. A&N islands
    }

    /* lat/lng -> canvas positions; nodes without coordinates in a row at the bottom */
    function geoPositions(nodes) {
        var m = Topo.l3.model, pts = [], none = [], pos = {}, used = {};
        nodes.forEach(function (n) {
            var d = m.nodes[n.id()];
            if (d && validCoord(d.lat, d.lng)) pts.push({ id: n.id(), lat: d.lat, lng: d.lng });
            else none.push(n.id());
        });
        var $c = $('#topo-cy-3'), W = Math.max($c.width(), 400) - 80, H = Math.max($c.height(), 300) - 160;
        var bottom = 0;
        if (pts.length) {
            var minLat = 90, maxLat = -90, minLng = 180, maxLng = -180, i;
            for (i = 0; i < pts.length; i++) {
                minLat = Math.min(minLat, pts[i].lat); maxLat = Math.max(maxLat, pts[i].lat);
                minLng = Math.min(minLng, pts[i].lng); maxLng = Math.max(maxLng, pts[i].lng);
            }
            var cosLat = Math.cos(((minLat + maxLat) / 2) * Math.PI / 180);
            var spanX = (maxLng - minLng) * cosLat, spanY = maxLat - minLat;
            var scale = Math.min(spanX > 0 ? W / spanX : Infinity, spanY > 0 ? H / spanY : Infinity);
            if (!isFinite(scale)) scale = 1;
            for (i = 0; i < pts.length; i++) {
                var x = 40 + (pts[i].lng - minLng) * cosLat * scale, y = 40 + (maxLat - pts[i].lat) * scale;
                var k = Math.round(x) + ',' + Math.round(y), j = used[k] || 0;   // spread out identical coordinates
                used[k] = j + 1;
                if (j) { x += 16 * Math.cos(j * 2.4); y += 16 * Math.sin(j * 2.4); }
                pos[pts[i].id] = { x: x, y: y };
            }
            bottom = 40 + spanY * scale;
        }
        for (var n = 0; n < none.length; n++) pos[none[n]] = { x: 40 + n * 80, y: bottom + 110 };
        return function (node) { return pos[node.id()] || { x: 0, y: 0 }; };
    }

    function runLayout() {
        var cy = Topo.cy[3];
        if (!cy) return;
        var mode = $('#topo-layout-3').val(), main = mainElements(cy), opts;

        if (main.nodes().length) {
            var roots = layoutRoots(main);
            if (mode === 'cose') {
                opts = { name: 'cose', animate: false, randomize: true, fit: false,
                         nodeRepulsion: function () { return 9000; }, idealEdgeLength: function () { return 70; },
                         nodeOverlap: 12, componentSpacing: 80 };
            } else if (mode === 'concentric') {
                var bd = bfsDepth(main, roots);
                opts = { name: 'concentric', animate: false, fit: false, minNodeSpacing: 22,
                         concentric: function (n) { return bd.max + 1 - bd.depth[n.id()]; },
                         levelWidth: function () { return 1; } };
            } else if (mode === 'geo') {
                opts = { name: 'preset', animate: false, fit: false, positions: geoPositions(main.nodes()) };
            } else {
                opts = { name: 'breadthfirst', roots: roots, directed: false, spacingFactor: 1.15,
                         animate: false, fit: false };
            }
            main.layout(opts).run();
        }
        placeNoLinkGroups(cy, main);
        cy.fit(cy.elements(), 30);
    }
    Topo.l3.runLayout = runLayout;

    /* "No link data" boxes in rows below the main graph */
    function placeNoLinkGroups(cy, main) {
        var names = Topo.l3.groups || [];
        if (!names.length) return;
        var bb = main.nodes().length ? main.boundingBox() : { x1: 0, y2: -110, w: 0 };
        var maxW = Math.max(bb.w, 700), x0 = bb.x1, x = x0, y = bb.y2 + 120, rowH = 0, step = 75;

        for (var g = 0; g < names.length; g++) {
            var kids = cy.getElementById('grp:' + names[g]).children().sort(function (a, b) {
                return String(a.data('label')) < String(b.data('label')) ? -1 : 1;
            });
            var n = kids.length, cols = Math.min(n, Math.max(3, Math.ceil(Math.sqrt(n * 2))));
            var gw = (cols - 1) * step, gh = (Math.ceil(n / cols) - 1) * step * 0.8;
            if (x > x0 && x + gw > x0 + maxW) { x = x0; y += rowH + 110; rowH = 0; }
            kids.forEach(function (k, i) {
                k.position({ x: x + (i % cols) * step, y: y + Math.floor(i / cols) * step * 0.8 });
            });
            x += gw + 120;
            rowH = Math.max(rowH, gh);
        }
    }

    function applyPorts() {
        if (Topo.cy[3]) Topo.cy[3].edges().toggleClass('ports', $('#topo-ports-3').is(':checked'));
    }

    /* ---------- tooltips ---------- */

    function alarmLines(alarms, limit) {
        var html = '', n = Math.min(alarms.length, limit);
        for (var i = 0; i < n; i++) {
            var a = alarms[i];
            html += '<div><span class="sev-' + sevCls(a.severity) + '">' + esc(a.alarm_type || a.severity) + '</span>'
                + (a.resource ? ' &middot; ' + esc(a.resource) : '') + ' &middot; ' + esc(agoText(a.raised_at)) + '</div>';
        }
        if (alarms.length > limit) html += '<div>+ ' + (alarms.length - limit) + ' more (click for all)</div>';
        return html;
    }

    function nodeTip(n) {
        var html = '<b>' + esc(n.name || n.id) + '</b><table>'
            + kvRow('Host Name', n.name) + kvRow('Device ID', n.id) + kvRow('IP', n.ip)
            + kvRow('Type', n.level) + kvRow('Model', n.model) + kvRow('Site', n.site)
            + kvRow('Ring(s)', ringsText(n.rings))
            + kvRow('Status', esc(normSt(n.effective_status)) + (n.status ? ' (' + esc(n.status) + ')' : ''), true)
            + kvRow('Last synced', n.last_synced)
            + (n.external ? kvRow('Block', upper(n.block) + ' (other block)') : '')
            + '</table>';
        var al = n.alarms || [];
        if (al.length) html += '<div class="al"><b style="font-size:12px">Active alarms (' + al.length + ')</b>' + alarmLines(al, 3) + '</div>';
        return html;
    }

    function edgeTip(l) {
        var m = Topo.l3.model, a = m.nodes[l.source], b = m.nodes[l.target];
        var html = '<b>Link</b><table>'
            + kvRow('A side', (a ? a.name || a.id : l.source) + ' [' + (l.a_port || '?') + ']')
            + kvRow('B side', (b ? b.name || b.id : l.target) + ' [' + (l.b_port || '?') + ']')
            + kvRow('Status', esc(l.status) + (l.stale ? ' &middot; <i>stale (last known, not in latest sync)</i>' : ''), true)
            + kvRow('Last change', l.last_change ? l.last_change + ' (' + agoText(l.last_change) + ')' : null)
            + kvRow('Aerial distance (est.)', l.aerial_km !== null && l.aerial_km !== undefined
                ? l.aerial_km.toFixed(2) + ' km (straight line, not cable length)' : null)
            + '</table>';
        var al = l.alarms || [];
        if (al.length) html += '<div class="al"><b style="font-size:12px">Interface alarms (' + al.length + ')</b>' + alarmLines(al, 5) + '</div>';
        return html;
    }

    function bindLevel3Events(cy) {
        var $c = $('#topo-cy-3'), $tip = $('#topo-tip-3');
        cy.on('mouseover', 'node, edge', function (e) {
            var t = e.target, m = Topo.l3.model;
            if (t.isNode() && t.data('grp')) return;
            t.addClass('hover');
            $c.css('cursor', 'pointer');
            var html = t.isNode() ? nodeTip(m.nodes[t.id()]) : edgeTip(m.links[t.data('lid')]);
            var pos = e.renderedPosition || (t.isNode() ? t.renderedPosition() : t.renderedMidpoint());
            showTip($tip, html, pos, $c);
        });
        cy.on('mousemove', 'node, edge', function (e) {
            if ($tip.is(':visible')) showTip($tip, $tip.html(), e.renderedPosition, $c);
        });
        cy.on('mouseout', 'node, edge', function (e) {
            e.target.removeClass('hover');
            $c.css('cursor', 'default');
            hideTip();
        });
        cy.on('tap', 'node', function (e) {
            if (e.target.data('grp')) return;
            hideTip();
            selectNode(e.target.id(), false);
        });
        cy.on('tap', function (e) {
            if (e.target === cy) { closeSide(); cy.nodes().removeClass('found'); }
        });
    }

    /* ---------- side panel ---------- */

    function selectNode(id, center) {
        var cy = Topo.cy[3];
        if (!cy) return;
        var n = cy.getElementById(id);
        if (!n.length) return;
        cy.nodes().removeClass('sel');
        n.addClass('sel');
        if (center) cy.animate({ center: { eles: n }, zoom: Math.max(cy.zoom(), 1.3) }, { duration: 300 });
        openSide(id);
    }
    Topo.l3.selectNode = selectNode;

    function openSide(id) {
        var m = Topo.l3.model, n = m && m.nodes[id];
        if (!n) { closeSide(); return; }
        Topo.l3.sideId = id;

        var html = '<div class="topo-side-head"><div><h5>' + esc(n.name || n.id) + '</h5>'
            + statusBadge(n.effective_status) + (n.external ? ' <small class="text-muted">other block</small>' : '')
            + '</div><button type="button" class="topo-side-close" title="Close">&times;</button></div>';

        html += '<div class="topo-side-sec"><h6>Device</h6><table class="topo-kv">'
            + kvRow('Host Name', n.name) + kvRow('Device ID', n.id) + kvRow('IP', n.ip)
            + kvRow('Type', n.level) + kvRow('Model', n.model) + kvRow('Site', n.site)
            + kvRow('Ring(s)', ringsText(n.rings))
            + kvRow('Reachability', n.status) + kvRow('Effective status', normSt(n.effective_status))
            + kvRow('Block', n.block ? upper(n.block) : null) + kvRow('District', n.cluster ? upper(n.cluster) : null)
            + kvRow('Lat / Lng', (n.lat !== null && n.lat !== undefined) ? n.lat + ', ' + n.lng : null)
            + kvRow('Last synced', n.last_synced ? n.last_synced + ' (' + agoText(n.last_synced) + ')' : null)
            + '</table>';
        if (n.external && n.block && n.cluster) {
            html += '<button type="button" class="terrasoft-btn terrasoft-btn-secondary topo-btn-sm mt-2" data-open-cluster="'
                + esc(n.cluster) + '" data-open-block="' + esc(n.block) + '"><i class="ti-share"></i> Open block '
                + esc(upper(n.block)) + '</button>';
        }
        html += '</div>';

        // Neighbours
        var adj = m.adj[id] || [];
        html += '<div class="topo-side-sec"><h6>Links (' + adj.length + ')</h6>';
        if (!adj.length) {
            html += '<div class="text-muted">No link data from HFCL for this device.</div>';
        } else {
            html += '<table class="topo-kv">';
            for (var i = 0; i < adj.length; i++) {
                var l = adj[i].link, o = m.nodes[adj[i].other] || { id: adj[i].other };
                var mine = l.source === id ? l.a_port : l.b_port, theirs = l.source === id ? l.b_port : l.a_port;
                var lc = l.stale ? '#94a3b8' : (l.status === 'up' ? Topo.colors.up : (l.status === 'partial' ? Topo.colors.unreach : Topo.colors.down));
                html += '<tr class="topo-nb" data-node="' + esc(o.id) + '"><td style="width:auto">'
                    + '<span class="topo-status" style="background:' + lc + '">' + esc(l.stale ? 'stale' : l.status) + '</span></td>'
                    + '<td><b>' + esc(o.name || o.id) + '</b><br><small class="text-muted">'
                    + esc(mine || '?') + ' &harr; ' + esc(theirs || '?')
                    + (l.aerial_km !== null && l.aerial_km !== undefined ? ' &middot; ~' + l.aerial_km.toFixed(2) + ' km aerial' : '')
                    + ((l.alarms || []).length ? ' &middot; <span style="color:#dc3545">' + l.alarms.length + ' alarm(s)</span>' : '')
                    + '</small></td></tr>';
            }
            html += '</table>';
        }
        html += '</div>';

        // Alarms, newest first
        var al = (n.alarms || []).slice().sort(function (a, b) {
            return String(b.raised_at) < String(a.raised_at) ? -1 : (String(b.raised_at) > String(a.raised_at) ? 1 : 0);
        });
        html += '<div class="topo-side-sec"><h6>Active alarms (' + al.length + ')</h6>';
        if (!al.length) html += '<div class="text-muted">No active alarms.</div>';
        for (var j = 0; j < al.length; j++) {
            var a = al[j];
            html += '<div class="topo-alarm ' + sevCls(a.severity) + '">'
                + '<div class="t">' + esc(a.alarm_type) + ' <span class="sev-' + sevCls(a.severity) + '" style="font-size:11px">'
                + esc(a.severity) + '</span></div>'
                + (a.description ? '<div>' + esc(a.description) + '</div>' : '')
                + '<div class="m">' + (a.resource ? 'Resource: ' + esc(a.resource) + ' &middot; ' : '')
                + 'Raised ' + esc(a.raised_at) + ' (' + esc(agoText(a.raised_at)) + ')</div></div>';
        }
        html += '</div>';

        $('#topo-side-3').html(html).addClass('on');
    }

    function closeSide() {
        Topo.l3.sideId = null;
        $('#topo-side-3').removeClass('on').empty();
        if (Topo.cy[3]) Topo.cy[3].nodes().removeClass('sel');
    }

    $('#topo-side-3')
        .on('click', '.topo-side-close', closeSide)
        .on('click', 'tr.topo-nb[data-node]', function () { selectNode($(this).attr('data-node'), true); })
        .on('click', '[data-open-block]', function () {
            Topo.go($(this).attr('data-open-cluster'), $(this).attr('data-open-block'));
        });

    /* ---------- controls ---------- */

    function doSearch() {
        var cy = Topo.cy[3], m = Topo.l3.model, S = Topo.l3.search;
        var q = $.trim($('#topo-search-3').val() || '').toLowerCase(), $info = $('#topo-search-info-3');
        if (!cy || !m) return;
        cy.nodes().removeClass('found');
        if (!q) { S.q = ''; $info.text(''); return; }

        var hits = cy.nodes().filter(function (n) {
            if (n.data('grp')) return false;
            var d = m.nodes[n.id()] || {};
            return String(n.id()).toLowerCase().indexOf(q) !== -1 || String(d.name || '').toLowerCase().indexOf(q) !== -1;
        });
        if (!hits.length) { S.q = q; $info.text('No match'); return; }

        if (S.q !== q) { S.q = q; S.i = 0; } else { S.i = (S.i + 1) % hits.length; }
        hits.addClass('found');
        var n = hits.eq(S.i);
        cy.animate({ center: { eles: n }, zoom: Math.max(cy.zoom(), 1.4) }, { duration: 300 });
        $info.text((S.i + 1) + ' / ' + hits.length + (hits.length > 1 ? ' (Enter = next)' : ''));
    }

    $('#topo-search-3').on('keydown', function (e) {
        if (e.which === 13) { e.preventDefault(); doSearch(); }
        else if (e.which === 27) { $(this).val(''); doSearch(); }
    });
    $('#topo-layout-3').on('change', runLayout);
    $('#topo-ports-3').on('change', function () { Topo.l3.portsTouched = true; applyPorts(); });
    $('#topo-fit-3').on('click', function () {
        if (Topo.cy[3]) Topo.cy[3].animate({ fit: { eles: Topo.cy[3].elements(), padding: 30 } }, { duration: 300 });
    });

    /* ---------------- wiring ---------------- */

    $('#topo-crumb').on('click', 'a[data-go]', function (e) {
        e.preventDefault();
        Topo.go($(this).attr('data-go') || null, $(this).attr('data-block') || null);
    });
    $('#topo-refresh').on('click', function () {
        Topo.data.state = null;
        Topo.data.cluster = {};
        Topo.data.block = {};      // Level 3 keeps its layout if the topology is unchanged
        render();
    });
    $(window).on('popstate', render);   // browser Back / Forward

    // Fit graphs when the sidebar collapses / window resizes
    var resizeTimer = null;
    $(window).on('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            for (var k in Topo.cy) {
                var c = Topo.cy.hasOwnProperty(k) ? Topo.cy[k] : null;
                if (c && $(c.container()).is(':visible')) { c.resize(); c.fit(null, 30); }
            }
        }, 200);
    });

    // Auto refresh with the sync cadence (network_topology every 5 min)
    setInterval(function () {
        if (document.hidden) return;
        if (Topo.route.level === 1) loadLevel1();
        else if (Topo.route.level === 2) Topo.renderLevel2(Topo.route.cluster);
        else loadLevel3(Topo.route.cluster, Topo.route.block);
    }, Topo.refreshMs);

    // Keep the "x min ago" text and stale warning current between refreshes
    setInterval(function () {
        var r = Topo.route, res = r.level === 1 ? Topo.data.state
            : (r.level === 2 ? Topo.data.cluster[r.cluster] : Topo.data.block[blockKey(r.cluster, r.block)]);
        if (res) setLastSync(res.last_sync);
    }, 60 * 1000);

    $(render);
})(jQuery);
</script>
@endsection
