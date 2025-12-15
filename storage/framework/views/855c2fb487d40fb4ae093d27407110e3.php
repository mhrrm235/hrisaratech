

<?php $__env->startSection('content'); ?>
<?php
    $userRole = Auth::user()->employee->role->title ?? null;
    $isHROrPowerUser = in_array($userRole, ['HR', 'Power User']);
    $pendingCount = $isHROrPowerUser ? \App\Models\Letter::where('status', 'pending')->count() : 0;
?>
<div class="page-heading">
    <h3>Letters
        <?php if($isHROrPowerUser && $pendingCount > 0): ?>
            <span class="badge bg-warning"><?php echo e($pendingCount); ?> Pending</span>
        <?php endif; ?>
    </h3>
</div>
<div class="page-content">
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="<?php echo e(route('letters.create')); ?>" class="btn btn-primary">+ Create Letter</a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Letter Number</th>
                        <th>Subject</th>
                        <th>From</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $letters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $letter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($letter->letter_number ?? 'Draft'); ?></td>
                        <td><?php echo e($letter->subject); ?></td>
                        <td><?php echo e($letter->user->name); ?></td>
                        <td>
                            <span class="badge bg-<?php echo e($letter->status === 'draft' ? 'secondary' : ($letter->status === 'pending' ? 'warning' : ($letter->status === 'approved' ? 'success' : 'info'))); ?>">
                                <?php echo e(ucfirst($letter->status)); ?>

                            </span>
                        </td>
                        <td><?php echo e($letter->created_date->format('d M Y')); ?></td>
                        <td>
                            <a href="<?php echo e(route('letters.show', $letter)); ?>" class="btn btn-sm btn-info">View</a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center">No letters found. <a href="<?php echo e(route('letters.create')); ?>">Create one</a></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hr-app\resources\views/letters/index.blade.php ENDPATH**/ ?>