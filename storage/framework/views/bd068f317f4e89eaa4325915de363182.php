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
                <h3>Presences</h3>
                <p class="text-subtitle text-muted">Monitor presences data.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item">Presences</li>
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
                    <a href="<?php echo e(route('presences.create')); ?>" class="btn btn-primary mb-3 ms-auto">New Presence</a>
                </div>

                <?php if(session('success')): ?>
                    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($attendance->employee?->fullname ?? 'Unknown Employee'); ?></td>
                                <td><?php echo e(\Carbon\Carbon::parse($attendance->check_in)->format('d, M Y H:i:s')); ?></td>
                                <td><?php echo e(\Carbon\Carbon::parse($attendance->check_out)->format('d, M Y H:i:s')); ?></td>
                                
                                <td>
                                    <?php if($attendance->status == 'leave'): ?>
                                        <span class="text-warning"><?php echo e(ucfirst($attendance->status)); ?></span>
                                    <?php elseif($attendance->status == 'present'): ?>
                                        <span class="text-success"><?php echo e(ucfirst($attendance->status)); ?></span>
                                    <?php elseif($attendance->status == 'absent'): ?>
                                        <span class="text-danger"><?php echo e(ucfirst($attendance->status)); ?></span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if(session('role') == 'HR'): ?>
                                    
                                    <a href="<?php echo e(route('presences.edit', $attendance->id)); ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="<?php echo e(route('presences.destroy', $attendance->id)); ?>" method="POST" style="display:inline;">
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
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hr-app\resources\views/presences/index.blade.php ENDPATH**/ ?>