<?php $__env->startSection('title', 'Providers '); ?>

<?php $__env->startSection('content'); ?>
<style type="text/css">
    table.dataTable thead th {
        background-color: #d9d9d9f5 !important;
        border-bottom: none !important;
    }
    .buttons-html5{
        border-radius: 10px;
/*        margin-right: 6px;*/
    }
    table.display tbody tr:hover td{
        background-color: #f1eeeef5 !important;
    }
    .dataTables_scrollBody thead {
        visibility: hidden;
    }
    select.select-box:not([size]):not([multiple]), input.select-box{
        height: 35px;
    }
</style>
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white"> 
        <form action="<?php echo e(route('admin.searchproviders')); ?>" method="GET">
            <?php echo e(csrf_field()); ?>

            <div class="row">
                   
                <div class="form-group row col-md-8">
                            <label for="email" class="col-xs-6 col-form-label">Search By Name</label>
                            <label for="type" class="col-xs-6 col-form-label">Designation Type</label>
                            <div class="col-xs-6">
                                <input class="form-control select-box" type="text" id="search_name" name="search_name" placeholder="Search with first Name or Lastname">
                            </div>
                            
                            <div class="col-xs-6">
                                <select class="form-control select-box" name="type" id="type">
                                    <option value="">Designation</option>
                                    <option value="1">OFC</option>
                                    <option value="2">FRT</option>
                                    <option value="5">Patroller</option>
                                    <option value="3">Zonal incharge</option>
                                    <option value="4">District incharge</option>
                        </select>
                    </div>
                  </div>
            
                <div class="col-xs-2 mt-2 pt-1">
                    <button type="submit" class="form-control btn btn-primary btn-cstm" onclick="return validate_reqst()">Fetch</button>
                </div>  
            </div>
        </form>
        </div> 

        <div class="box box-block bg-white">
            <?php if(Setting::get('demo_mode') == 1): ?>
        <div class="col-md-12" style="height:50px;color:red;">
                    ** Demo Mode : <?php echo app('translator')->get('admin.demomode'); ?>
                </div>
                <?php endif; ?>
            <h4 class="mb-1">
                <!-- <?php echo app('translator')->get('admin.provides.providers'); ?> -->
                <?php echo app('translator')->get('admin.contacts.contact'); ?>
                <?php if(Setting::get('demo_mode', 0) == 1): ?>
                <span class="pull-right">(*personal information hidden in demo)</span>
                <?php endif; ?>
            </h4>
           <?php if(auth()->user()->role != 'super_admin'): ?>
            <a href="<?php echo e(route('admin.provider.create')); ?>" style="margin-left: 1em;" class="btn btn-primary pull-right btn-cstm"><i class="fa fa-plus"></i><?php echo app('translator')->get('admin.contacts.add_contact'); ?></a>
           <?php endif; ?> 
           <table class="table row-bordered dataTable nowrap display" id="table-5" style="width:100%">
                <thead>
                    <tr>
                        <th><?php echo app('translator')->get('admin.id'); ?></th>
                        <th><?php echo app('translator')->get('admin.provides.full_name'); ?></th>
                        <th>Role</th>
                        <th><?php echo app('translator')->get('admin.email'); ?></th>
                        <th><?php echo app('translator')->get('admin.mobile'); ?></th>
                        <th>Version</th>
                        <th><?php echo app('translator')->get('admin.provides.total_requests'); ?></th>
                        <th><?php echo app('translator')->get('admin.provides.accepted_requests'); ?></th>
                        <th><?php echo app('translator')->get('admin.provides.cancelled_requests'); ?></th> 
                        <th><?php echo app('translator')->get('admin.provides.service_type'); ?></th>
                        <th><?php echo app('translator')->get('admin.provides.online'); ?></th>
                        <?php if(auth()->user()->role != 'super_admin'): ?>
                        <th><?php echo app('translator')->get('admin.action'); ?></th>
                       <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php ($page =0); ?>
                <?php $__currentLoopData = $providers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $provider): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                <?php ($page++); ?>
                    <tr>
                        <td><?php echo e($page); ?></td>
                        <td class="font-weight-bold"><?php echo e($provider->first_name); ?> <?php echo e($provider->last_name); ?></td>
                         <?php if($provider->type == 2): ?>
                        <td>FRT</td>
                        <?php elseif($provider->type == 5): ?>
                        <td>Patroller</td>
                        <?php else: ?>
                        <td></td>
                        <?php endif; ?>

                        <?php if(Setting::get('demo_mode', 0) == 1): ?>
                        <td><?php echo e(substr($provider->email, 0, 3).'****'.substr($provider->email, strpos($provider->email, "@"))); ?></td>
                        <?php else: ?>
                        <td><?php echo e($provider->email); ?></td>
                        <?php endif; ?>
                        <?php if(Setting::get('demo_mode', 0) == 1): ?>
                        <td>+919876543210</td>
                        <?php else: ?>
                        <td><?php echo e($provider->mobile); ?></td>
                        <?php endif; ?>
                        <td><?php echo e($provider->version); ?></td>
                        <td><?php echo e($provider->total_requests()); ?></td>
                        <td><?php echo e($provider->accepted_requests()); ?></td>
                        <td><?php echo e($provider->total_requests() - $provider->accepted_requests()); ?></td> 
                        <td>
                            
                            <?php if($provider->service != null): ?>
                                 <a class="btn btn-success btn-block btn-rounded" href="<?php echo e(route('admin.provider.document.index', $provider->id )); ?>">All Set!</a>
                            <?php else: ?>                               
                                <a class="btn btn-danger btn-block btn-rounded" href="<?php echo e(route('admin.provider.document.index', $provider->id )); ?>">Attention! Service</a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($provider->service): ?>
                                <?php if($provider->service->status == 'active'): ?>
                                    <label class="btn btn-block btn-primary btn-rounded">Yes</label>
                                <?php else: ?>
                                    <label class="btn btn-block btn-warning btn-rounded">No</label>
                                <?php endif; ?>
                            <?php else: ?>
                                <label class="btn btn-block btn-danger btn-rounded">N/A</label>
                            <?php endif; ?>
                        </td>
                            <td>
                            <div class="btn-group" style="width:200px">
                                 <?php if(Auth::guard('admin')->user()->id == 1) {?>
                                <?php if($provider->status == 'approved'): ?>
                                <a class="btn btn-danger  b-a-radius-0-5 pull-left mr-1" href="<?php echo e(route('admin.provider.disapprove', $provider->id )); ?>"><?php echo app('translator')->get('Disable'); ?></a>
                                <?php else: ?>
                                <a class="btn btn-success  b-a-radius-0-5 pull-left mr-1" href="<?php echo e(route('admin.provider.approve', $provider->id )); ?>"><?php echo app('translator')->get('Enable'); ?></a>
                                <?php endif; ?>
                                <?php } ?>
                                <button type="button" 
                                    class="btn btn-info  dropdown-toggle b-a-radius-0-5 pull-left"
                                    data-toggle="dropdown"><?php echo app('translator')->get('admin.action'); ?>
                                    <span class="caret"></span>
                                </button>
                                  <ul class="dropdown-menu">
                                      <?php if(Auth::guard('admin')->user()->id == 1) {?>
                                    <li>
                                        <a href="<?php echo e(route('admin.provider.request', $provider->id)); ?>" class="btn btn-default"><i class="fa fa-search"></i> <?php echo app('translator')->get('admin.History'); ?></a>
                                    </li>
                                    <li>
                                        <a href="<?php echo e(route('admin.provider.statement', $provider->id)); ?>" class="btn btn-default"><i class="fa fa-account"></i> <?php echo app('translator')->get('admin.Statements'); ?></a>
                                    </li>
                                    <?php } ?>
                                    <?php if( Setting::get('demo_mode') == 0): ?>
                                    <li>
                                        <a href="<?php echo e(route('admin.provider.edit', $provider->id)); ?>" class="btn btn-default"><i class="fa fa-pencil"></i> <?php echo app('translator')->get('admin.edit'); ?></a>
                                    </li>
                                    <?php endif; ?>
                                    <?php if(auth()->user()->role != 'super_admin'): ?>
                                     <?php if(Auth::guard('admin')->user()->id == 1) {?>
                                    <li>
                                        <form action="<?php echo e(route('admin.provider.destroy', $provider->id)); ?>" method="POST">
                                            <?php echo e(csrf_field()); ?>

                                            <input type="hidden" name="_method" value="DELETE">
                                            <?php if( Setting::get('demo_mode') == 0): ?>
                                            <button class="btn btn-default look-a-like" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i><?php echo app('translator')->get('admin.delete'); ?></button>
                                            <?php endif; ?>
                                        </form>
                                    </li>
                                  <?php } ?>
                                  <?php endif; ?>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
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

<?php $__env->startSection('scripts'); ?>
<script type="text/javascript">
    /*jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
        if ( this.context.length ) {
            var jsonResult = $.ajax({
                url: "<?php echo e(url('admin/provider')); ?>?page=all",
                data: {},
                success: function (result) {                       
                    p = new Array();
                    $.each(result.data, function (i, d)
                    {
                        var item = [d.id,d.first_name, d.last_name, d.email,d.mobile,d.rating, d.wallet_balance];
                        p.push(item);
                    });
                },
                async: false
            });
            var head=new Array();
            head.push("ID", "First Name", "Last Name", "Email", "Mobile", "Rating", "Wallet");
            return {body: p, header: head};
        }
    } );*/

    $('#table-5').DataTable( {
        scrollX: true,
        searching: true,
        paging:true,
            info:true,
            dom: 'Bfrtip',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ]
    } );
    
    function validate_reqst(){
        var contact_type = document.getElementById('type');
        var type = contact_type.options[contact_type.selectedIndex].value;
        var search_name = document.getElementById('search_name').value;

        if(!type && !search_name){
            document.getElementById('type').style.border = "1px solid red";
            document.getElementById('search_name').style.border = "1px solid red";
            return false;    // in failure case
        }  
        return true;
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>