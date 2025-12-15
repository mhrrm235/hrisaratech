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
                <h3>Tasks</h3>
                <p class="text-subtitle text-muted">Manage tasks data.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item">Tasks</li>
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
                    <?php if(session('role') == 'HR'): ?>
                        <a href="<?php echo e(route('tasks.create')); ?>" class="btn btn-primary mb-3 ms-auto">New Task</a>
                    <?php endif; ?>
                </div>

                <?php if(session('success')): ?>
                    <div class="alert alert-success">
                        <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>

                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Assigned To</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($task->title); ?></td>
                                <td><?php echo e($task->employee?->fullname ?? 'Unknown Employee'); ?></td>
                                <td><?php echo e(\Carbon\Carbon::parse($task->due_date)->format('d F Y')); ?></td>
                                <td>
                                    <?php if($task->status == 'pending'): ?>
                                        <span class="text-warning">Pending</span>
                                    <?php elseif($task->status == 'done'): ?>
                                        <span class="text-success">Done</span>
                                    <?php elseif($task->status == 'on progress'): ?>
                                        <span class="text-info">On Progress</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a class="btn btn-info btn-sm" href="<?php echo e(route('tasks.show', $task->id)); ?>">View</a>
                                    
                                    <?php if($task->status == 'pending'): ?>
                                        <a class="btn btn-success btn-sm" href="<?php echo e(route('tasks.done', $task->id)); ?>">Mark as Done</a>
                                    <?php else: ?> 
                                        <a class="btn btn-warning btn-sm" href="<?php echo e(route('tasks.pending', $task->id)); ?>">Mark as Pending</a>
                                    <?php endif; ?>
                                    
                                    <?php if(session('role') == 'HR'): ?>
                                        <a class="btn btn-warning btn-sm" href="<?php echo e(route('tasks.edit', $task->id)); ?>">Edit</a>
                                        <form action="<?php echo e(route('tasks.destroy', $task->id)); ?>" method="POST" style="display:inline;">
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
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hr-app\resources\views/tasks/index.blade.php ENDPATH**/ ?>