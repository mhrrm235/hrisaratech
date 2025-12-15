

<?php $__env->startSection('content'); ?>
<div class="page-heading">
<div class="page-content">
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Inventory Usage Logs</h1>
        <a href="<?php echo e(route('inventory-usage-logs.create')); ?>" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
            Log Usage
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

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
                    <th class="border border-gray-300 px-4 py-2 text-left">Item</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Employee</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Borrowed Date</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Returned Date</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Status</th>
                    <th class="border border-gray-300 px-4 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50">
                    <td class="border border-gray-300 px-4 py-2"><?php echo e($log->inventory->name); ?></td>
                    <td class="border border-gray-300 px-4 py-2"><?php echo e($log->employee->fullname); ?></td>
                    <td class="border border-gray-300 px-4 py-2"><?php echo e($log->borrowed_date->format('M d, Y H:i')); ?></td>
                    <td class="border border-gray-300 px-4 py-2">
                        <?php if($log->returned_date): ?>
                            <?php echo e($log->returned_date->format('M d, Y H:i')); ?>

                        <?php else: ?>
                            <span class="text-orange-600 font-bold">Not Returned</span>
                        <?php endif; ?>
                    </td>
                    <td class="border border-gray-300 px-4 py-2 text-center">
                        <?php if($log->returned_date): ?>
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded">Returned</span>
                        <?php else: ?>
                            <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded">Borrowed</span>
                        <?php endif; ?>
                    </td>
                    <td class="border border-gray-300 px-4 py-2 text-center">
                        <a href="<?php echo e(route('inventory-usage-logs.edit', $log)); ?>" class="text-blue-500 hover:text-blue-700 mr-3">Edit</a>
                        <form method="POST" action="<?php echo e(route('inventory-usage-logs.destroy', $log)); ?>" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <?php if($logs->isEmpty()): ?>
    <div class="mt-4 p-4 bg-gray-100 border border-gray-300 text-gray-700 rounded text-center">
        No logs found. <a href="<?php echo e(route('inventory-usage-logs.create')); ?>" class="text-blue-500 hover:text-blue-700">Create one</a>
    </div>
    <?php endif; ?>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hr-app\resources\views/inventory-usage-logs/index.blade.php ENDPATH**/ ?>