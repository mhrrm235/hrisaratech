

<?php $__env->startSection('content'); ?>
<header class="mb-3">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>

<div class="page-heading">
    <h3>Inventories</h3>
</div>
<div class="page-content">
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Inventories</h1>
        <a href="<?php echo e(route('inventories.create')); ?>" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
            Add Item
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="overflow-x-auto">
        <table class="w-full bg-white border border-gray-300 rounded-lg">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-gray-300 px-4 py-2 text-left">Name</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Category</th>
                    <th class="border border-gray-300 px-4 py-2 text-center">Quantity</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Location</th>
                    <th class="border border-gray-300 px-4 py-2 text-center">Status</th>
                    <th class="border border-gray-300 px-4 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50">
                    <td class="border border-gray-300 px-4 py-2"><?php echo e($inventory->name); ?></td>
                    <td class="border border-gray-300 px-4 py-2"><?php echo e($inventory->category->name); ?></td>
                    <td class="border border-gray-300 px-4 py-2 text-center"><?php echo e($inventory->quantity); ?></td>
                    <td class="border border-gray-300 px-4 py-2"><?php echo e($inventory->location); ?></td>
                    <td class="border border-gray-300 px-4 py-2 text-center">
                        <span class="px-2 py-1 rounded text-white 
                            <?php if($inventory->status === 'active'): ?> bg-green-500 
                            <?php elseif($inventory->status === 'inactive'): ?> bg-yellow-500 
                            <?php else: ?> bg-red-500 
                            <?php endif; ?>">
                            <?php echo e(ucfirst($inventory->status)); ?>

                        </span>
                    </td>
                    <td class="border border-gray-300 px-4 py-2 text-center">
                        <a href="<?php echo e(route('inventories.show', $inventory)); ?>" class="text-green-500 hover:text-green-700 mr-3">View</a>
                        <a href="<?php echo e(route('inventories.edit', $inventory)); ?>" class="text-blue-500 hover:text-blue-700 mr-3">Edit</a>
                        <form method="POST" action="<?php echo e(route('inventories.destroy', $inventory)); ?>" style="display:inline;" onsubmit="return confirm('Are you sure?')">
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

    <?php if($inventories->isEmpty()): ?>
    <div class="mt-4 p-4 bg-gray-100 border border-gray-300 text-gray-700 rounded text-center">
        No items found. <a href="<?php echo e(route('inventories.create')); ?>" class="text-blue-500 hover:text-blue-700">Add one</a>
    </div>
    <?php endif; ?>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hr-app\resources\views/inventories/index.blade.php ENDPATH**/ ?>