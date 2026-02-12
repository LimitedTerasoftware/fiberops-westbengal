<?php $__env->startSection('title', 'Add Block '); ?>

<?php $__env->startSection('content'); ?>
<style type="text/css">
	.shadow-gray {
    box-shadow: 0 0 5px 1px #3333332e !important;
	}
	.col-form-label{
		font-size: 13px !important;
		font-weight: 600;
	}
	.p-2-5{
		padding:2.5rem;
	}
</style>
<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
            <a href="<?php echo e(route('admin.location.block')); ?>" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> <?php echo app('translator')->get('admin.back'); ?></a>

			<h5 style="margin-bottom: 2em;"><?php echo app('translator')->get('admin.location.new_block'); ?></h5>

      <form class="form-horizontal" action="<?php echo e(route('admin.location.block.store')); ?>" method="POST" enctype="multipart/form-data" role="form">
            	<?php echo e(csrf_field()); ?>

        <div class="top-cs box-block shadow-gray">
        	<h5 class="mb-2">Block Details</h5>
        	<div class="box-block">
					<div class="form-group row">
						<div class="col-sm-12 col-md-10">
							<label for="block_name" class="col-form-label "><?php echo app('translator')->get('admin.location.block_name'); ?>
							<span class="look-a-like">*</span></label>
							<input class="form-control select-box" type="text" value="<?php echo e(old('block_name')); ?>" name="block_name" required id="block_name" placeholder="Block Name">
						</div>
					</div>
					<?php if(count($districts) > 0): ?>
						<div class="form-group row">
							<label for="district" class="col-xs-12 col-form-label">Districts
							<span class="look-a-like">*</span></label></label>
							<div class="col-xs-10">
								<select class="form-control select-box" name="district" id="district" required>
									<option value="">Please Select District</option>
									<?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dist): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
									<option value="<?php echo e($dist->id); ?>"><?php echo e($dist->name); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
									
								</select>
							</div>
						</div>
			        <?php endif; ?>
                                        <div class="form-group row">
						<div class="col-sm-12 col-md-10">
							<label for="m_s_code" class="col-form-label ">Ms Code							<span class="look-a-like">*</span></label>
							<input class="form-control select-box" type="text" value="<?php echo e(old('m_s_code')); ?>" name="m_s_code" required id="m_s_code" placeholder="MS Code">
						</div>
					</div>


					<div class="form-group row">
						<label for="zipcode" class="col-xs-12 col-form-label"></label>
						<div class="col-xs-12 mt-2">
							<button type="submit" class="btn btn-primary btn-cstm pull-right "><?php echo app('translator')->get('admin.location.add_block'); ?></button>
							<a href="<?php echo e(route('admin.location.block')); ?>" class="btn btn-default pull-right "><?php echo app('translator')->get('admin.cancel'); ?></a>
						</div>
					</div>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>