<?php $__env->startSection('title', 'Tickets'); ?>

<?php $__env->startSection('content'); ?>

<style type="text/css">
    table.dataTable thead th {
        background-color: #d9d9d9f5 !important;
        border-bottom: none !important;
    }
    .buttons-html5{
        border-radius: 10px;
/*        margin-right: 6px;*/
    }
    table.display tbody tr:hover td{
        background-color: #f1eeeef5 !important;
    }
    .dataTables_scrollBody thead {
        visibility: hidden;
    }
    select.filter-box:not([size]):not([multiple]), input.filter{
        height: 30px;
        background-color: #f6f7f7 !important;
        border:none;
        color:#000;
        padding-top: 4px !important;
    }

    .nav-cstm .nav-link-cstm:not(.active):hover{
        color: #333333 !important;
        border-bottom:3px solid #edf1f2;
        transition: none !important;
    }

    .nav-cstm .nav-link-cstm{
        font-weight: 600;
        color: #636f73 !important;
    }

    .nav-link-cstm.active{
        background-color: transparent !important;
        color: #2b3eb1 !important;
        border-bottom: 3px solid #2b3eb1;        
    }
    .filter-box{
        border-radius: 25px;
        height: 30px !important;
    }
    #table-5_filter label{
        display: none !important;
    }
    .pt-5 {
     padding-top:5px;  
     }
    .br-10{
        border-radius: 10px;
    }
    .dropdown-menu{
        left: -50px !important;
    }

</style>
<div class="content-area py-1" id="main_content">
    <div class="container-fluid">
        <div class="box box-block bg-white">
            <?php if(Setting::get('demo_mode') == 1): ?>
        <div class="col-md-12" style="height:50px;color:red;">
                    ** Demo Mode : <?php echo app('translator')->get('admin.demomode'); ?>
                </div>
                <?php endif; ?>
             <?php if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'super_admin'): ?>
            <a href="<?php echo e(route('admin.tickets.create')); ?>" style="margin-left: 1em;" class="btn btn-primary pull-right b-a-radius-0-5"><i class="fa fa-plus"></i> Add New Ticket</a>
            <a href="<?php echo e(route('admin.import')); ?>" style="margin-left: 1em;" class="btn btn-success pull-right b-a-radius-0-5"><i class="fa fa-upload"></i> Upload CSV File</a>
             <?php endif; ?>
            <h4 class="mb-2">Tickets History</h4>

            <ul class="nav nav-pills mb-1 b-b nav-cstm">
              <li class="nav-item mr-0-5">
                <a href="<?php echo e(route('admin.tickets1')); ?>" class="nav-link nav-link-cstm pb-1 <?php echo e(@Request::get('status') == ''? 'active' : ''); ?>">All</a>
              </li>
              <?php $__currentLoopData = $ticket_status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                <li class=" nav-item mr-0-5">
                    <a href="<?php echo e(route('admin.tickets1',['status' => $status])); ?>" class="nav-link nav-link-cstm pb-1 <?php echo e($status == @Request::get('status') ? 'active' : ''); ?>"><?php echo e($status); ?></a>
                </li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
            </ul>
            <form action="<?php echo e(route('admin.tickets1', $query_params)); ?>" method="GET">
            <ul class="nav nav-pills mb-2 pb-1 b-b">
                <li class="nav-item mr-0-75">
                    <input type="text" class="form-control filter-box" id="searchInput" name="searchinfo" placeholder="Search..." onkeydown="return event.key != 'Enter';"  value="<?php echo e(@Request::get('searchinfo')); ?>" >
                </li>
                <li class="nav-item mr-0-75">
                    <select class="form-control selectpicker filter-box" data-show-subtext="true" data-live-search="true" name="district_id" id="searchblocklist">
                        <option value="">District</option>
                        <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <option value="<?php echo e($district->name); ?>" rel="<?php echo e($district->id); ?>" <?php if(Request::get('district_id')): ?> <?php if(@Request::get('district_id') == $district->name): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e($district->name); ?> </option> 
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?> 
                    </select>

                </li>
                  <li class="nav-item mr-0-75">
                    <select class="form-control selectpicker filter-box" data-show-subtext="true" data-live-search="true" name="zone_id" id="searchzonelist">
                        <option value="">Zonal</option>
                        <?php $__currentLoopData = $zonals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zone): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <option value="<?php echo e($zone->id); ?>" rel="<?php echo e($zone->id); ?>" <?php if(Request::get('zone_id')): ?> <?php if(@Request::get('zone_id') == $zone->id): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e($zone->Name); ?> </option> 
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?> 
                    </select>

                </li>

                <li class="nav-item mr-0-75">
                    <select class="form-control selectpicker filter-box" data-show-subtext="true" data-live-search="true" name="block_id" id="getblock">
                        <option value="">Block</option>
                        <?php $__currentLoopData = $blocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <option value="<?php echo e($district->name); ?>" <?php if(Request::get('block_id')): ?> <?php if(@Request::get('block_id') == $district->name): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e($district->name); ?> </option> 
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?> 
                    </select>
                </li>
                 <li class="nav-item mr-0-75">
                    <select class="form-control selectpicker filter-box" data-show-subtext="true" data-live-search="true" name="category" id="getcategory">
                        <option value="">Category</option>
                        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <option value="<?php echo e($service->name); ?>" <?php if(Request::get('category')): ?> <?php if(@Request::get('category') == $service->name): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e($service->name); ?> </option> 
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?> 
                    </select>
                </li>

                <li class="nav-item mr-0-75">
                    <input class="form-control filter-box filter" type="date" id="from_date" name="from_date" placeholder="From Date" value="<?php echo e(@Request::get('from_date')); ?>"  onclick="this.showPicker()">
                </li>
               
                 <li class="nav-item mr-0-75 pt-5">
                  To  
                 </li>

                 <li class="nav-item mr-0-75">
                    <input class="form-control filter-box filter" type="date" id="to_date" name="to_date" placeholder="To Date" value="<?php echo e(@Request::get('to_date')); ?>"  onclick="this.showPicker()">
                </li>

                <li class="nav-item mr-0-75 pull-right">
                    <button type="submit" class="form-control btn btn-primary btn-cstm" style="height:30px">Apply</button>
                </li>
            </ul>
            <input type="hidden" value="<?php echo e(@Request::get('status')); ?>" name="status">
            </form>

            <?php if(count($tickets) != 0): ?>
            <table class="table row-bordered dataTable nowrap display" id="table-5" style="width:100%">
                <thead>
                    <tr>
                        <th>Ticket Id</th>
                        <th>Zonal</th>
                        <th>District Name</th>
                        <th>Block Name</th>
                        <th>GP Name</th>
                        <th>First Name</th>
                        <th>Last Name</th>     
                        <th>Mobile</th>
                        <th>LGD Code</th>
                        <th>Down Reason</th>
                        <th>Description</th>
                        <th>Ticket Down Date</th>
                        <th>Ticket Down Time</th>
                        <th>Source Address</th>
                        <th>Source Latitude</th>
                        <th>Source Longitude</th>
                        <th>Destination Address</th>
                        <th>Destination Latitude</th>
                        <th>Destination Longitude</th>
                        <th>Ticket Assigned Time</th>
                        <th>Ticket Started Time</th>
                        <th>Ticket Started Location</th>
                        <th>Ticket Reached Time</th>
                        <th>Ticket Reached Location</th>
                        <th>Ticket Closed Time</th>
                        <th>During Hours</th>
                       <?php
                        if (isset($_GET['status']) && $_GET['status'] == 'Completed') { ?>                        
                        <th>Ticket  Closed</th>
                        <?php } else { ?>
                        <th>Ticket Assigned</th>
                        <?php } 
                         ?>
                        <th>Status</th>
                        <th>Action</th>
                        
                    </tr>
                </thead>
                <tbody> 
                 <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $request): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
             
                    <tr>
                        <td class="font-weight-bold"><?php echo e($request->ticketid); ?></td>
                        <td><?php echo e($request->zone_name); ?></td>
                        <td><?php echo e($request->district); ?></td>
                        <td>
                         <?php echo e($request->mandal); ?>

                        </td>
                         <td>
                         <?php echo e($request->gpname); ?>

                        </td>
                        <td><?php echo e($request->first_name); ?></td>
                        <td><?php echo e($request->last_name); ?></td>
                        <td><?php echo e($request->mobile); ?></td>
                         <td>
                         <?php echo e($request->lgd_code); ?>

                        </td>

                        <td><?php echo e($request->downreason); ?></td>
                        
                        <td>
                         <?php echo e($request->downreasonindetailed); ?>   
                            
                        </td>
                        <td>
                         <?php echo e($request->downdate); ?></td>
                        <td><?php echo e($request->downtime); ?></td>
                        <td><?php echo e($request->s_address); ?></td>
                        <td><?php echo e($request->s_latitude); ?></td>
                        <td><?php echo e($request->s_longitude); ?></td>
                        <td><?php echo e($request->d_address); ?></td>
                        <td><?php echo e($request->d_latitude); ?></td>
                        <td><?php echo e($request->d_longitude); ?></td>
                        <td><?php echo e($request->assigned_at); ?></td>
                        <td><?php echo e($request->started_at); ?></td>
                        <td><?php echo e($request->started_location); ?></td>
                        <td><?php echo e($request->reached_at); ?></td>
                        <td><?php echo e($request->reached_location); ?></td>
                        <td><?php echo e($request->finished_at); ?></td>
                        <?php
$downdate = $request->downdate;
$downtime = $request->downtime;
$downdatetime = date('Y-m-d H:i:s', strtotime("$downdate $downtime"));
$todaydatetime = date('Y-m-d H:i:s');

if(!empty($request->finished_at)) {
    $seconds = strtotime($request->finished_at) - strtotime($downdatetime);
} else {
    $seconds = strtotime($todaydatetime) - strtotime($downdatetime);
}

// Calculate hours, minutes properly
$hours = floor($seconds / 3600);
$minutes = floor(($seconds / 60) % 60);

// Format as hh:mm
$formattedTime = sprintf("%02d:%02d", $hours, $minutes);
?>
                        <td><?php echo e($formattedTime); ?></td>
                        <td><?php echo e($request->autoclose); ?></td>

                        <?php if($request->status != ''){?>
                        <td>
                            <?php if($request->status == 'COMPLETED'): ?>
                                <span class="tag tag-success tag-brp"> <?php echo e($request->status); ?> </span>
                            <?php elseif($request->status == 'CANCELLED'): ?>
                                <span class="tag tag-danger tag-brp"> <?php echo e($request->status); ?> </span>
                            <?php elseif($request->status == 'SEARCHING'): ?>
                                <span class="tag tag-warning tag-brp"> <?php echo e($request->status); ?> </span>
                            <?php elseif($request->status == 'SCHEDULED'): ?>
                                <span class="tag tag-primary tag-brp"> <?php echo e($request->status); ?> </span>
                            <?php else: ?> 
                                <span class="tag tag-info tag-brp"> <?php echo e($request->status); ?> </span>
                            <?php endif; ?>
                        </td>
                        <?php } else {?>
                          <td><span class="tag tag-info tag-brp">Not Assigned</span></td>
                         <?php } ?> 
                         <?php if($request->status != ''){?>
                           <td>
                                <div class="input-group-btn">
                                  <button type="button" class="btn btn-info b-a-radius-0-5 dropdown-toggle pull-left" data-toggle="dropdown">Action
                                    <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu">
                                    <li>
                                        <a href="<?php echo e(route('admin.requests.show', $request->request_id)); ?>" class="btn btn-default"><i class="fa fa-search"></i> More Details</a>
                                    </li>
                                    <?php if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'super_admin'): ?>
                                    <li>
                                        <a href="<?php echo e(route('admin.tickets.edit', $request->master_id)); ?>" class="btn btn-default"><i class="fa fa-pencil"></i> <?php echo app('translator')->get('admin.edit'); ?></a>
                                    </li>
                                     <?php if($request->status == 'SEARCHING'){ ?>
                                     <li>
                                        <a href="<?php echo e(route('admin.dispatcher.assignform', $request->request_id)); ?>" class="btn btn-default"><i class="fa fa-arrows"></i> Assign</a>
                                    </li>
                                   <?php } ?>
                                                                 
                                 
                                      <?php if($request->status != 'COMPLETED'){ ?>
                                    <li>
                                        <a href="<?php echo e(route('admin.dispatcher.completeform', $request->request_id)); ?>" class="btn btn-default"><i class="fa fa-arrows"></i> Request Close </a>
                                    </li>
                                     <?php } ?>
                                   <?php endif; ?>
                              
                                                                     
                                     <?php if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'super_admin' || auth()->user()->role == 'zone_admin'): ?>

                                      <?php if($request->status == 'INCOMING' || $request->status == 'ONHOLD'){ ?>
                                     <li>
                                        <a href="<?php echo e(route('admin.dispatcher.assignform', $request->request_id)); ?>" class="btn btn-default"><i class="fa fa-arrows"></i> Re-Assign</a>
                                    </li>
                                   <?php } ?>
         
                                    <?php if($request->status == 'INCOMING'){ ?>
                                    <li>
                                        <a href="<?php echo e(route('admin.dispatcher.onholdform', $request->request_id)); ?>" class="btn btn-default"><i class="fa fa-arrows"></i> On Hold </a>
                                    </li>
                                     <?php } ?>
                                     <?php endif; ?>

                                  </ul>
                                </div>
                            </td>
                           <?php } else{?>
                            <td>
                             <div class="input-group-btn">
                                  <button type="button" class="btn btn-info dropdown-toggle b-a-radius-0-5 pull-left" data-toggle="dropdown">Action
                                    <span class="caret"></span>
                                  </button>
                              </div>
                              </td>    
                           <?php } ?>   
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                </tbody>
              </table>
            <?php else: ?>
            <h6 class="no-result">No results found</h6>
            <?php endif; ?> 
        </div>
         Showing <?php echo e($tickets->currentPage() != 1 ? $tickets->currentPage() * 10 - 9 : $tickets->currentPage()); ?> to <?php echo e($tickets->currentPage() * $tickets->perPage()); ?> of <?php echo e($tickets->total()); ?> entries
    </div>
      <?php echo e($tickets->appends(['status' => @$status_get,'district_id'=>@$district_id_get,'searchinfo'=>@$serch_term_get,'zone_id'=>@$zone_id_get,'team_id'=>@$team_id_get,'block_id'=>@$block_id_get,'from_date'=>@$from_date_get,'to_date'=>@$to_date_get,'autoclose'=>@$autoclose_get,'default_autoclose'=>@$default_autoclose_get,'category'=>@$category_get,'newfrom_date'=>@$newfrom_date_get,'newto_date'=>@$newto_date_get,'range'=>@$range_get])->links()); ?>

   </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
 <script type="text/javascript">
    /*$('#table-5').DataTable( {
        scrollX: true,
        searching: false,
        responsive: true,
        paging: false,
        info:false,
        dom: 'Bfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ]
    } );*/

$('#searchblocklist').change(function(){
        var nid = $(this).find('option:selected').attr('rel');
        if(nid){
        $.ajax({
           type:"get",
            url: 'https://fleet.terasoftware.com/public/westbengal/public/admin/getSearchblocklist/'+ nid,
            success:function(res)
           {       
                if(res)
                {
                    $("#getblock").empty();
                    $("#getblock").append('<option value="">Select block</option>');
                    $.each(res,function(key,value){
                        $("#getblock").append('<option value="'+value+'">'+value+'</option>');
                    });
                }
           }

        });
        }
});

$(document).ready(function() {
  var table = $('#table-5').DataTable( {
        scrollX: true,
        searching: true,
        responsive: false,
        paging:false,
        info: false,
        dom: 'Bfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ]
    } );

  $('#searchInput').on('keyup', function() {
    console.log('haii');
    // table.search(this.value, true, false).draw();
    // var srch_info = this.value;
    // if(srch_info.length > 3){
    //     var csrf_tokenn = '<?php echo e(csrf_token()); ?>';
    //     $.ajax({
    //         url: "<?php echo e(route('admin.tickets1', $query_params)); ?>",
    //         method: 'GET',
    //         data: {'_token': csrf_tokenn, 'searchinfo':srch_info },
    //         success: function(response) {
    //             $('#main_content').html(response);
    //             // table.clear().draw();
    //             table.rows.add($('#table-5 tbody tr')).draw();
    //         }
    //     });
    // }
  });
});

jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
   if ( this.context.length ) {

     var string = window.location.search;
            if(string == ''){
                string = '?page=all';                         
            }
     var jsonResult = $.ajax({
       url: "<?php echo e(url('admin/tickets')); ?>"+string,
       data: {},
       success: function (result) {                       
         p = new Array();
		 console.log(p);
         var current = 1;
         $.each(result.data, function (i, d)
         {
           var item = [
           current,
		   d.ticketid,
           d.zone_name,
           d.district,
           d.mandal,
           d.gpname,
           d.lgd_code,
           d.subsategory,
           d.downreason,
           d.downreasonindetailed,
           d.downdate,
		   d.downtime,
		   d.first_name,
		   d.last_name,
		   d.mobile,
		   d.s_address,
		   d.s_latitude,
		   d.s_longitude,
		   d.d_address,
		   d.d_latitude,
		   d.d_longitude,
		   d.assigned_at,
		   d.started_at,
		   d.started_location,
		   d.reached_at,
		   d.reached_location,
		   d.finished_at,
                   d.autoclose,
           d.status
           ];
           p.push(item);
           current++;
         });
       },
       async: false
     });
     var head=new Array();
     head.push(
       "ID",
       "Ticket ID",
       "Zonal",
       "District Name",
       "Block Name",
       "GP Name",
       "LGD Code",
       "Category",
       "Down Reason",
       "Description",
       "Ticket Down Date",
       "Ticket Down Time",
	   "First Name",
	   "Last Name",
	   "Mobile",
	   "Source Address",
	   "Source Latitude",
	   "Source Longitude",
	   "Destination Address",
	   "Destination Latitude",
	   "Destination Longitude",
	   "Ticket Assigned Time",
	   "Ticket Started Time",
	   "Ticket Started Location",
	   "Ticket Reached Time",
	   "Ticket Reached Location",
	   "Ticket Closed Time",
           "Ticket Assigned",
       "Status"
       );            
     return {body: p, header: head};
   }
 } );


</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>