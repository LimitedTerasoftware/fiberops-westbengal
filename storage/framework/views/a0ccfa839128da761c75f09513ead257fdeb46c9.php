<?php $__env->startSection('title', 'Add GP '); ?>

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
            <a href="<?php echo e(route('admin.gps.index')); ?>" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> <?php echo app('translator')->get('admin.back'); ?></a>

			<h5 style="margin-bottom: 2em;"><?php echo app('translator')->get('admin.gp.new_gp'); ?></h5>
      <form class="form-horizontal" action="<?php echo e(route('admin.gps.store')); ?>" method="POST" enctype="multipart/form-data" role="form">
            	<?php echo e(csrf_field()); ?>

        <div class="top-cs box-block shadow-gray mb-3">
        	<h5 class="mb-2">GP Details</h5>
        	<div class="box-block pb-0">
        	<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="gp_name" class="col-form-label  ">GP Name
							</label>
							<input class="form-control select-box" type="text" value="<?php echo e(old('gp_name')); ?>" name="gp_name"  id="gp_name" placeholder="GP Name">
						</div>
					</div>
					<?php if(count($districts) > 0): ?>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
						<label for="district" class="col-form-label">Districts</label>
							<select class="form-control select-box" name="district" id="district">
								<option value="">Please Select District</option>
								<?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dist): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
								<option value="<?php echo e($dist->id); ?>"><?php echo e($dist->name); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
								
							</select>
						</div>
					</div>
	        <?php endif; ?>
	        <?php if(count($blocks) > 0): ?>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
						<label for="block" class="col-form-label">Block</label>
							<select class="form-control select-box" name="block" id="block">
								<option value="">Please Select Block</option>
								<?php $__currentLoopData = $blocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
								<option value="<?php echo e($block->id); ?>"><?php echo e($block->name); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
								
							</select>
						</div>
					</div>
	        <?php endif; ?>

                  <?php if(count($zonals) > 0): ?>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
						<label for="district" class="col-form-label">Zonal Manager</label>
							<select class="form-control select-box" name="zone" id="zone">
								<option value="">Please Select zone</option>
								<?php $__currentLoopData = $zonals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zone): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
								<option value="<?php echo e($zone->id); ?>"><?php echo e($zone->Name); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
								
							</select>
						</div>
					</div>
	        <?php endif; ?>

					<?php if(count($providers) > 0): ?>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="provider" class="col-form-label  ">Provider Name
							</label>
							<select class="form-control select-box" name="provider" id="provider" >
								<option value="">Please Select Provider Name</option>
								<?php $__currentLoopData = $providers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provider): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
								<option value="<?php echo e($provider->first_name); ?> <?php echo e($provider->last_name); ?>"><?php echo e($provider->first_name); ?> <?php echo e($provider->last_name); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>									
							</select>
						</div>
					</div>
					<?php endif; ?>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="contact" class="col-form-label  ">Contact
							</label>
							<input class="form-control select-box" type="number" value="<?php echo e(old('contact')); ?>" name="contact"  id="contact" placeholder="Provider Contact">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="lgd_code" class="col-form-label  ">LGD Code
							</label>
							<input class="form-control select-box" type="text" value="<?php echo e(old('lgd_code')); ?>" name="lgd_code"  id="lgd_code" placeholder="LGD Code">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="phase" class="col-form-label  ">Phase
							</label>
							<input class="form-control select-box" type="text" value="<?php echo e(old('phase')); ?>" name="phase"  id="phase" placeholder="Phase">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="latitude" class="col-form-label  ">Latitude
							</label>
							<input class="form-control select-box" type="text" name="latitude" id="latitude" placeholder="Latitude">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="longitude" class="col-form-label  ">Longitude
							</label>
							<input class="form-control select-box" type="text" name="longitude" id="longitude" placeholder="Longitude">
						</div>
					</div>
					<div class="form-group row">
						<label for="zipcode" class="col-xs-12 col-form-label"></label>
						<div class="col-xs-12 mt-2">
							<button type="submit" class="btn btn-primary btn-cstm pull-right "><?php echo app('translator')->get('admin.gp.add_gp'); ?></button>
							<a href="<?php echo e(route('admin.gps.index')); ?>" class="btn btn-default pull-right "><?php echo app('translator')->get('admin.cancel'); ?></a>
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
<script>
$("select[id='district']").change(function(){
  var district = $(this).val();
  $.get('<?php echo e(url("admin/ajax-blocks-id")); ?>/'+district,function(data) {
    $("#block").empty().append(data);      
  });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>