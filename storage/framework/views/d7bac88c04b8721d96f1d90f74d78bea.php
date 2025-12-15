

<?php $__env->startSection('content'); ?>
<div class="page-heading">
    <h3>Log Item Usage</h3>
</div>
<div class="page-content">
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Log Item Usage</h1>

    <?php if($errors->any()): ?>
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('inventory-usage-logs.store')); ?>" method="POST" class="bg-white p-6 rounded-lg shadow">
        <?php echo csrf_field(); ?>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="inventory_id" class="block text-gray-700 font-bold mb-2">Item <span class="text-red-500">*</span></label>
                <select name="inventory_id" id="inventory_id" class="form-control" required>
                    <option value="">Select Item</option>
                    <?php $__currentLoopData = $inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($inventory->id); ?>" <?php echo e(old('inventory_id') == $inventory->id ? 'selected' : ''); ?>><?php echo e($inventory->name); ?> (<?php echo e($inventory->category->name); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div>
                <label for="employee_id" class="block text-gray-700 font-bold mb-2">Employee <span class="text-red-500">*</span></label>
                <select name="employee_id" id="employee_id" class="form-control" required>
                    <option value="">Select Employee</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($employee->id); ?>" <?php echo e(old('employee_id') == $employee->id ? 'selected' : ''); ?>><?php echo e($employee->fullname); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div>
                <label for="borrowed_date" class="block text-gray-700 font-bold mb-2">Borrowed Date <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="borrowed_date" id="borrowed_date" class="form-control" value="<?php echo e(old('borrowed_date')); ?>" required>
            </div>

            <div>
                <label for="returned_date" class="block text-gray-700 font-bold mb-2">Returned Date</label>
                <input type="datetime-local" name="returned_date" id="returned_date" class="form-control" value="<?php echo e(old('returned_date')); ?>">
            </div>
        </div>

        <div class="mt-4">
            <label for="notes" class="block text-gray-700 font-bold mb-2">Notes</label>
            <textarea name="notes" id="notes" class="form-control" rows="4"><?php echo e(old('notes')); ?></textarea>
        </div>

        <div class="flex gap-2 mt-6">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded flex-1">
                Create
            </button>
            <a href="<?php echo e(route('inventory-usage-logs.index')); ?>" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded flex-1 text-center">
                Cancel
            </a>
        </div>
    </form>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hr-app\resources\views/inventory-usage-logs/create.blade.php ENDPATH**/ ?>