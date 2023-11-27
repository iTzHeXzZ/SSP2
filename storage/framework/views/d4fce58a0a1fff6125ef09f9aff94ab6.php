<?php $__env->startSection('content'); ?>

    <form action="<?php echo e(route('pdf.process')); ?>" method="post">
        <?php echo csrf_field(); ?>
        <?php $__currentLoopData = $fillableFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <label for="<?php echo e($field['name']); ?>"><?php echo e($field['name']); ?> (<?php echo e($field['type']); ?>):</label>
            <?php if($field['type'] === 'text'): ?>
                <input type="text" name="<?php echo e($field['name']); ?>" value="<?php echo e(old($field['name'])); ?>">
            <?php elseif($field['type'] === 'checkbox'): ?>
                <input type="checkbox" name="<?php echo e($field['name']); ?>" <?php echo e(old($field['name']) ? 'checked' : ''); ?>>
            <?php endif; ?>
            <br>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <button type="submit">PDF bearbeiten und speichern</button>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/hecz/SSP2/resources/views/pdf/form.blade.php ENDPATH**/ ?>