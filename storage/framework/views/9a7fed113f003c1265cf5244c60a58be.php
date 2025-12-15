<?php $__env->startSection('content'); ?>

<header class="mb-3">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Leave Requests</h3>
                <p class="text-subtitle text-muted">Manage leave data.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item">Leave Requests</li>
                        <li class="breadcrumb-item active" aria-current="page">Index</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
    <section class="section">
        <div class="card">
            
            <div class="card-body">

                <div class="d-flex">
                    <a href="<?php echo e(route('leave-requests.create')); ?>" class="btn btn-primary mb-3 ms-auto">New Leave Request</a>
                </div>

                <?php if(session('success')): ?>
                    <div class="alert alert-success">
                        <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>
                
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $leaveRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leaveRequest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($leaveRequest->employee?->fullname ?? 'Unknown Employee'); ?></td>
                                <td><?php echo e($leaveRequest->leave_type); ?></td>
                                <td>
                                    <?php if($leaveRequest->status == 'pending'): ?>
                                        <span class="text-warning"><?php echo e(ucfirst($leaveRequest->status)); ?></span>
                                    <?php elseif($leaveRequest->status == 'confirmed'): ?>
                                        <span class="text-success"><?php echo e(ucfirst($leaveRequest->status)); ?></span>
                                    <?php elseif($leaveRequest->status == 'rejected'): ?>
                                        <span class="text-danger"><?php echo e(ucfirst($leaveRequest->status)); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e(\Carbon\Carbon::parse($leaveRequest->start_date)->format('d, M Y')); ?></td>
                                <td><?php echo e(\Carbon\Carbon::parse($leaveRequest->end_date)->format('d, M Y')); ?></td>
                                <td>
                                    <?php if(session('role') == 'HR'): ?>
                                        <?php if(in_array($leaveRequest->status, ['pending', 'rejected'])): ?>
                                            <a onclick="return confirm('Sure?');" href="<?php echo e(route('leave-requests.confirm', $leaveRequest->id)); ?>" class="btn btn-success btn-sm">Confirm</a>
                                        <?php else: ?> 
                                            <a onclick="return confirm('Sure?');" href="<?php echo e(route('leave-requests.reject', $leaveRequest->id)); ?>" class="btn btn-warning btn-sm">Reject</a>
                                        <?php endif; ?>

                                        <a href="<?php echo e(route('leave-requests.edit', $leaveRequest->id)); ?>" class="btn btn-info btn-sm">Edit</a>
                                        <form action="<?php echo e(route('leave-requests.destroy', $leaveRequest->id)); ?>" method="POST" style="display:inline-block;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hr-app\resources\views/leave_requests/index.blade.php ENDPATH**/ ?>