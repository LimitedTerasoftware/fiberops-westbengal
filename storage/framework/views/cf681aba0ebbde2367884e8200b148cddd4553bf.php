<?php $__env->startSection('title', 'Teams Reports '); ?>

<?php $__env->startSection('content'); ?>
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">

              <form action="<?php echo e(route('admin.teams_status')); ?>" method="GET">
            <ul class="nav nav-pills mb-2 pb-1 b-b">
                 
                  <li class="nav-item mr-0-75">
                    <input class="form-control filter-box filter" type="date" id="from_date" name="from_date" placeholder="From Date" value="<?php echo e(@Request::get('from_date')); ?>"  onclick="this.showPicker()">
                
                <li class="nav-item mr-0-75">
                    <input class="form-control filter-box filter" type="date" id="to_date" name="to_date" placeholder="To Date" value="<?php echo e(@Request::get('to_date')); ?>"  onclick="this.showPicker()">
                </li>

                <li class="nav-item mr-0-75 pull-right mt">
                    <button type="submit" class="form-control btn btn-primary btn-cstm" style="height:30px">Apply</button>
                </li>
            </ul>
            </form>



           <?php if(Setting::get('demo_mode') == 1): ?>
        <div class="col-md-12" style="height:50px;color:red;">
                    ** Demo Mode : <?php echo app('translator')->get('admin.demomode'); ?>
                </div>
                <?php endif; ?>
            <h5 class="mb-1">
                 Teams Report
                <?php if(Setting::get('demo_mode', 0) == 1): ?>
                <span class="pull-right">(*personal information hidden in demo)</span>
                <?php endif; ?>               
            </h5>
            <table class="table table-striped table-bordered dataTable" id="table-5">
    <thead>
        <tr>
            <th><?php echo app('translator')->get('admin.id'); ?></th>
            <th>Zonal</th>
            <th>District</th>
            <th>Team</th>
            <th>Name</th>
            <th>Total Tickets</th>
            <th>Auto Close</th>
            <th>Manual Close</th>
            <th>Hold</th>
            <th>OnGoing</th>
            <th>NotStarted</th>
            <th>>24Hr</th>
        </tr>
    </thead>
    <tbody>
        <?php 
            $page = 0;
            $totalTickets = 0;
            $totalCompletedAuto = 0;
            $totalCompletedManual = 0;
            $totalHold = 0;
            $totalOngoing = 0;
            $totalPending = 0;
            $pending24hr = 0;
         ?>
        <?php $__currentLoopData = $teams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $user): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
        <?php 
            $page++;
            $totalTickets += $user->total_tickets;
            $totalCompletedAuto += $user->completed_tickets_auto;
            $totalCompletedManual += $user->completed_tickets_manual;
            $totalHold += $user->hold_tickets;
            $totalOngoing += $user->pickup_tickets;
            $totalPending += $user->pending_tickets;
            $pending24hr += $user->pending_tickets_morethen_24;
         ?>
        <tr>
            <td><?php echo e($page); ?></td>
            <td><?php echo e($user->zone_name); ?></td>
            <td><?php echo e($user->district); ?></td>
            <td><?php echo e($user->team_name); ?></td>
            <td><?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?></td>
            <td><?php echo e($user->total_tickets); ?></td>
            <td><a href="/public/westbengal/public/admin/tickets?zone_id=<?php echo e($user->zone_id); ?>&team_id=<?php echo e($user->team_id); ?>&status=Completed&autoclose=Auto&newfrom_date=<?php echo e($fromDate); ?>&newto_date=<?php echo e($toDate); ?>"><?php echo e($user->completed_tickets_auto); ?></a></td>
            <td><a href="/public/westbengal/public/admin/tickets?zone_id=<?php echo e($user->zone_id); ?>&team_id=<?php echo e($user->team_id); ?>&status=Completed&autoclose=Manual&newfrom_date=<?php echo e($fromDate); ?>&newto_date=<?php echo e($toDate); ?>"><?php echo e($user->completed_tickets_manual); ?></a></td>
            <td><a href="/public/westbengal/public/admin/tickets?zone_id=<?php echo e($user->zone_id); ?>&team_id=<?php echo e($user->team_id); ?>&status=Onhold&newfrom_date=<?php echo e($fromDate); ?>&newto_date=<?php echo e($toDate); ?>"><?php echo e($user->hold_tickets); ?></a></td>
            <td><a href="/public/westbengal/public/admin/tickets?zone_id=<?php echo e($user->zone_id); ?>&team_id=<?php echo e($user->team_id); ?>&status=OnGoing"><?php echo e($user->pickup_tickets); ?></a></td>
            <td>
              <?php if(request()->has('from_date') && request()->has('to_date')): ?>
            <a href="/public/westbengal/public/admin/tickets?zone_id=<?php echo e($user->zone_id); ?>&team_id=<?php echo e($user->team_id); ?>&status=NotStarted&from_date=<?php echo e($fromDate); ?>&to_date=<?php echo e($toDate); ?>">
                <?php echo e($user->pending_tickets); ?>

            </a>
        <?php else: ?>
            <a href="/public/westbengal/public/admin/tickets?zone_id=<?php echo e($user->zone_id); ?>&team_id=<?php echo e($user->team_id); ?>&status=NotStarted">
                <?php echo e($user->pending_tickets); ?>

            </a>
        <?php endif; ?>
            </td>
            <td>
              <?php if(request()->has('from_date') && request()->has('to_date')): ?>
            <a href="/public/westbengal/public/admin/tickets?zone_id=<?php echo e($user->zone_id); ?>&team_id=<?php echo e($user->team_id); ?>&status=NotStarted&from_date=<?php echo e($fromDate); ?>&to_date=<?php echo e($toDate); ?>&range=24hr">
                <?php echo e($user->pending_tickets_morethen_24); ?>

            </a>
        <?php else: ?>
            <a href="/public/westbengal/public/admin/tickets?zone_id=<?php echo e($user->zone_id); ?>&team_id=<?php echo e($user->team_id); ?>&status=NotStarted&range=24hr">
                <?php echo e($user->pending_tickets_morethen_24); ?>

            </a>
        <?php endif; ?>
            </td>

        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5">Total</th>
            <th><?php echo e($totalTickets); ?></th>
            <th><?php echo e($totalCompletedAuto); ?></th>
            <th><?php echo e($totalCompletedManual); ?></th>
            <th><?php echo e($totalHold); ?></th>
            <th><?php echo e($totalOngoing); ?></th>
            <th><?php echo e($totalPending); ?></th>
            <th><?php echo e($pending24hr); ?></th>

        </tr>
    </tfoot>
</table>
  </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script type="text/javascript">
       $('#table-5').DataTable( {
        responsive: true,
        paging:false,
            info:false,
            dom: 'Bfrtip',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ]
    } );
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>