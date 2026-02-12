<?php $__env->startSection('title', 'Unassigned Permissions '); ?>

<?php $__env->startSection('content'); ?>
<div class="content-area py-1">
	<div class="container-fluid">
    	<div class="box box-block bg-white">
    		<p>Permissions are not allowed</p>
    	</div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>