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
                <h3>Payrolls</h3>
                <p class="text-subtitle text-muted">Manage payroll data.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item">Payrolls</li>
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
                        <a href="<?php echo e(route('payrolls.create')); ?>" class="btn btn-primary mb-3 ms-auto">New Payroll</a>
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
                            <th>Employee</th>
                            <th>Salary</th>
                            <th>Bonuses</th>
                            <th>Deductions</th>
                            <th>Net Salary</th>
                            <th>Pay Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $payrolls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payroll): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <tr>
                                <td><?php echo e($payroll->employee?->fullname ?? 'Unknown Employee'); ?></td>
                                <td><?php echo e('Rp ' . number_format($payroll->salary, 0, ',', '.')); ?></td>
                                <td><?php echo e('Rp ' . number_format($payroll->bonuses ?? 0, 0, ',', '.')); ?></td>
                                <td><?php echo e('Rp ' . number_format($payroll->deductions ?? 0, 0, ',', '.')); ?></td>
                                <td><?php echo e('Rp ' . number_format($payroll->salary + $payroll->bonuses - $payroll->deductions, 0, ',', '.')); ?></td>
                                <td><?php echo e(\Carbon\Carbon::parse($payroll->pay_date)->format('d, M Y')); ?></td>
                                <td>
                                    <a href="<?php echo e(route('payrolls.show', $payroll->id)); ?>" class="btn btn-info btn-sm">Salary Slip</a>
                                    
                                    <?php if(session('role') == 'HR'): ?>
                                        <a href="<?php echo e(route('payrolls.edit', $payroll->id)); ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="<?php echo e(route('payrolls.destroy', $payroll->id)); ?>" method="POST" style="display:inline;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button onclick="return confirm('Sure?');" type="submit" class="btn btn-danger btn-sm">Delete</button>
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
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hr-app\resources\views/payrolls/index.blade.php ENDPATH**/ ?>