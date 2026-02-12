<?php $__env->startSection('title', 'Scheduled Tickets '); ?>

<?php $__env->startSection('content'); ?>

    <div class="content-area py-1">
        <div class="container-fluid">
            
            <div class="box box-block bg-white">
                <h5 class="mb-1">Scheduled Tickets</h5>
                <?php if(count($requests) != 0): ?>
                <table class="table table-striped table-bordered dataTable" id="table-2">
                    <thead>
                        <tr>
                            <th><?php echo app('translator')->get('admin.id'); ?></th>
                            <th><?php echo app('translator')->get('admin.request.Request_Id'); ?></th>
                            <th><?php echo app('translator')->get('admin.request.User_Name'); ?></th>
                            <th><?php echo app('translator')->get('admin.request.Provider_Name'); ?></th>
                            <th><?php echo app('translator')->get('admin.request.Scheduled_Date_Time'); ?></th>
                            <th><?php echo app('translator')->get('admin.status'); ?></th>
                            <th><?php echo app('translator')->get('admin.action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $request): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>

                            <td><?php echo e($request->booking_id); ?></td>
                            <td><?php echo e($request->user?$request->user->first_name:''); ?> <?php echo e($request->user?$request->user->last_name:''); ?></td>
                            <td>
                                <?php if($request->provider_id): ?>
                                    <?php echo e($request->provider?$request->provider->first_name:''); ?> <?php echo e($request->provider?$request->provider->last_name:''); ?>

                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($request->schedule_at); ?></td>
                            <td>
                                <?php echo e($request->status); ?>

                            </td>
                            <td>
                                <div class="input-group-btn">
                                  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">Action
                                    <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu">
                                    <li>
                                        <a href="<?php echo e(route('admin.requests.show', $request->id)); ?>" class="btn btn-default"><i class="fa fa-search"></i> More Details</a>
                                    </li>
                                  </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                    </tbody>
                    <!--<tfoot>
                        <tr>
                            <th><?php echo app('translator')->get('admin.id'); ?></th>
                            <th><?php echo app('translator')->get('admin.request.Request_Id'); ?></th>
                            <th><?php echo app('translator')->get('admin.request.User_Name'); ?></th>
                            <th><?php echo app('translator')->get('admin.request.Provider_Name'); ?></th>
                            <th><?php echo app('translator')->get('admin.request.Scheduled_Date_Time'); ?></th>
                            <th><?php echo app('translator')->get('admin.status'); ?></th>
                            <th><?php echo app('translator')->get('admin.request.Payment_Mode'); ?></th>
                            <th><?php echo app('translator')->get('admin.request.Payment_Status'); ?></th>
                            <th><?php echo app('translator')->get('admin.action'); ?></th>
                        </tr>
                    </tfoot>-->
                </table>
                <?php else: ?>
                    <h6 class="no-result">No results found</h6>
                <?php endif; ?> 
            </div>
            
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>