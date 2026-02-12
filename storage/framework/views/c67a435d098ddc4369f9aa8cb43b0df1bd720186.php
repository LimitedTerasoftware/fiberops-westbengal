<?php $__env->startSection('title', 'GPs Downreports '); ?>

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
    select.select-box:not([size]):not([multiple]), input.select-box{
        height: 35px;
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
   .btn-cstm1{
   background:red;
  text:#fff; 
   }


</style>
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">


              <form action="<?php echo e(route('admin.reports')); ?>" method="GET">
            <ul class="nav nav-pills mb-2 pb-1 b-b">

                 <li class="nav-item mr-0-75">
                    <select class="form-control selectpicker filter-box" data-show-subtext="true" data-live-search="true" name="zone_id" id="searchzonelist">
                        <option value="">Zonal</option>
                        <?php $__currentLoopData = $zonals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zone): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <option value="<?php echo e($zone->id); ?>" rel="<?php echo e($zone->id); ?>" <?php if(Request::get('zone_id')): ?> <?php if(@Request::get('zone_id') == $zone->id): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e($zone->Name); ?> </option> 
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?> 
                    </select>

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
                    <select class="form-control selectpicker filter-box" data-show-subtext="true" data-live-search="true" name="block_id" id="getblock">
                        <option value="">Block</option>
                        <?php $__currentLoopData = $blocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <option value="<?php echo e($district->name); ?>" <?php if(Request::get('block_id')): ?> <?php if(@Request::get('block_id') == $district->name): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e($district->name); ?> </option> 
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?> 
                    </select>
                </li>


                 
                  <li class="nav-item mr-0-75">
                    <input class="form-control filter-box filter" type="date" id="from_date" name="from_date" placeholder="From Date" value="<?php echo e(@Request::get('from_date')); ?>"  onclick="this.showPicker()">
                
                <li class="nav-item mr-0-75">
                    <input class="form-control filter-box filter" type="date" id="to_date" name="to_date" placeholder="To Date" value="<?php echo e(@Request::get('to_date')); ?>"  onclick="this.showPicker()">
                </li>

                <li class="nav-item mr-0-75 pull-right mt">
                    <button type="submit" class="form-control btn btn-primary btn-cstm" style="height:30px">Apply</button>
                </li>

                 <!-- Reset Filters Button -->
                  <li class="nav-item mr-0-75 pull-right mt">
                  <button type="button" id="resetFilters" class="form-control btn btn-primary btn-cstm" style="height:30px;">Reset</button>
               </li>

            </ul>
            </form>



            <?php if(Setting::get('demo_mode') == 1): ?>
        <div class="col-md-12" style="height:50px;color:red;">
                    ** Demo Mode : <?php echo app('translator')->get('admin.demomode'); ?>
                </div>
                <?php endif; ?>
            <h4 class="mb-1">
                <?php echo app('translator')->get('admin.gp.gp'); ?>
                <?php if(Setting::get('demo_mode', 0) == 1): ?>
                <span class="pull-right">(*personal information hidden in demo)</span>
                <?php endif; ?>
            </h4>
            
            <table class="table row-bordered dataTable nowrap display" id="table-5" style="width:100%">
                <thead>
                    <tr>
                        <th><?php echo app('translator')->get('admin.id'); ?></th>
                        <th><?php echo app('translator')->get('GP Name'); ?></th>
                        <th><?php echo app('translator')->get('District'); ?></th>
                        <th><?php echo app('translator')->get('Block'); ?></th>
                        <th>Zonal Incharge</th>
                        <th><?php echo app('translator')->get('LGD Code'); ?></th>
                        <th>Down Hours</th>
                     </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $downreport; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $gp): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                    <tr>
                        <td><?php echo e($index); ?></td>
                        <td class="font-weight-bold"><?php echo e($gp->gpname); ?></td>
                        <td><?php echo e($gp->district); ?></td>
                        <td><?php echo e($gp->mandal); ?></td>
                        <td><?php echo e($gp->zone_name); ?></td>
                        <td><?php echo e($gp->lgd_code); ?></td>
                        <td><?php echo e($gp->total_gps_down_hours); ?></td>
                        
                   </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script type="text/javascript">

$('#searchblocklist').change(function(){
        var nid = $(this).find('option:selected').attr('rel');
        if(nid){
        $.ajax({
           type:"get",
            url: '/admin/getSearchblocklist/'+ nid,
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


    $('#table-5').DataTable( {
        scrollX: true,
        searching: true,
        paging:true,
        info:true,
        dom: 'Bfrtip',
        // buttons: [
        //     'copyHtml5',
        //     'excelHtml5',
        //     'csvHtml5',
        //     'pdfHtml5'
        // ]
        buttons: [
            {
                extend: 'copyHtml5',
                exportOptions: {
                    modifier: {
                      page: 'all'
                    }
                  }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    modifier: {
                      page: 'all'
                    }
                  }
            },
            {
                extend: 'csvHtml5',
                exportOptions: {
                    modifier: {
                      page: 'all'
                    }
                  }
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    modifier: {
                      page: 'all'
                    }
                  }
            }
        ]
    } );
    
    
</script>

<script>
document.getElementById('resetFilters').addEventListener('click', function() {
    // Reset all filters
    document.getElementById('searchzonelist').value = "";
    document.getElementById('searchblocklist').value = "";
    document.getElementById('getblock').value = "";
    document.getElementById('from_date').value = "";
    document.getElementById('to_date').value = "";

    // Reload the page without filters
    //window.location.href = "<?php echo e(route('admin.reports')); ?>";
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>