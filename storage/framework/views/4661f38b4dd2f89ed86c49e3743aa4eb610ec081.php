<?php $__env->startSection('title', 'GPs '); ?>

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
</style>
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">
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
            <?php if(auth()->user()->role != 'super_admin'): ?>
            <a href="<?php echo e(route('admin.gps.create')); ?>" style="margin-left: 1em;" class="btn btn-primary pull-right btn-cstm"><i class="fa fa-plus"></i><?php echo app('translator')->get('admin.gp.add_gp'); ?></a>
            <?php endif; ?>
            <table class="table row-bordered dataTable nowrap display" id="table-5" style="width:100%">
                <thead>
                    <tr>
                        <th><?php echo app('translator')->get('admin.id'); ?></th>
                        <th><?php echo app('translator')->get('GP Name'); ?></th>
                        <th><?php echo app('translator')->get('District'); ?></th>
                        <th><?php echo app('translator')->get('Block'); ?></th>
                        <th>Zonal Incharge</th>
                        <th><?php echo app('translator')->get('LGD Code'); ?></th>
                        <th>FRT Name</th>
                        <th><?php echo app('translator')->get('Contact No'); ?></th>
                        <th>Patroller Name</th>
                        <th><?php echo app('translator')->get('Contact No'); ?></th>
                        <?php if(auth()->user()->role != 'super_admin'): ?>
                        <th><?php echo app('translator')->get('admin.action'); ?></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $gps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $gp): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                    <tr>
                        <td><?php echo e($gp->id); ?></td>
                        <td class="font-weight-bold"><?php echo e($gp->gp_name); ?></td>
                        <td><?php echo e($gp->district_name); ?></td>
                        <td><?php echo e($gp->block_name); ?></td>
                        <td><?php echo e($gp->zonal_name); ?></td>
                        <td><?php echo e($gp->lgd_code); ?></td>
                        <td><?php echo e($gp->provider); ?></td>
                        <td><?php echo e($gp->contact_no); ?></td>
                        <td><?php echo e($gp->petroller); ?></td>
                        <td><?php echo e($gp->petroller_contact_no); ?></td>

                        <?php if(auth()->user()->role != 'super_admin'): ?>
                        <td>
                            <div class="btn-group" style="width:200px">
                                <?php if( Setting::get('demo_mode') == 0): ?>
                                <form action="<?php echo e(route('admin.gps.destroy', $gp->id)); ?>" method="POST">
                                    <?php echo e(csrf_field()); ?>

                                    <input type="hidden" name="_method" value="DELETE">
                                    <button class="btn btn-danger b-a-radius-0-5 pull-left mr-1" onclick="return confirm('Are you sure you want to delete this GP?')"><i class="fa fa-trash"></i> <?php echo app('translator')->get('admin.delete'); ?></button>
                                </form>
                                <a href="<?php echo e(route('admin.gps.edit', $gp->id)); ?>" class="btn btn-default"><i class="fa fa-pencil"></i> <?php echo app('translator')->get('admin.edit'); ?></a>
                                <?php endif; ?>
                            </div>
                        </td>
                        <?php endif; ?>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>