@extends('admin.layout.base')

@section('title', 'Workforce Dashboard - ')

@section('content')
 @php
    $roles = [
        1 => 'OFC',
        2 => 'FRT',
        5 => 'Patroller',
        3 => 'Zonal incharge',
        4 => 'District incharge'
    ];
@endphp
@php
    $queryParams = request()->all();
@endphp
<div class="attendance-dashboard">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="dashboard-header">
            <div class="header-left">
                <h1 class="dashboard-title">Workforce & Teams Overview</h1>
            </div>
        </div>

       <!-- Statistics Cards -->
        <div class="stats-grid">
            <a href="{{ route('admin.attendance_list', $queryParams) }}" class="stat-card">

                <div class="stat-content">
                    <div class="stat-number" id="total"></div>
                    <div class="stat-label">Total Staff Today</div>
                </div>
                <div class="stat-icon stat-icon-blue">
                    <i class="bi bi-people-fill"></i>
                </div>
              
            </a>
            <a href="{{ route('admin.attendance_list', array_merge(request()->all(), ['status' => 'present']))}}" class="stat-card">

                <div class="stat-content">
                    <div class="stat-number" id="logged_in"></div>
                    <div class="stat-label">Active Staff</div>
                </div>
                <div class="stat-icon stat-icon-green">
                    <i class="bi bi-person-check-fill"></i>
                </div>
            </a>
            <a href="{{ route('admin.workforce_details', array_merge(request()->all(), ['stage' => 'no_ticket']))}}" class="stat-card">
                <div class="stat-content">
                    <div class="stat-number" id="no_ticket"></div>
                    <div class="stat-label">Idle Staff</div>
                </div>
                <div class="stat-icon stat-icon-red">
                    <i class="bi bi-person-x-fill"></i>
                </div>
            </a>
            <a href="{{ route('admin.attendance_list', array_merge(request()->all(), ['status' => 'absent']))}}" class="stat-card">
                <div class="stat-content">
                    <div class="stat-number" id="not_logged_in"></div>
                    <div class="stat-label">Not Logged In</div>
                </div>
                <div class="stat-icon stat-icon-orange">
                    <i class="bi bi-person-fill-slash"></i>
                </div>
           </a>
           
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-number" id="offline"></div>
                    <div class="stat-label">Offline Staff</div>
                </div>
                <div class="stat-icon stat-icon-red">
                    <i class="bi bi-wifi-off"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-number"  id="haveTicket"></div>
                    <div class="stat-label" >Tickets Assigned Today</div>
                </div>
                <div class="stat-icon stat-icon-blue">
                    <i class="bi bi-ticket-fill"></i>
                </div>
            </div>
            <a href="{{ route('admin.workforce_details', array_merge(request()->all(), ['stage' => 'working']))}}" class="stat-card">
             
                <div class="stat-content">
                    <div class="stat-number" id="working"></div>
                    <div class="stat-label">Ongoing Today</div>
                </div>
                <div class="stat-icon stat-icon-purple">
                    <i class="bi bi-tools"></i>
                </div>
            
           </a>
           <a href="{{ route('admin.workforce_details', array_merge(request()->all(), ['stage' => 'only_hold']))}}" class="stat-card">
            
                <div class="stat-content">
                    <div class="stat-number" id="only_hold"></div>
                    <div class="stat-label">Onhold Today</div>
                </div>
                <div class="stat-icon stat-icon-red">
                    <i class="bi bi-pause-circle-fill"></i>
                </div>
           
           </a>
           <a href="{{ route('admin.workforce_details', array_merge(request()->all(), ['stage' => 'completed']))}}" class="stat-card">
            
                <div class="stat-content">
                    <div class="stat-number" id="completed"></div>
                    <div class="stat-label">Closed Today</div>
                </div>
                <div class="stat-icon stat-icon-green">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
          
           </a>

           <a href="{{ route('admin.workforce_details', array_merge(request()->all(), ['stage' => 'not_started']))}}" class="stat-card">
            
                <div class="stat-content">
                    <div class="stat-number" id="not_started"></div>
                    <div class="stat-label">Yet to Start</div>
                </div>
                <div class="stat-icon stat-icon-cyan">
                    <i class="bi bi-question-circle-fill"></i>
                </div>
          
          </a>
        </div>

 <div class="row">

    <div class="col-md-4 mb-2">
     <div class="canvas-card p-2">
      <canvas id="loginActivityChart"></canvas>
     </div>
  </div>
 

   <div class="col-md-4 mb-2">
     <div class="canvas-card p-2">

      <canvas id="workDistributionChart"></canvas>
     </div>
  </div>

     <div class="col-md-4 mb-2">
     <div class="canvas-card p-2">

      <canvas id="patworkDistributionChart"></canvas>
     </div>
  </div>

  <div class="col-md-6 mb-2">
     <div class="canvas-card mt-4">
        <h6 class="mb-3 fw-bold">Risk Heatmap - Zone Vs FRT</h6>
        <div id="frtHeatmap" class="heatmap"></div>
      </div>
  </div>
   
  <div class="col-md-6 mb-2">
      <div class="canvas-card mt-4">
         <h6 class="mb-3 fw-bold">Risk Heatmap - Zone Vs Patrollers</h6>
        <div id="patHeatmap" class="heatmap"></div>
     </div>
   </div>

<div class="col-md-6 mb-2">
    <div class="canvas-card mt-4">

        <div class="heatmap-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h6 class="fw-bold mb-0">Ongoing Heatmap - Zone Vs FRT</h6>
            <!-- FILTER MENU -->
            <div class="menu-container">
                <button class="menu-btn"><i class="bi bi-three-dots-vertical"></i></button>

                <div class="menu-dropdown">
                    <div class="menu-item" data-filter="all">All</div>
                    <div class="menu-item" data-filter="auto">Auto</div>
                    <div class="menu-item" data-filter="manual">Manual</div>
                </div>
            </div>
        </div>
        <div id="frtPickupHeatmap" class="heatmap"></div>

    </div>
</div>
   
  <div class="col-md-6 mb-2">
      <div class="canvas-card mt-4">

         <div class="heatmap-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h6 class="fw-bold mb-0">Ongoing Heatmap - Zone Vs Patrollers</h6>
            <!-- FILTER MENU -->
            <div class="menu-container">
                <button class="menu-btn"><i class="bi bi-three-dots-vertical"></i></button>

                <div class="menu-dropdown">
                    <div class="menu-item" data-filter="all">All</div>
                    <div class="menu-item" data-filter="auto">Auto</div>
                    <div class="menu-item" data-filter="manual">Manual</div>
                </div>
            </div>
        </div>
        <div id="patPickupHeatmap" class="heatmap"></div>
     </div>
   </div>


  <div class="col-md-6 mb-2">
     <div class="canvas-card p-2">
         <canvas id="statusComparisonChart"></canvas>
     </div>
  </div>

  



</div>
</div>



@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    $.ajax({
        url: "{{ url('/admin/get_todayfrtreport') }}",// Your Laravel route
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            let frtTotal = 0, frtWorking = 0, frtNotStarted = 0, frtCompleted = 0;
            let frtNoTicket = 0, frtOnlyHold = 0, frtLoggedIn = 0, frtNotLoggedIn = 0;
            let frtOnline = 0, frtOffline = 0;

            let patTotal = 0, patWorking = 0, patNotStarted = 0, patCompleted = 0;
            let patNoTicket = 0, patOnlyHold = 0, patLoggedIn = 0, patNotLoggedIn = 0;
            let patOnline = 0, patOffline = 0;

            // Loop through zones
            $.each(response.zones, function(zoneId, zoneData) {
                // Sum FRT
                frtTotal       += zoneData.frt.total;
                frtWorking     += zoneData.frt.working;
                frtNotStarted  += zoneData.frt.not_started;
                frtCompleted   += zoneData.frt.completed;
                frtNoTicket    += zoneData.frt.no_ticket;
                frtOnlyHold    += zoneData.frt.only_hold;
                frtLoggedIn    += zoneData.frt.logged_in;
                frtNotLoggedIn += zoneData.frt.not_logged_in;
                frtOnline      += zoneData.frt.online;
                frtOffline     += zoneData.frt.offline;

                // Sum Patrollers
                patTotal       += zoneData.patrollers.total;
                patWorking     += zoneData.patrollers.working;
                patNotStarted  += zoneData.patrollers.not_started;
                patCompleted   += zoneData.patrollers.completed;
                patNoTicket    += zoneData.patrollers.no_ticket;
                patOnlyHold    += zoneData.patrollers.only_hold;
                patLoggedIn    += zoneData.patrollers.logged_in;
                patNotLoggedIn += zoneData.patrollers.not_logged_in;
                patOnline      += zoneData.patrollers.online;
                patOffline     += zoneData.patrollers.offline;
            });

            let total = frtTotal+patTotal;
            let Working = frtWorking+patWorking;
            let NotStarted = frtNotStarted+patNotStarted;
            let Completed = frtCompleted+patCompleted;
            let NoTicket = frtNoTicket+patNoTicket;
            let OnlyHold = frtOnlyHold+patOnlyHold;
            let LoggedIn = frtLoggedIn+patLoggedIn;
            let NotLoggedIn = frtNotLoggedIn+patNotLoggedIn;
            let Online = frtOnline+patOnline;
            let Offline = frtOffline+patOffline;
            let frthaveTicket = frtTotal-frtNoTicket;
            let pathaveTicket = patTotal-patNoTicket;
            let haveTicket = frthaveTicket + pathaveTicket;

            // Set FRT totals
            $('#frt_total').text(frtTotal);
            $('#frt_working').text(frtWorking);
            $('#frt_not_started').text(frtNotStarted);
            $('#frt_completed').text(frtCompleted);
            $('#frt_no_ticket').text(frtNoTicket);
            $('#frt_only_hold').text(frtOnlyHold);
            $('#frt_logged_in').text(frtLoggedIn);
            $('#frt_not_logged_in').text(frtNotLoggedIn);
            $('#frt_online').text(frtOnline);
            $('#frt_offline').text(frtOffline);
            $('#frt_have_ticket').text(frthaveTicket);

            // Set Patroller totals
            $('#pat_total').text(patTotal);
            $('#pat_working').text(patWorking);
            $('#pat_not_started').text(patNotStarted);
            $('#pat_completed').text(patCompleted);
            $('#pat_no_ticket').text(patNoTicket);
            $('#pat_only_hold').text(patOnlyHold);
            $('#pat_logged_in').text(patLoggedIn);
            $('#pat_not_logged_in').text(patNotLoggedIn);
            $('#pat_online').text(patOnline);
            $('#pat_offline').text(patOffline);
            $('#pat_have_ticket').text(pathaveTicket);


            //total
            $('#total').text(total);
            $('#working').text(Working);
            $('#not_started').text(NotStarted);
            $('#completed').text(Completed);
            $('#no_ticket').text(NoTicket);
            $('#only_hold').text(OnlyHold);
            $('#logged_in').text(LoggedIn);
            $('#not_logged_in').text(NotLoggedIn);
            $('#online').text(Online);
            $('#offline').text(Offline);
            $('#haveTicket').text(haveTicket);

            

                // === Chart.js Data ===
            renderCharts({
                frt: {
                    total: frtTotal,
                    working: frtWorking,
                    not_started: frtNotStarted,
                    no_tickets:frtNoTicket,
                    hold:frtOnlyHold,
                    completed: frtCompleted,
                    logged_in: frtLoggedIn,
                    not_logged_in: frtNotLoggedIn,
                    offline: frtOffline
                },
                pat: {
                    total: patTotal,
                    working: patWorking,
                    not_started: patNotStarted,
                    no_tickets:patNoTicket,
                    hold:patOnlyHold,
                    completed: patCompleted,
                    logged_in: patLoggedIn,
                    not_logged_in: patNotLoggedIn,
                    offline: patOffline
                }
            });
             // ===== 6?? Render Heatmap using SAME API Response =====
            renderHeatmap(response);
            renderpatHeatmap(response);

        },
        error: function(xhr, status, error) {
            console.error('Error fetching report:', error);
        }
    });
});


// ===== Enhanced Heatmap Function =====
function renderpatHeatmap(data) {
  const heatmapContainer = $('#patHeatmap');
  heatmapContainer.empty();

  const stages = ['no_ticket', 'working', 'only_hold', 'completed', 'not_started'];
  const stageLabels = ['Not Assigned', 'Ongoing', 'Hold', 'Completed', 'Not Started'];

  // === Header Row ===
  let headerRow = `
    <div class="heatmap-row header">
      <div class="zone-name"></div>
     
      ${stageLabels.map(label => `<div class="cell-header">${label}</div>`).join('')}
    </div>`;
  heatmapContainer.append(headerRow);

  // === Zone Rows ===
  $.each(data.zones, function (zoneId, zoneData) {
    const pat = zoneData.patrollers;
    const zoneName = zoneData.zone_name;
    const zone_id = zoneData.zone_id;
    const total = pat.total || 1;

    let row = `<div class="heatmap-row">
      <div class="zone-name">${zoneName} (${pat.total})</div>`;
       

    stages.forEach((stage, i) => {
      const value = pat[stage] ?? 0;
      const percent = (value / total) * 100;

      let riskClass = 'neutral'; // default

      // === Color logic based on count + meaning ===
      switch (stage) {
        // ? High-risk stages (should be 0)
        case 'no_ticket':
        case 'not_started':
          riskClass = value === 0 ? 'good' : 'bad';
          break;

        // ?? Hold: some is okay, too much is bad
        case 'only_hold':
          if (value === 0) riskClass = 'good';
          else if (percent <= 10) riskClass = 'medium';
          else riskClass = 'bad';
          break;

        // ?? Working: should have some activity
        case 'working':
          riskClass = value > 0 ? 'good' : 'bad';
          break;

        // ?? Completed: more is better
        case 'completed':
          riskClass = value > 0 ? 'good' : 'neutral';
          break;

        default:
          riskClass = 'neutral';
      }

 const url = `/admin/workforce_details?zone_id=${zone_id}&stage=${stage}&type=patroller`;

      row += `<a href="${url}" class="cell ${riskClass}" 
     title="${stageLabels[i]}: ${value} (${percent.toFixed(1)}%)">
    ${value}
  </a>`;
    });

    row += '</div>';
    heatmapContainer.append(row);
  });

  // === Legend ===
  const legend = `
    <div class="heatmap-legend mt-2">
      <span><span class="box good"></span> Healthy</span>
      <span><span class="box medium"></span> Moderate Risk</span>
      <span><span class="box bad"></span> High Risk</span>
      <span><span class="box neutral"></span> Neutral</span>
    </div>`;
  heatmapContainer.append(legend);
}


function renderHeatmap(data) {
  const heatmapContainer = $('#frtHeatmap');
  heatmapContainer.empty();

  const stages = ['no_ticket', 'working', 'only_hold', 'completed', 'not_started'];
  const stageLabels = ['Not Assigned', 'Ongoing', 'Hold', 'Completed', 'Not Started'];

  // === Header Row ===
  let headerRow = `
    <div class="heatmap-row header">
      <div class="zone-name"></div>
      ${stageLabels.map(label => `<div class="cell-header">${label}</div>`).join('')}
    </div>`;
  heatmapContainer.append(headerRow);

  // === Zone Rows ===
  $.each(data.zones, function (zoneId, zoneData) {
    const frt = zoneData.frt;
    const zoneName = zoneData.zone_name;
    const zone_id = zoneData.zone_id;
    const total = frt.total || 1;

    let row = `<div class="heatmap-row">
      <div class="zone-name">${zoneName} (${frt.total})</div>`;

    stages.forEach((stage, i) => {
      const value = frt[stage] ?? 0;
      const percent = (value / total) * 100;

      let riskClass = 'neutral'; // default

      // === Color logic based on count + meaning ===
      switch (stage) {
        // ? High-risk stages (should be 0)
        case 'no_ticket':
        case 'not_started':
          riskClass = value === 0 ? 'good' : 'bad';
          break;

        // ?? Hold: some is okay, too much is bad
        case 'only_hold':
          if (value === 0) riskClass = 'good';
          else if (percent <= 10) riskClass = 'medium';
          else riskClass = 'bad';
          break;

        // ?? Working: should have some activity
        case 'working':
          riskClass = value > 0 ? 'good' : 'bad';
          break;

        // ?? Completed: more is better
        case 'completed':
          riskClass = value > 0 ? 'good' : 'neutral';
          break;

        default:
          riskClass = 'neutral';
      }

     

     const url = `/admin/workforce_details?zone_id=${zone_id}&stage=${stage}&type=frt`;

      row += `<a href="${url}" class="cell ${riskClass}" 
     title="${stageLabels[i]}: ${value} (${percent.toFixed(1)}%)">
    ${value}
  </a>`;
    });

    row += '</div>';
    heatmapContainer.append(row);
  });

  // === Legend ===
  const legend = `
    <div class="heatmap-legend mt-2">
      <span><span class="box good"></span> Healthy</span>
      <span><span class="box medium"></span> Moderate Risk</span>
      <span><span class="box bad"></span> High Risk</span>
      <span><span class="box neutral"></span> Neutral</span>
    </div>`;
  heatmapContainer.append(legend);
}
	


function renderCharts(data) {
    // 1?? Bar Chart - Overall Status Comparison
    new Chart(document.getElementById('statusComparisonChart'), {
        type: 'bar',
        data: {
            labels: ['Total', 'Not Assigned','Ongoing','Completed','Hold','Not Started'],
            datasets: [
                {
                    label: 'FRT Team',
                    data: [data.frt.total, data.frt.no_tickets, data.frt.working, data.frt.completed,data.frt.hold, data.frt.not_started],
                    backgroundColor: 'rgba(54, 162, 235, 0.7)'
                },
                {
                    label: 'Patrollers',
                    data: [data.pat.total, data.pat.no_tickets, data.pat.working, data.pat.completed,data.pat.hold, data.pat.not_started],
                    backgroundColor: 'rgba(255, 99, 132, 0.7)'
                }
            ]
        },
        options: {
            responsive: true,
            
            plugins: {
                title: { display: true,
                         text: 'Overall Status Comparison',
                         font: { size: 14, weight: 'bold' },
                         align: 'start',
                         padding: { bottom: 10 }
 
                       },
                legend: { position: 'bottom' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

// ?? Doughnut Chart - Work Distribution (FRT)
const frtCtx = document.getElementById('workDistributionChart');
const frtTotal = data.frt.no_tickets + data.frt.working + data.frt.completed + data.frt.hold + data.frt.not_started;

new Chart(frtCtx, {
    type: 'doughnut',
    data: {
        labels: ['Not Assigned','Ongoing', 'Completed','Hold', 'Not Started'],
        datasets: [{
            label: 'FRT Work Distribution',
            data: [
                data.frt.no_tickets,
                data.frt.working,
                data.frt.completed,
                data.frt.hold,
                data.frt.not_started
            ],
            backgroundColor: ['#F54927','#3b82f6', '#10b981','#F5CF27', '#f59e0b']
        }]
    },
    options: {
        plugins: {
            title: { 
                display: true, 
                text: `FRT Work Progress (Total: ${frtTotal})`, // ?? show total count in title
                font: { size: 14, weight: 'bold' },
                align: 'start',
                padding: { bottom: 10 }

            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const dataset = context.dataset;
                        const total = dataset.data.reduce((sum, val) => sum + val, 0);
                        const value = dataset.data[context.dataIndex];
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${context.label}: ${value} (${percentage}%)`;
                    }
                }
            },
            legend: {
                position: 'bottom',
                labels: {
                    generateLabels: function(chart) {
                        const data = chart.data;
                        const dataset = data.datasets[0];
                        return data.labels.map(function(label, i) {
                            const value = dataset.data[i];
                            const bgColor = dataset.backgroundColor[i];
                            return {
                                text: `${label} (${value})`,
                                fillStyle: bgColor,
                                strokeStyle: bgColor,
                                hidden: !chart.getDataVisibility(i),
                                index: i
                            };
                        });
                    }
                },
                onClick: function(e, legendItem, legend) { // ?? toggle visibility on click
                    if (legendItem.index === null) return;
                    const ci = legend.chart;
                    ci.toggleDataVisibility(legendItem.index);
                    ci.update();
                }
            },
            datalabels: false // in case plugin is active, disable external data labels
        },
        cutout: '65%', // nice donut look
        animation: { animateRotate: true, animateScale: true }
    }
});


// ?? Doughnut Chart - Work Distribution (Patrollers)
const patCtx = document.getElementById('patworkDistributionChart');
const patTotal = data.pat.no_tickets + data.pat.working + data.pat.completed + data.pat.hold + data.pat.not_started;

new Chart(patCtx, {
    type: 'doughnut',
    data: {
        labels: ['Not Assigned','Ongoing', 'Completed','Hold', 'Not Started'],
        datasets: [{
            label: 'Patrollers Work Distribution',
            data: [
                data.pat.no_tickets,
                data.pat.working,
                data.pat.completed,
                data.pat.hold,
                data.pat.not_started
            ],
            backgroundColor: ['#F54927','#3b82f6', '#10b981','#F5CF27', '#f59e0b']
        }]
    },
    options: {
        plugins: {
            title: { 
                display: true, 
                text: `Patrollers Work Progress (Total: ${patTotal})`,
                font: { size: 14, weight: 'bold' },
                align: 'start',
                padding: { bottom: 10 }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const dataset = context.dataset;
                        const total = dataset.data.reduce((sum, val) => sum + val, 0);
                        const value = dataset.data[context.dataIndex];
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${context.label}: ${value} (${percentage}%)`;
                    }
                }
            },
            legend: {
                position: 'bottom',
                labels: {
                    generateLabels: function(chart) {
                        const data = chart.data;
                        const dataset = data.datasets[0];
                        return data.labels.map(function(label, i) {
                            const value = dataset.data[i];
                            const bgColor = dataset.backgroundColor[i];
                            return {
                                text: `${label} (${value})`,
                                fillStyle: bgColor,
                                strokeStyle: bgColor,
                                hidden: !chart.getDataVisibility(i),
                                index: i
                            };
                        });
                    }
                },
                onClick: function(e, legendItem, legend) {
                    if (legendItem.index === null) return;
                    const ci = legend.chart;
                    ci.toggleDataVisibility(legendItem.index);
                    ci.update();
                }
            }
        },
        cutout: '65%',
        animation: { animateRotate: true, animateScale: true }
    }
});


    // 3?? Doughnut Chart - Login Activity Comparison
// Prepare dynamic labels with counts
const frtLabels = [
    `FRT Logged In (${data.frt.logged_in})`,
    `FRT Not Logged In (${data.frt.not_logged_in})`,
    `FRT Offline (${data.frt.offline})`
];

const patLabels = [
    `Patroller Logged In (${data.pat.logged_in})`,
    `Patroller Not Logged In (${data.pat.not_logged_in})`,
    `Patroller Offline (${data.pat.offline})`
];

// Combine into ONE list of labels
const finalLabels = [...frtLabels, ...patLabels];

// Combined data array
const finalData = [
    data.frt.logged_in, data.frt.not_logged_in, data.frt.offline,
    data.pat.logged_in, data.pat.not_logged_in, data.pat.offline
];

// Matching colors
const finalColors = [
    '#22c55e', '#ef4444', '#9ca3af',  // FRT
    '#3b82f6', '#f87171', '#d1d5db'   // Patrollers
];

// Draw chart
new Chart(document.getElementById('loginActivityChart'), {
    type: 'doughnut',
    data: {
        labels: finalLabels,
        datasets: [
            {
                data: finalData,
                backgroundColor: finalColors
            }
        ]
    },
    options: {
        plugins: {
            title: { 
                display: true, 
                text: `Login Activity (FRT vs Patrollers)`,
                font: { size: 14, weight: 'bold' },
                align: 'start',
                padding: { bottom: 10 }
            },
            legend: { position: 'bottom' }
        }
    }
});
  
}
</script>

<script>
let currentFilter = "all";   // DEFAULT FILTER

$(document).ready(function () {

    // LOAD DEFAULT (ALL)
    $.ajax({
        url: "{{ url('/admin/ongoing_ticket_data') }}",
        method: "GET",
        dataType: "json",

        success: function (response) {
            console.log("AGE REPORT LOADED:", response);
            renderPickupHeatmap(response);
        },

        error: function (xhr, status, error) {
            console.error("API ERROR:", error);
        }
    });

});
</script>


<script>

// ============================
// MAIN WRAPPER
// ============================
function renderPickupHeatmap(data) {
    renderPickupHeatmapFRT(data);
    renderPickupHeatmapPat(data);
}

// ============================
// FRT HEATMAP
// ============================
function renderPickupHeatmapFRT(data) {

    const container = $('#frtPickupHeatmap');
    container.empty();

    const ranges = ['0_4', '4_10', '10_24', '24_48', 'above_48'];
    const labels = ['0-4 hrs', '4-10 hrs', '10-24 hrs', '24-48 hrs', '> 48 hrs'];

    container.append(`
        <div class="heatmap-row header">
            <div class="zone-name">Zone</div>
            ${labels.map(l => `<div class="cell-header">${l}</div>`).join('')}
        </div>
    `);

    $.each(data.zones, function (zoneId, zone) {

        const frt = zone.frt;

        let row = `<div class="heatmap-row"><div class="zone-name">${zone.zone_name}</div>`;

        ranges.forEach(key => {
            const value = frt[key] ?? 0;

            // UPDATED: FILTER ADDED TO URL
            const url =
                `/admin/ongoing_tt_details?zone_id=${zone.zone_id}` +
                `&range=${key}&type=frt&filter=${currentFilter}`;

            let risk = 'neutral';
            if (key === '0_4') risk = 'good';
            else if (key === '4_10') risk = 'medium';
            else if (key === '10_24') risk = 'warning';
            else if (key === '24_48') risk = 'bad';
            else risk = 'high';

            row += `<a href="${url}" class="cell ${risk}">${value}</a>`;
        });

        row += "</div>";
        container.append(row);
    });

    addLegend(container);
}

// ============================
// PATROLLER HEATMAP
// ============================
function renderPickupHeatmapPat(data) {

    const container = $('#patPickupHeatmap');
    container.empty();

    const ranges = ['0_4', '4_10', '10_24', '24_48', 'above_48'];
    const labels = ['0-4 hrs', '4-10 hrs', '10-24 hrs', '24-48 hrs', '> 48 hrs'];

    container.append(`
        <div class="heatmap-row header">
            <div class="zone-name">Zone</div>
            ${labels.map(l => `<div class="cell-header">${l}</div>`).join('')}
        </div>
    `);

    $.each(data.zones, function (zoneId, zone) {

        const pat = zone.patrollers;

        let row = `<div class="heatmap-row"><div class="zone-name">${zone.zone_name}</div>`;

        ranges.forEach(key => {
            const value = pat[key] ?? 0;

            // UPDATED: FILTER ADDED TO URL
            const url =
                `/admin/ongoing_tt_details?zone_id=${zone.zone_id}` +
                `&range=${key}&type=patroller&filter=${currentFilter}`;

            let risk = 'neutral';
            if (key === '0_4') risk = 'good';
            else if (key === '4_10') risk = 'medium';
            else if (key === '10_24') risk = 'warning';
            else if (key === '24_48') risk = 'bad';
            else risk = 'high';

            row += `<a href="${url}" class="cell ${risk}">${value}</a>`;
        });

        row += "</div>";
        container.append(row);
    });

    addLegend(container);
}

// ============================
// LEGEND
// ============================
function addLegend(container) {
    container.append(`
        <div class="heatmap-legend mt-2">
            <span><span class="box good"></span> 0-4 hrs</span>
            <span><span class="box medium"></span> 4-10 hrs</span>
            <span><span class="box warning"></span> 10-24 hrs</span>
            <span><span class="box bad"></span> 24-48 hrs</span>
            <span><span class="box high"></span> > 48 hrs</span>
        </div>
    `);
}
</script>



<!-- ============================
     FILTER MENU SCRIPT
===============================-->
<script>

$(document).on("click", ".menu-btn", function (e) {
    e.stopPropagation();
    $(".menu-container").not($(this).parent()).removeClass("open");
    $(this).parent().toggleClass("open");
});

// Close dropdown on outside click
$(document).on("click", function () {
    $(".menu-container").removeClass("open");
});

// Filter selection
$(document).on("click", ".menu-item", function () {

    $(".menu-item").removeClass("active");
    $(this).addClass("active");

    currentFilter = $(this).data("filter");   // SAVE FILTER

    $(".menu-container").removeClass("open");

    loadHeatmapWithFilter(currentFilter);
});

// AJAX CALL — APPLY FILTER
function loadHeatmapWithFilter(filterType) {

    $.ajax({
        url: "{{ url('/admin/ongoing_ticket_data') }}",
        method: "GET",
        data: { filter: filterType },

        beforeSend: function () {
            console.log("Loading heatmap with filter:", filterType);
        },

        success: function (response) {
            renderPickupHeatmap(response); // Re-render with filter
        },

        error: function (xhr) {
            console.error("Filter API error:", xhr.responseText);
        }
    });
}

</script>


@endsection


@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
 <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>


/* ===========================
   3-DOT MENU (FILTER)
   =========================== */
.menu-container {
    position: relative;
    display: inline-block;
}

.menu-btn {
    border: none;
    background: transparent;
    font-size: 20px;
    cursor: pointer;
    padding: 4px 8px;
    line-height: 1;
    border-radius: 6px;
    color: #374151;
}

.menu-btn:hover {
    background: #f3f4f6;
}

/* Dropdown Box */
.menu-dropdown {
    position: absolute;
    top: 28px;
    right: 0;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    width: 140px;
    display: none;
    z-index: 1000;
}

/* Show When Open */
.menu-container.open .menu-dropdown {
    display: block;
}

/* Menu Items */
.menu-item {
    padding: 10px 14px;
    cursor: pointer;
    font-size: 14px;
    color: #374151;
    transition: background 0.2s ease;
}

.menu-item:hover {
    background: #f3f4f6;
    color: #111827;
}

.canvas-card { 
  background-color: #fff !important;
  border-radius: 16px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  padding: 16px;
}

.heatmap {
  overflow-x: auto;
  padding-bottom: 8px;
}

.heatmap-row {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  margin-bottom: 6px;
  min-width: 480px;
}

.zone-name {
  width: 110px;
  font-weight: 600;
  font-size: 14px;
  color: #111827;
}

.cell, .cell-header {
  flex: 1;
  min-width: 80px;
  text-align: center;
  border-radius: 8px;
  margin: 2px;
  padding: 8px 0;
  color: #fff;
  font-size: 13px;
  font-weight: 500;
  transition: all 0.2s ease;
}

.cell-header {
  background-color: #f3f4f6;
  color: #374151;
  font-weight: 600;
  border-radius: 8px;
}

/* === Color Logic === */
.cell.good {
  background-color: #28a745; /* Green */
  color: white;
}
.cell.medium {
  background-color: #ffc107; /* Orange */
  color: black;
}
.cell.bad {
  background-color: #dc3545; /* Red */
  color: white;
}
.cell.neutral {
  background-color: #6c757d; /* Gray */
  color: white;
}

.cell.warning {
    background-color: #f97316 !important;   /* orange */
}

.cell.high {
    background-color: #7f1d1d !important;   /* dark red */
}

.heatmap-legend {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
  margin-top: 12px;
  flex-wrap: wrap;
  font-size: 13px;
  color: #374151;
  line-height: 1;
}

.heatmap-legend span {
  display: flex;
  align-items: center;
  gap: 6px;
  white-space: nowrap;
}

.heatmap-legend .box {
  width: 14px;
  height: 14px;
  display: inline-block;
  border-radius: 3px;
  flex-shrink: 0;
}

/* Legend boxes */
.heatmap-legend .box.good { background-color: #28a745; }
.heatmap-legend .box.medium { background-color: #ffc107; }
.heatmap-legend .box.bad { background-color: #dc3545; }
.heatmap-legend .box.neutral { background-color: #6c757d; }
.heatmap-legend .box.high { background-color: #7f1d1d; }
.heatmap-legend .box.warning { background-color: #f97316; }

@media (max-width: 576px) {
  .zone-name {
    width: 60px;
    font-size: 12px;
  }
  .cell, .cell-header {
    font-size: 12px;
    min-width: 65px;
    padding: 6px 0;
  }
}

    
/* Reset and Base Styles */
* {
    box-sizing: border-box;
}


/* Pagination */

.pagination-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 0.5rem;
}

.pagination-info {
    font-size: 0.875rem;
    color: #718096;
}

.pagination {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 0.25rem;
}

.page-item {
    display: block;
}

.page-link {
    padding: 0.5rem 0.75rem;
    border: 1px solid #e2e8f0;
    color: #4a5568;
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.page-item.active .page-link {
    background-color: #4299e1;
    border-color: #4299e1;
    color: white;
}

.page-item.disabled .page-link {
    color: #a0aec0;
    cursor: not-allowed;
}

.page-link:hover:not(.disabled) {
    background-color: #f7fafc;
    border-color: #cbd5e0;
}

.attendance-dashboard {
    background-color: #f8fafc;
    min-height: 100vh;
    padding: 1.5rem 0;
}

.container-fluid {
    max-width: 1600px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

/* Header Section */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.dashboard-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}
.td-actions {
    white-space: nowrap;
}
.action-btn {
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    border: 1px solid;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-right: 0.5rem;
}

.action-btn-primary {
    background-color: transparent;
    border-color: #4299e1;
    color: #4299e1;
}

.action-btn-primary:hover {
    background-color: #4299e1;
    color: white;
}

.action-btn-secondary {
    background-color: transparent;
    border-color: #a0aec0;
    color: #718096;
}

.action-btn-secondary:hover {
    background-color: #718096;
    color: white;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}





/* Filters Section */
.filters-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.filters-row {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 1rem;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #64748b;
    margin-bottom: 0.5rem;
}

.filter-select,
.filter-input {
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.875rem;
    background: white;
    transition: border-color 0.2s ease;
}

.filter-select:focus,
.filter-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.quick-filters {
    grid-column: span 1;
}

.quick-filter-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.quick-filter-checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #64748b;
    cursor: pointer;
}

.quick-filter-checkbox input[type="checkbox"] {
    margin: 0;
}

/* Statistics Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
    flex-shrink: 0;
}

/* ===== Light Background Stat Icons ===== */
.stat-icon-blue {
    background-color: #dbeafe;   /* Light blue */
    color: #3b82f6;              /* Text/Icon color */
}

.stat-icon-green {
    background-color: #d1fae5;   /* Light green */
    color: #10b981;
}

.stat-icon-red {
    background-color: #fee2e2;   /* Light red */
    color: #ef4444;
}

.stat-icon-orange {
    background-color: #fef3c7;   /* Light orange */
    color: #f59e0b;
}

.stat-icon-purple {
    background-color: #ede9fe;   /* Light purple */
    color: #8b5cf6;
}

.stat-icon-teal {
    background-color: #ccfbf1;   /* Light teal */
    color: #14b8a6;
}

.stat-icon-indigo {
    background-color: #e0e7ff;   /* Light indigo */
    color: #c;
}

.stat-icon-cyan {
    background-color: #cffafe;   /* Light cyan */
    color: #06b6d4;
}

/* Main Content Grid */
.main-content-grid {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 2rem;
}

/* Left Column */
.left-column {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

/* Matrix Card */
.matrix-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 1.5rem 0;
}

.matrix-table {
    overflow-x: auto;
}

.matrix {
    width: 100%;
    border-collapse: collapse;
}

.matrix-header {
    background: #f8fafc;
    padding: 0.75rem 1rem;
    text-align: left;
    font-size: 0.875rem;
    font-weight: 500;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
}

.matrix td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f1f5f9;
}
.role-cell {
    font-weight: 500;
    color: #1e293b;
}

.matrix-cell {
    /* text-align: center; */
}

.matrix-value {
    font-weight: 600;
    color: #10b981;
}

.matrix-total {
    color: #64748b;
    font-size: 0.875rem;
}

.total-cell {
    font-weight: 600;
    color: #1e293b;
}

/* Records Card */
.records-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    flex: 1;
}

.records-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.export-btn {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
}

.export-btn:hover {
    background: #2563eb;
}

.records-table {
    overflow-x: auto;
    max-height: 800px;   /* adjust height */
    overflow-y: auto;    /* vertical scroll */
    max-width:800px;
    /* border: 1px solid #e5e7eb;
    border-radius: 8px; */
    
}

.attendance-table {
    width: 100%;
    border-collapse: collapse;
}

.attendance-table thead th {
    background: #f8fafc;
    padding: 0.75rem 1rem;
    text-align: left;
    font-size: 0.875rem;
    font-weight: 500;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}

.attendance-table tbody td {
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.record-row:hover {
    background: #f8fafc;
}

.staff-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.staff-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    border-color: #000000;
}

.staff-name {
    font-weight: 500;
    color: #1e293b;
    font-size: 0.875rem;
    margin: 0;
}

.staff-id {
    font-size: 0.75rem;
    color: #64748b;
    margin: 0;
}

.role-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 16px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}
.role-ofc { background: #e0f2fe; color: #0369a1; }
.role-frt { background: #f0fdf4; color: #166534; }
.role-patroller { background: #fef2f2; color: #b91c1c; }
.role-zonal-incharge { background: #fdf4ff; color: #7e22ce; }
.role-district-incharge { background: #fff7ed; color: #c2410c; }
.role-unknown { background: #e2e8f0; color: #475569; }

.time-cell {
    color: #10b981;
    font-weight: 500;
}

.patrol-cell {
    color: #10b981;
    font-weight: 500;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 16px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-done {
    background: #f0fdf4;
    color: #166534;
}

.status-late {
    background: #fef3c7;
    color: #92400e;
}

.status-present {
    background: #f0fdf4;
    color: #166534;
}

.score-cell {
    font-weight: 500;
    color: #1e293b;
}

/* Right Column */
.right-column {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* Map Card */
.map-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.map-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.view-full-btn {
    background: transparent;
    border: 1px solid #e2e8f0;
    color: #64748b;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
}

.view-full-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e0;
}

.map-container {
    height: 200px;
    background: #f8fafc;
    border-radius: 8px;
    position: relative;
    overflow: hidden;
}

.map-placeholder {
    height: 100%;
    /* display: flex; */
    align-items: center;
    justify-content: center;
}
#dashboardMap {
        height: 50%;
        min-height: 320px;
}

.map-content {
    text-align: center;
    color: #64748b;
}

.active-indicator {
    position: absolute;
    /* top: 1rem; */
    right: 1rem;
    background: white;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.active-count {
    font-size: 1.25rem;
    font-weight: 600;
    color: #3b82f6;
    display: block;
}

.active-label {
    font-size: 0.75rem;
    color: #64748b;
}

.map-visual i {
    font-size: 2rem;
    color: #cbd5e1;
    margin-bottom: 0.5rem;
}

.map-visual p {
    margin: 0;
    font-size: 0.875rem;
    color: #64748b;
}

.map-subtitle {
    font-size: 0.75rem !important;
    color: #94a3b8 !important;
}

/* Exceptions Card */
.exceptions-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.exceptions-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.exception-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.exception-warning {
    background: #fef3c7;
    border-left: 4px solid #f59e0b;
}

.exception-info {
    background: #dbeafe;
    border-left: 4px solid #3b82f6;
}

.exception-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.exception-warning .exception-icon {
    background: #f59e0b;
    color: white;
}

.exception-info .exception-icon {
    background: #3b82f6;
    color: white;
}

.exception-content {
    flex: 1;
}

.exception-title {
    font-weight: 500;
    color: #1e293b;
    font-size: 0.875rem;
    margin: 0 0 0.125rem 0;
}

.exception-subtitle {
    font-size: 0.75rem;
    color: #64748b;
    margin: 0;
}

.exception-action {
    background: transparent;
    border: 1px solid #e2e8f0;
    color: #3b82f6;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.exception-action:hover {
    background: #f8fafc;
    border-color: #3b82f6;
}

/* Insights Card */
.insights-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.insights-card .card-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #1e293b;
}

.insights-card .card-title i {
    color: #f59e0b;
}

.insights-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.insight-item {
    background: #f8fafc;
    padding: 0.75rem;
    border-radius: 8px;
    border-left: 3px solid #3b82f6;
}

.insight-item p {
    margin: 0;
    font-size: 0.875rem;
    color: #475569;
    line-height: 1.4;
}

/* Trend Card */
.trend-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.trend-placeholder {
    height: 150px;
    background: #f8fafc;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.trend-chart {
    text-align: center;
    color: #64748b;
}

.trend-chart i {
    font-size: 2rem;
    color: #cbd5e1;
    margin-bottom: 0.5rem;
}

.trend-chart p {
    margin: 0;
    font-size: 0.875rem;
}

.trend-subtitle {
    font-size: 0.75rem !important;
    color: #94a3b8 !important;
}

/* Responsive Design */
@media (max-width: 1400px) {
    .main-content-grid {
        grid-template-columns: 1fr 350px;
    }
}

@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .filters-row {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 992px) {
    .main-content-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .filters-row {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .filters-row {
        grid-template-columns: 1fr;
    }
    
    .container-fluid {
        padding: 0 1rem;
    }
    
    .attendance-dashboard {
        padding: 1rem 0;
    }
}

@media (max-width: 576px) {
    .dashboard-title {
        font-size: 1.5rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
}

/* Utility Classes */
.fa {
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
}
.bi {
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
}

</style>
@endsection

