<?php $__env->startSection('title', 'Map View '); ?>

<?php $__env->startSection('content'); ?>

<div class="content-area py-1">
        <div class="container-fluid ">
        	<div class="box box-block bg-white"> 
        <form action="<?php echo e(route('admin.trackattendance')); ?>" method="GET">
            <div class="row">
                <div class="col-xs-2">
                   <select class="form-control selectpicker" data-show-subtext="true" data-live-search="true" name="district_id" id="district" required>
                   	<option value="">Please Select District</option>
                    <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                    <option value="<?php echo e($district->id); ?>" <?php if(Request::get('district_id')): ?> <?php if(@Request::get('district_id') == $district->id): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e($district->name); ?> </option> 
                   <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?> 
                  </select>
                </div>
                    <?php if(count($blocks) > 0): ?>
                  <div class="col-xs-2">
                   <select class="form-control selectpicker" data-show-subtext="true" data-live-search="true" name="block_id" id="block" required>
                   	<option value="">Please Select Block</option>
                    <?php $__currentLoopData = $blocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                    <option value="<?php echo e($block->id); ?>" <?php if(Request::get('block_id')): ?> <?php if(@Request::get('block_id') == $block->id): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e($block->name); ?> </option> 
                   <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?> 
                  </select>
                </div>
                <?php endif; ?>
 
				
				
				 <div class="col-xs-2">
                   <select class="form-control selectpicker" data-show-subtext="true" data-live-search="true" name="provider_id" id="person" required>
                   	<option value="">Please Select Person</option>
                    <?php $__currentLoopData = $providers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provider): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                    <option value="<?php echo e($provider->id); ?>" <?php if(Request::get('provider_id')): ?> <?php if(@Request::get('provider_id') == $provider->id): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e($provider->first_name); ?> <?php echo e($provider->last_name); ?></option> 
                   <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?> 
                  </select>
                </div>
				
				<div class="form-group row col-md-3">
                            <label for="name" class="col-xs-4 col-form-label">Date From</label>
                            <div class="col-xs-8">
                                <input class="form-control" type="date" name="from_date" required placeholder="From Date">
                            </div>
                </div>
                            
                <div class="form-group row col-md-3">
                            <label for="email" class="col-xs-4 col-form-label">Date To</label>
                            <div class="col-xs-8">
                                <input class="form-control" type="date" required name="to_date" placeholder="To Date">
                            </div>
                  </div>
                
                <div class="col-xs-2">
                    <button type="submit" class="form-control btn btn-primary">Fetch</button>
                </div>  
            </div>
        </form>
        </div>       
            <div class="box box-block bg-white">
		     <?php if((Request::get('from_date') != '') && (Request::get('to_date') != '')): ?>
            <h5 class="mb-1" style="color:#0275d8;"> Attendance Map View From <?php echo e(Request::get('from_date')); ?> To <?php echo e(Request::get('to_date')); ?></h5>
		     <?php else: ?>
			 <h5 class="mb-1" style="color:#0275d8;">Today Attendance Map View</h5>	 
			 <?php endif; ?>
            <div class="row">
            	 <input type="hidden" name="districtdata" value="<?php echo e($district_id); ?>" id="districtdata" />
                 <input type="hidden" name="blockdata" value="<?php echo e($block_id); ?>" id="blockdata" />
  
				 <input type="hidden" name="fromdate" value="<?php echo e($from_date); ?>" id="fromdate" />
				 <input type="hidden" name="todate" value="<?php echo e($to_date); ?>" id="todate" />
				 <input type="hidden" name="providerid" value="<?php echo e($provider_id); ?>" id="providerid" />
				 
                <div class="col-xs-12">
                    <div id="map"></div>
                    <div id="legend"><h3>Note: </h3></div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style type="text/css">
    #map {
        height: 100%;
        min-height: 500px;
    }
    
    #legend {
        font-family: Arial, sans-serif;
        background: rgba(255,255,255,0.8);
        padding: 10px;
        margin: 10px;
        border: 2px solid #f3f3f3;
    }

    #legend h3 {
        margin-top: 0;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
    }

    #legend img {
        vertical-align: middle;
        margin-bottom: 5px;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
$("select[id='district']").change(function(){
  var district = $(this).val();
  $.get('<?php echo e(url("admin/ajax-blocks-id")); ?>/'+district,function(data) {
    $("#block").empty().append(data);      
  });
});

$("select[id='block']").change(function(){
  var block= $(this).val();
  $.get('<?php echo e(url("admin/ajax-blockwise-providers")); ?>/'+block,function(data) {
    $("#person").empty().append(data);      
  });
});

</script>
<script>
    var map;
    var users;
    var districtdata = document.getElementById('districtdata').value;
    var blockdata = document.getElementById('blockdata').value;
    var providers;
    var ajaxMarkers = [];
    var googleMarkers = [];
    var mapIcons = {
        user: '<?php echo e(asset("asset/img/marker-user.png")); ?>',
        active: '<?php echo e(asset("asset/img/marker-user.png")); ?>',
        riding: '<?php echo e(asset("asset/img/marker-user.png")); ?>',
        offline: '<?php echo e(asset("asset/img/map-marker-red.png")); ?>',
        unactivated: '<?php echo e(asset("asset/img/marker-plus.png")); ?>'
    }

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 20.8444, lng: 85.1511},
            zoom: 8,
            minZoom: 1
        });

        setInterval(ajaxMapData, 3000);

        var legend = document.getElementById('legend');
       


        // var div = document.createElement('div');
        // div.innerHTML = '<img src="' + mapIcons['user'] + '"> ' + 'User';
        // legend.appendChild(div);

        var div = document.createElement('div');
        div.innerHTML = '<img src="' + mapIcons['offline'] + '"> ' + 'offline';
        legend.appendChild(div);
        
        var div = document.createElement('div');
        div.innerHTML = '<img src="' + mapIcons['active'] + '"> ' + 'online';
        legend.appendChild(div);
        
        //var div = document.createElement('div');
       // div.innerHTML = '<img src="' + mapIcons['unactivated'] + '"> ' + 'Unactivated';
        //legend.appendChild(div);

        map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legend);
        
        google.maps.Map.prototype.clearOverlays = function() {
            for (var i = 0; i < googleMarkers.length; i++ ) {
                googleMarkers[i].setMap(null);
            }
            googleMarkers.length = 0;
        }

    }

    function ajaxMapData() {
        map.clearOverlays();
        $.ajax({
            url: 'https://fleet.terasoftware.com/public/westbengal/public/admin/map/ajax?district_id='+ districtdata+'&block_id='+blockdata+'&provider_id='+providerid +'&from_date='+fromdate+'&to_date='+todate,
            dataType: "JSON",
            headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken },
            type: "GET",
            success: function(data) {
                console.log('Ajax Response', data);
                ajaxMarkers = data;
            }
        });

        ajaxMarkers ? ajaxMarkers.forEach(addMarkerToMap) : '';
    }

    function addMarkerToMap(element, index) {
    	//var baddress = element.service.address?element.service.address:(element.latitude + "," + element.longitude);
        var baddress = (element.service && element.service.address) 
        ? element.service.address 
        : (element.latitude + "," + element.longitude);
        marker = new google.maps.Marker({
            position: {
                lat: element.latitude,
                lng: element.longitude
            },
            id: element.id,
            map: map,
            title: element.first_name + " " +element.last_name + "\n" + baddress,
            icon : mapIcons[element.service ? element.service.status : element.status],
        });

        googleMarkers.push(marker);

        google.maps.event.addListener(marker, 'click', function() {
            //window.location.href = '/admin/' + element.service ? 'provider' : 'user' + '/' +element.user_id;
            window.location.href = 'https://fleet.terasoftware.com/public/westbengal/public/admin/currentlocation/'+element.id;
        });
    }
</script>
<script src="//maps.googleapis.com/maps/api/js?key=<?php echo e(Setting::get('map_key')); ?>&libraries=places&callback=initMap" async defer></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>