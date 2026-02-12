<?php $__env->startSection('title', 'Service Types '); ?>

<?php $__env->startSection('content'); ?>
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">
           <?php if(Setting::get('demo_mode') == 1): ?>
        <div class="col-md-12" style="height:50px;color:red;">
                    ** Demo Mode : <?php echo app('translator')->get('admin.demomode'); ?>
                </div>
                <?php endif; ?> 
            <h5 class="mb-1">Service Types</h5>
            <a href="<?php echo e(route('admin.service.create')); ?>" style="margin-left: 1em;" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add New Service</a>
            <table class="table table-striped table-bordered dataTable" id="table-2">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Service Name</th>
                        <!-- <th>Provider Name</th> -->
                        <!-- <th>Capacity</th> -->
                        <!-- <th>Base Price</th>
                        <th>Base Distance</th>
                        <th>Distance Price</th>
                         <th>Time Price</th> -->
                       <!--  <th>Hour Price</th>
                        <th>Price Calculation</th>-->
                        <th>Service Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $service): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><?php echo e($service->name); ?></td>
                        <!-- <td><?php echo e($service->provider_name); ?></td> -->
                        <!-- <td><?php echo e($service->capacity); ?></td> -->
                        <!--<td><?php echo e(currency($service->fixed)); ?></td>
                        <td><?php echo e(distance($service->distance)); ?></td>
                        <td><?php echo e(currency($service->price)); ?></td>-->
                        <!-- <td><?php echo e(currency($service->minute)); ?></td> -->
                        <!-- <?php if($service->calculator == 'DISTANCEHOUR'): ?> 
                       <td><?php echo e(currency($service->hour)); ?></td>
                        <?php else: ?>
                        <td>No Hour Price</td>
                        <?php endif; ?>
                        <td><?php echo app('translator')->get('servicetypes.'.$service->calculator); ?></td>-->
                        <td>
                            <?php if($service->image): ?> 
                                <img src="<?php echo e($service->image); ?>" style="height: 50px" >
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td>
                            <form action="<?php echo e(route('admin.service.destroy', $service->id)); ?>" method="POST">
                                <?php echo e(csrf_field()); ?>

                                <?php echo e(method_field('DELETE')); ?>

                                <?php if( Setting::get('demo_mode') == 0): ?>
                                <a href="<?php echo e(route('admin.service.edit', $service->id)); ?>" class="btn btn-info btn-block">
                                    <i class="fa fa-pencil"></i> Edit
                                </a>
                                <button class="btn btn-danger btn-block" onclick="return confirm('Are you sure?')">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                </tbody>
                <!--<tfoot>
                    <tr>
                        <th>ID</th>
                        <th>Service Name</th>
                        <th>Provider Name</th> -->
                        <!-- <th>Capacity</th> -->
                        <!-- <th>Base Price</th>
                        <th>Base Distance</th>
                        <th>Distance Price</th>
                         <th>Time Price</th> -->
                        <!-- <th>Hour Price</th>
                        <th>Price Calculation</th>
                        <th>Service Image</th>
                        <th>Action</th>
                    </tr>
                </tfoot>-->
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>