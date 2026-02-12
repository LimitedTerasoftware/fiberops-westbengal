<?php $__env->startSection('title', 'Add Service Type '); ?>

<?php $__env->startSection('content'); ?>
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">
            <a href="<?php echo e(route('admin.service.index')); ?>" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> <?php echo app('translator')->get('admin.back'); ?></a>

            <h5 style="margin-bottom: 2em;"><?php echo app('translator')->get('admin.service.Add_Service_Type'); ?></h5>

            <form class="form-horizontal" action="<?php echo e(route('admin.service.store')); ?>" method="POST" enctype="multipart/form-data" role="form">
                <?php echo e(csrf_field()); ?>

                <div class="form-group row">
                    <label for="name" class="col-xs-12 col-form-label"><?php echo app('translator')->get('admin.service.Service_Name'); ?></label>
                    <div class="col-xs-10">
                        <input class="form-control" type="text" value="<?php echo e(old('name')); ?>" name="name" required id="name" placeholder="Service Name">
                    </div>
                </div>

                <!-- <div class="form-group row">
                    <label for="provider_name" class="col-xs-12 col-form-label"><?php echo app('translator')->get('admin.service.Provider_Name'); ?></label>
                    <div class="col-xs-10">
                        <input class="form-control" type="text" value="<?php echo e(old('provider_name')); ?>" name="provider_name" required id="provider_name" placeholder="Provider Name">
                    </div>
                </div> -->

                <div class="form-group row">
                    <label for="picture" class="col-xs-12 col-form-label">
                    <?php echo app('translator')->get('admin.service.Service_Image'); ?></label>
                    <div class="col-xs-10">
                        <input type="file" accept="image/*" name="image" class="dropify form-control-file" id="picture" aria-describedby="fileHelp">
                    </div>
                </div>

                 <!--<div class="form-group row">
                    <label for="calculator" class="col-xs-12 col-form-label"><?php echo app('translator')->get('admin.service.Pricing_Logic'); ?></label>
                    <div class="col-xs-5">
                        <select class="form-control" id="calculator" name="calculator">
                             <option value="MIN"><?php echo app('translator')->get('servicetypes.MIN'); ?></option> 
                             <option value="HOUR"><?php echo app('translator')->get('servicetypes.HOUR'); ?></option> 
                            <option value="DISTANCE"><?php echo app('translator')->get('servicetypes.DISTANCE'); ?></option>
                            <option value="DISTANCEMIN"><?php echo app('translator')->get('servicetypes.DISTANCEMIN'); ?></option> 
                            <option value="DISTANCEHOUR"><?php echo app('translator')->get('servicetypes.DISTANCEHOUR'); ?></option>
                        </select>
                    </div>
                    <div class="col-xs-5">
                        <span class="showcal"><i><b>Price Calculation: <span id="changecal"></span></b></i></span>
                    </div> 
                </div>-->
 
                <!-- Set Hour Price -->
                <!--<div class="form-group row" id="hour_price">
                    <label for="fixed" class="col-xs-12 col-form-label"><?php echo app('translator')->get('admin.service.hourly_Price'); ?> (<?php echo e(currency()); ?>)</label>
                    <div class="col-xs-5">
                        <input class="form-control" type="number" value="<?php echo e(old('fixed')); ?>" name="hour"  id="hourly_price" placeholder="Set Hour Price( Only For DISTANCEHOUR )" min="0">
                    </div>
                    <div class="col-xs-5">
                        <span class="showcal"><i><b>PH (<?php echo app('translator')->get('admin.service.per_hour'); ?>), TH (<?php echo app('translator')->get('admin.service.total_hour'); ?>)</b></i></span>
                    </div>
                </div>-->

                <!-- Base fare -->
                <!--<div class="form-group row">
                    <label for="fixed" class="col-xs-12 col-form-label"><?php echo app('translator')->get('admin.service.Base_Price'); ?> (<?php echo e(currency()); ?>)</label>
                    <div class="col-xs-5">
                        <input class="form-control" type="number" value="<?php echo e(old('fixed')); ?>" name="fixed" required id="fixed" placeholder="Base Price" min="0">
                    </div>
                    <div class="col-xs-5">
                        <span class="showcal"><i><b>BP (<?php echo app('translator')->get('admin.service.Base_Price'); ?>)</b></i></span>
                    </div>
                </div>-->
                <!-- Base distance -->
                <!--<div class="form-group row">
                    <label for="distance" class="col-xs-12 col-form-label"><?php echo app('translator')->get('admin.service.Base_Distance'); ?> (<?php echo e(distance()); ?>)</label>
                    <div class="col-xs-5">
                        <input class="form-control" type="number" value="<?php echo e(old('distance')); ?>" name="distance" required id="distance" placeholder="Base Distance" min="0">
                    </div>
                    <div class="col-xs-5">
                        <span class="showcal"><i><b>BD (<?php echo app('translator')->get('admin.service.Base_Distance'); ?>) </b></i></span>
                    </div>
                </div>-->
                <!-- unit time pricing -->
                <!--<div class="form-group row">
                    <label for="minute" class="col-xs-12 col-form-label"><?php echo app('translator')->get('admin.service.unit_time'); ?></label>
                    <div class="col-xs-5">
                        <input class="form-control" type="number" value="<?php echo e(old('minute')); ?>" name="minute" required id="minute" placeholder="Unit Time Pricing" min="0">
                    </div>
                    <div class="col-xs-5">
                        <span class="showcal"><i><b>PM (<?php echo app('translator')->get('admin.service.per_minute'); ?>), TM(<?php echo app('translator')->get('admin.service.total_minute'); ?>)</b></i></span>
                    </div>
                </div>-->
                <!-- unit distance price -->
                <!--<div class="form-group row">
                    <label for="price" class="col-xs-12 col-form-label"><?php echo app('translator')->get('admin.service.unit'); ?>(<?php echo e(distance()); ?>)</label>
                    <div class="col-xs-5">
                        <input class="form-control" type="number" value="<?php echo e(old('price')); ?>" name="price" required id="price" placeholder="Unit Distance Price" min="0">
                    </div>
                    <div class="col-xs-5">
                        <span class="showcal"><i><b>P<?php echo e(Setting::get('distance')); ?> (<?php echo app('translator')->get('admin.service.per'); ?> <?php echo e(Setting::get('distance')); ?>), T<?php echo e(Setting::get('distance')); ?> (<?php echo app('translator')->get('admin.service.total'); ?> <?php echo e(Setting::get('distance')); ?>)</b></i></span>
                    </div>
                </div>-->

                <!--<div class="form-group row">
                    <label for="capacity" class="col-xs-12 col-form-label"><?php echo app('translator')->get('admin.service.Seat_Capacity'); ?></label>
                    <div class="col-xs-5">
                        <input class="form-control" type="number" value="<?php echo e(old('capacity')); ?>" name="capacity" required id="capacity" placeholder="Capacity" min="1">
                    </div>
                </div>
               

                <div class="form-group row">
                    <label for="description" class="col-xs-12 col-form-label"><?php echo app('translator')->get('admin.service.Description'); ?></label>
                    <div class="col-xs-5">
                        <textarea class="form-control" type="number" value="<?php echo e(old('description')); ?>" name="description" required id="description" placeholder="Description" rows="4"></textarea>
                    </div>
                </div>-->

                <div class="form-group row">
                    <div class="col-xs-10">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-3">
                                <a href="<?php echo e(route('admin.service.index')); ?>" class="btn btn-danger btn-block"><?php echo app('translator')->get('admin.cancel'); ?></a>
                            </div>
                            <div class="col-xs-12 col-sm-6 offset-md-6 col-md-3">
                                <button type="submit" class="btn btn-primary btn-block"><?php echo app('translator')->get('admin.service.Add_Service_Type'
                                ); ?></button>
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
<script type="text/javascript">
    var cal='DISTANCE';
    priceInputs(cal);
    $("#calculator").on('change', function(){       
        cal=$(this).val();
        priceInputs(cal);
    });

    function priceInputs(cal){
        if(cal=='MIN'){
            $("#hourly_price,#distance,#price").attr('value','');
            $("#minute").prop('disabled', false); 
            $("#minute").prop('required', true); 
            $("#hourly_price,#distance,#price").prop('disabled', true);
            $("#hourly_price,#distance,#price").prop('required', false);
            $("#changecal").text('BP + (TM*PM)'); 
        }
        else if(cal=='HOUR'){
            $("#minute,#distance,#price").attr('value',''); 
            $("#hourly_price").prop('disabled', false);
            $("#hourly_price").prop('required', true);
            $("#minute,#distance,#price").prop('disabled', true);
            $("#minute,#distance,#price").prop('required', false);
            $("#changecal").text('BP + (TH*PH)');
        }
        else if(cal=='DISTANCE'){
            $("#minute,#hourly_price").attr('value',''); 
            $("#price,#distance").prop('disabled', false);
            $("#price,#distance").prop('required', true);
            $("#minute,#hourly_price").prop('disabled', true);
            $("#minute,#hourly_price").prop('required', false);
            $("#changecal").text('BP + (T<?php echo e(Setting::get("distance")); ?>-BD*P<?php echo e(Setting::get("distance")); ?>)');
        }
        else if(cal=='DISTANCEMIN'){
            $("#hourly_price").attr('value',''); 
            $("#price,#distance,#minute").prop('disabled', false);
            $("#price,#distance,#minute").prop('required', true);
            $("#hourly_price").prop('disabled', true);
            $("#hourly_price").prop('required', false);
            $("#changecal").text('BP + (T<?php echo e(Setting::get("distance")); ?>-BD*P<?php echo e(Setting::get("distance")); ?>) + (TM*PM)');
        }
        else if(cal=='DISTANCEHOUR'){
            $("#minute").attr('value',''); 
            $("#price,#distance,#hourly_price").prop('disabled', false);
            $("#price,#distance,#hourly_price").prop('required', true);
            $("#minute").prop('disabled', true);
            $("#minute").prop('required', false);
            $("#changecal").text('BP + ((T<?php echo e(Setting::get("distance")); ?>-BD)*P<?php echo e(Setting::get("distance")); ?>) + (TH*PH)');
        }
        else{
            $("#minute,#hourly_price").attr('value',''); 
            $("#price,#distance").prop('disabled', false);
            $("#price,#distance").prop('required', true);
            $("#minute,#hourly_price").prop('disabled', true);
            $("#minute,#hourly_price").prop('required', false);
            $("#changecal").text('BP + (T<?php echo e(Setting::get("distance")); ?>-BD*P<?php echo e(Setting::get("distance")); ?>)');
        }
    }

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>