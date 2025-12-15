

<?php $__env->startSection('content'); ?>
<div class="page-heading">

</div>
<div class="page-content">
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Add Inventory Item</h1>

    <?php if($errors->any()): ?>
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('inventories.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-gray-700 font-bold mb-2">Item Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div>
                <label for="inventory_category_id" class="block text-gray-700 font-bold mb-2">Category</label>
                <select name="inventory_category_id" id="inventory_category_id" class="form-control" required>
                    <option value="">Select Category</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category->id); ?>" <?php echo e(old('inventory_category_id') == $category->id ? 'selected' : ''); ?>><?php echo e($category->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div>
                <label for="quantity" class="block text-gray-700 font-bold mb-2">Quantity</label>
                <input type="number" name="quantity" id="quantity"class="form-control" min="0" required>
            </div>

            <div>
                <label for="status" class="block text-gray-700 font-bold mb-2">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="active" <?php echo e(old('status') == 'active' ? 'selected' : ''); ?>>Active</option>
                    <option value="inactive" <?php echo e(old('status') == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                    <option value="damaged" <?php echo e(old('status') == 'damaged' ? 'selected' : ''); ?>>Damaged</option>
                </select>
            </div>

            <div>
                <label for="location" class="block text-gray-700 font-bold mb-2">Location</label>
                <input type="text" name="location" id="location" class="form-control" value="<?php echo e(old('location')); ?>">
            </div>

            <div>
                <label for="purchase_date" class="block text-gray-700 font-bold mb-2">Purchase Date</label>
                <input type="date" name="purchase_date" id="purchase_date" class="form-control" value="<?php echo e(old('purchase_date')); ?>">
            </div>
        </div>

        <div class="mt-4">
            <label for="description" class="block text-gray-700 font-bold mb-2">Description</label>
            <textarea name="description" id="description" class="form-control" rows="4"><?php echo e(old('description')); ?></textarea>
        </div>

        <div class="flex gap-2 mt-6">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded flex-1">
                Create
            </button>
            <a href="<?php echo e(route('inventories.index')); ?>" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded flex-1 text-center">
                Cancel
            </a>
        </div>
    </form>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hr-app\resources\views/inventories/create.blade.php ENDPATH**/ ?>