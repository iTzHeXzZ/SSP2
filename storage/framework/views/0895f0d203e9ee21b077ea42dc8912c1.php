<?php $__env->startSection('content'); ?>
<h2><?php echo e($postleitzahl); ?>,<a href="<?php echo e(route('projects.street', ['ort' => $ort, 'postleitzahl' => $postleitzahl])); ?>" style="text-decoration : none"><?php echo e($ort); ?></a>,<?php echo e($strasse); ?> Hausnummer:</h2>

    <table class="table">
        <thead>
            <tr>
                <th data-sort="0">Hausnummer</th>
                <th data-sort="1">Status</th>
                <th data-sort="2">Wohneinheiten</th>
                <th data-sort="3">Bestand</th>
                <th data-sort="4">Bearbeitungsdatum</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($project->hausnummer); ?></td>
                    <td>
                        <style>
                            /* Stil für das Formular innerhalb der Zelle */
                            form {
                                display: flex;
                                flex-direction: column;
                                align-items: flex-start;
                            }
                    
                            /* Stil für das Select-Feld */
                            select {
                                padding: 8px;
                                margin-bottom: 10px;
                                border: 1px solid #ccc;
                                border-radius: 4px;
                                width: 100%;
                            }
                    
                            /* Stil für das Textarea-Feld */
                            textarea {
                                padding: 8px;
                                margin-bottom: 10px;
                                border: 1px solid #ccc;
                                border-radius: 4px;
                                width: 100%;
                                resize: vertical;
                                min-height: 100px;
                            }
                    
                            /* Stil für den Speichern-Button */
                            .btn-primary {
                                background-color: #007bff;
                                color: white;
                                padding: 10px 15px;
                                border: none;
                                border-radius: 4px;
                                cursor: pointer;
                            }
                    
                            .btn-primary:hover {
                                background-color: #0056b3;
                            }

                            @media (max-width: 768px) {
                                form {
                                    flex-direction: column; /* Formular-Inhalte in Spalten anordnen */
                                }

                                select {
                                    width: 100%; /* Feld auf volle Breite */
                                }

                                textarea {
                                    width: 100%; /* Feld auf volle Breite */
                                }

                                .form-group {
                                    overflow-x: auto; /* Horizontales Scrollen ermöglichen */
                                    width: 100%; /* Breite auf 100% setzen */
                                }
                            }
                        </style>
                        
                        <form method="POST" action="<?php echo e(route('projects.update', $project->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <select name="status" onchange="handleVertragSelect(this, '<?php echo e($ort); ?>', '<?php echo e($project->hausnummer); ?>')">
                                <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($option); ?>" <?php echo e($project->status === $option ? 'selected' : ''); ?>>
                                        <?php echo e($option); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php if(!auth()->user()->hasRole('Viewer')): ?>
                            <textarea name="notiz"><?php echo e($project->notiz); ?></textarea>
                            <button type="submit" class="btn btn-primary">Speichern</button>
                            <?php endif; ?>
                        </form>
                    </td>
                                       
                    <td><?php echo e($project->wohneinheiten); ?></td>
                    <td><?php echo e($project->bestand); ?> </td>
                    <td><?php echo e($project->updated_at); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
// Speichere die aktuelle Scroll-Position, bevor du die Seite verlässt
window.addEventListener('beforeunload', () => {
  const scrollPosition = window.scrollY;
  localStorage.setItem('scrollPosition', scrollPosition);
});

// Rufe die gespeicherte Scroll-Position ab und scrolle zur Position zurück
const scrollPosition = localStorage.getItem('scrollPosition');
if (scrollPosition !== null) {
  window.scrollTo(0, scrollPosition);
  localStorage.removeItem('scrollPosition');
}


function handleVertragSelect(selectElement, ort, hausnummer) {
            var selectedValue = selectElement.value;

            
            if (selectedValue === 'Vertrag' && ort.includes('SWLangenfeld')) {
                var popup = window.open('https://www.stw-langenfeld.de/media/glasfaser-ausbaugebiete-langenfeld/', '_blank');

                var interval = setInterval(function () {
                    if (popup.closed) {
                        clearInterval(interval);
                        alert('Vertrag erfolgreich erstellt');
                    }
                }, 1000);  
            }
        }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/hecz/SSP2/resources/views/projects/number.blade.php ENDPATH**/ ?>