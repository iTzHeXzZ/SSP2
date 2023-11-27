<?php $__env->startSection('content'); ?>
    <h1>Deine Projekte</h1>
    
    <table class="table">
        <thead>
            <tr>
                <th data-sort="0">Ort</th>
                <th data-sort="1">Postleitzahl</th>
                <th data-sort="2">Wohneinheiten</th>
                <th data-sort="3">Unbesuchte</th>
                <th data-sort="4">Vertrag</th>
                <th data-sort="5">Überleger</th>
                <th data-sort="6">Karte</th>
                <th data-sort="7">Kein Interesse</th>
                <th data-sort="8">Prozentsatz Vertrag</th>
                <th data-sort="9">Bearbeitungsdatum</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $uniqueNames = $projects->unique('ort');
            ?>
            <?php $__currentLoopData = $uniqueNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $countUnbesucht = $projects->where('ort', $project->ort)
                        ->where('postleitzahl', $project->postleitzahl)
                        ->where('status', 'Unbesucht')->count();
                    
                    $countVertrag = $projects->where('ort', $project->ort)
                        ->where('postleitzahl', $project->postleitzahl)
                        ->where('status', 'Vertrag')->count();
                    
                    $countOverleger = $projects->where('ort', $project->ort)
                        ->where('postleitzahl', $project->postleitzahl)
                        ->where('status', 'Überleger')->count();
                    
                    $countKarte = $projects->where('ort', $project->ort)
                        ->where('postleitzahl', $project->postleitzahl)
                        ->where('status', 'Karte')->count();
                    
                    $countKeinInteresse = $projects->where('ort', $project->ort)
                        ->where('postleitzahl', $project->postleitzahl)
                        ->where('status', 'Kein Interesse')->count();
                    
                    $totalWohneinheiten = $projects->where('ort', $project->ort)
                        ->where('postleitzahl', $project->postleitzahl)
                        ->sum('wohneinheiten');
                    
                    $percentage = 0;
                    if ($totalWohneinheiten > 0) {
                        $percentage = ($countVertrag / $totalWohneinheiten) * 100;
                    }

                    $lastUpdated = $projects
                        ->where('ort', $project->ort)
                        ->where('postleitzahl', $project->postleitzahl)
                        ->max('updated_at');
                ?>
                <tr>
                    <td><a class="locc" href="<?php echo e(route('projects.street', ['ort' => $project->ort, 'postleitzahl' => $project->postleitzahl])); ?>" style="text-decoration : none"><?php echo e($project->ort); ?></a></td>
                    <td><?php echo e($project->postleitzahl); ?></td>
                    <td><?php echo e($totalWohneinheiten); ?></td>
                    <td><?php echo e($countUnbesucht); ?></td>
                    <td><?php echo e($countVertrag); ?></td>
                    <td><?php echo e($countOverleger); ?></td>
                    <td><?php echo e($countKarte); ?></td>
                    <td><?php echo e($countKeinInteresse); ?></td>
                    <td><?php echo e(number_format($percentage, 2)); ?>%</td>
                    <td><?php echo e($lastUpdated); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/hecz/SSP2/resources/views/projects/index.blade.php ENDPATH**/ ?>