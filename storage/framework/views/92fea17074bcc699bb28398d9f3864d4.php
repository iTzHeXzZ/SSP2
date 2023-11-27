<?php $__env->startSection('content'); ?>
    <h1>Straßen in <?php echo e($postleitzahl); ?>, <?php echo e($ort); ?></h1>

    <table class="table">
        <thead>
            <tr>
                <th data-sort="0">Strasse</th>
                <th data-sort="1">Wohneinheiten</th>
                
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
                $uniqueNames = $projects->unique('strasse');
            ?>
            <?php $__currentLoopData = $uniqueNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(auth()->user()->hasRole(['Admin', 'Viewer']) || auth()->user()->projects->contains($project)): ?>
                    <tr>
                        <td><a class="locc" href="<?php echo e(route('projects.number', ['ort' => $project->ort, 'postleitzahl' => $project->postleitzahl, 'strasse' => $project->strasse])); ?>" style="text-decoration: none"><?php echo e($project->strasse); ?></a></td>
                        <td><?php echo e($project->wohneinheiten); ?></td>
                        
                        <td><?php echo e($countUnbesucht[$project->strasse]); ?></td>
                        <td><?php echo e($countVertrag[$project->strasse]); ?></td>
                        <td><?php echo e($countOverleger[$project->strasse]); ?></td>
                        <td><?php echo e($countKarte[$project->strasse]); ?></td>
                        <td><?php echo e($countKeinInteresse[$project->strasse]); ?></td>
                        <td>
                            <?php
                                $percentage = 0;
                                if ($project->wohneinheiten > 0) {
                                    $percentage = ($countVertrag[$project->strasse] / $project->wohneinheiten) * 100;
                                }
                            ?>
                            <?php echo e(number_format($percentage, 2)); ?>%
                        </td>
                        <td>
                            <?php
                                $lastUpdated = $projects
                                    ->where('ort', $project->ort)
                                    ->where('postleitzahl', $project->postleitzahl)
                                    ->where('strasse', $project->strasse)
                                    ->max('updated_at');
                            ?>
                            <?php echo e($lastUpdated); ?>

                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/hecz/SSP2/resources/views/projects/street.blade.php ENDPATH**/ ?>