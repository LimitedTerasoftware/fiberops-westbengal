<?php $__env->startSection('title', 'Today Attendance Report'); ?>

<?php $__env->startSection('content'); ?>

<div class="content-area py-1"> 
        <div class="box box-block bg-white">
            <?php if(Setting::get('demo_mode') == 1): ?>
        <div class="col-md-12" style="height:50px;color:red;">
                    ** Demo Mode : <?php echo app('translator')->get('admin.demomode'); ?>
                </div>
                <?php endif; ?>
            <h5 class="mb-1" style="color:#0275d8;">
			 Today Attendance Report
                <?php if(Setting::get('demo_mode', 0) == 1): ?>
                <span class="pull-right">(*personal information hidden in demo)</span>
                <?php endif; ?>
            </h5>
            <table class="table table-striped table-bordered dataTable" id="table-4">
                <thead>
                    <tr>
                        <th>Total No of Users</th>
                        <th>Total No of Users Logged in</th>
                        <th>Total No of Users Not logged</th>
                    </tr>
                </thead>
                <tbody>		
                     <tr>
                       <td><a href="<?php echo e(route('admin.provider.index')); ?>" target="_blank"><?php echo e($totalusers); ?></a></td> 
                       <td><a href="<?php echo e(route('admin.attendance')); ?>" target="_blank"><?php echo e($loggedinusers); ?></a></td>
                       <td><a href="<?php echo e(route('admin.provider.index')); ?>?status=offline" target="_blank"><?php echo e($totalusers - $loggedinusers); ?></a></td> 			
                    </tr>
                </tbody>
                <!--<tfoot>
                    <tr>
                        <th><?php echo app('translator')->get('admin.id'); ?></th>
                        <th><?php echo app('translator')->get('admin.provides.full_name'); ?></th>
                        <th><?php echo app('translator')->get('admin.email'); ?></th>
                        <th><?php echo app('translator')->get('admin.mobile'); ?></th>
                        <th><?php echo app('translator')->get('admin.provides.total_requests'); ?></th>
                        <th><?php echo app('translator')->get('admin.provides.accepted_requests'); ?></th>
                        <th><?php echo app('translator')->get('admin.provides.cancelled_requests'); ?></th> 
                        <th><?php echo app('translator')->get('admin.provides.service_type'); ?></th>
                        <th><?php echo app('translator')->get('admin.provides.online'); ?></th>
                        <th><?php echo app('translator')->get('admin.action'); ?></th>
                    </tr>
                </tfoot>-->
            </table>
           
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layout.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>