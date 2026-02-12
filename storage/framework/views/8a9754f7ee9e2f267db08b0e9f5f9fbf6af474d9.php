<?php $__env->startSection('title', 'Documents '); ?>

<?php $__env->startSection('content'); ?>

    <div class="content-area py-1">
        <div class="container-fluid">
            
            <div class="box box-block bg-white">
             <?php if(Setting::get('demo_mode') == 1): ?>
                <div class="col-md-12" style="height:50px;color:red;">
                    ** Demo Mode : <?php echo app('translator')->get('admin.demomode'); ?>
                </div>
             <?php endif; ?>
                <h5 class="mb-1"><?php echo app('translator')->get('admin.document.document'); ?></h5>
                <a href="<?php echo e(route('admin.document.create')); ?>" style="margin-left: 1em;" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> <?php echo app('translator')->get('admin.document.add_Document'); ?></a>
                <table class="table table-striped table-bordered dataTable" id="table-2">
                    <thead>
                        <tr>
                            <th><?php echo app('translator')->get('admin.id'); ?></th>
                            <th><?php echo app('translator')->get('admin.document.document_name'); ?></th>
                            <th><?php echo app('translator')->get('admin.type'); ?></th>
                            <th><?php echo app('translator')->get('admin.action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $document): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><?php echo e($document->name); ?></td>
                            <td><?php echo e($document->type); ?></td>
                            <td>
                                <form action="<?php echo e(route('admin.document.destroy', $document->id)); ?>" method="POST">
                                    <?php echo e(csrf_field()); ?>

                                    <input type="hidden" name="_method" value="DELETE">
                                    <?php if( Setting::get('demo_mode') == 0): ?>
                                    <a href="<?php echo e(route('admin.document.edit', $document->id)); ?>" class="btn btn-info"><i class="fa fa-pencil"></i> <?php echo app('translator')->get('admin.edit'); ?></a>
                                    <button class="btn btn-danger" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i> <?php echo app('translator')->get('admin.delete'); ?></button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                    </tbody>
                    <!--<tfoot>
                        <tr>
                            <th><?php echo app('translator')->get('admin.id'); ?></th>
                            <th><?php echo app('translator')->get('admin.document.document_name'); ?></th>
                            <th><?php echo app('translator')->get('admin.type'); ?></th>
                            <th><?php echo app('translator')->get('admin.action'); ?></th>
                        </tr>
                    </tfoot>-->
                </table>
            </div>
            
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>