<?php $__env->startSection('title', 'Add Document '); ?>

<?php $__env->startSection('content'); ?>

<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">
            <a href="<?php echo e(route('admin.document.index')); ?>" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> <?php echo app('translator')->get('admin.back'); ?></a>

            <h5 style="margin-bottom: 2em;"><?php echo app('translator')->get('admin.document.add_Document'); ?></h5>

            <form class="form-horizontal" action="<?php echo e(route('admin.document.store')); ?>" method="POST" enctype="multipart/form-data" role="form">
                <?php echo e(csrf_field()); ?>

                <div class="form-group row">
                    <label for="name" class="col-xs-12 col-form-label"><?php echo app('translator')->get('admin.document.document_name'); ?></label>
                    <div class="col-xs-10">
                        <input class="form-control" type="text" value="<?php echo e(old('name')); ?>" name="name" required id="name" placeholder="Document Name">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="name" class="col-xs-12 col-form-label"><?php echo app('translator')->get('admin.document.document_type'); ?></label>
                    <div class="col-xs-10">
                        <select name="type">
                            <option value="DRIVER"><?php echo app('translator')->get('admin.document.driver_review'); ?></option>
                            <option value="VEHICLE"><?php echo app('translator')->get('admin.document.vehicle_review'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="zipcode" class="col-xs-12 col-form-label"></label>
                    <div class="col-xs-10">
                        <button type="submit" class="btn btn-primary"><?php echo app('translator')->get('admin.document.add_Document'); ?></button>
                        <a href="<?php echo e(route('admin.document.index')); ?>" class="btn btn-default"><?php echo app('translator')->get('admin.cancel'); ?></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>