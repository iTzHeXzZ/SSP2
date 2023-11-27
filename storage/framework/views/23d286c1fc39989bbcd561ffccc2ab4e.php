<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Projekt und Straßen einem Benutzer zuweisen</div>

                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('assign.project')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="form-group">
                            <label for="location_zipcode">Projekt auswählen:</label>
                            <select name="project_id" id="location_zipcode" class="form-control">
                                <option value="" disabled selected>Bitte wählen Sie ein Projekt</option>
                                <?php $__currentLoopData = $projectsByLocationAndZipcode; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $firstProject = $group->first();
                                    ?>
                                    <option value="<?php echo e($firstProject->id); ?>_<?php echo e($firstProject->ort); ?>_<?php echo e($firstProject->postleitzahl); ?>"><?php echo e($firstProject->ort); ?>, <?php echo e($firstProject->postleitzahl); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="street">Straßen auswählen:</label>
                            <select name="streets[]" id="street" class="form-control" multiple>
                                <!-- Hier werden die Straßenoptionen eingefügt -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="user_id">Benutzer auswählen:</label>
                            <select name="user_id" id="user_id" class="form-control">
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Projekt und Straße zuweisen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mt-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Zugewiesene Projekte und Straßen</div>

            <div class="card-body">
                <ul>
                    <?php $__currentLoopData = $allUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <strong><?php echo e($user->name); ?></strong>:
                            <button class="toggle-streets-btn" data-toggle-target=".user-streets-list-<?php echo e($user->id); ?>">
                                <i class="fas fa-chevron-down"></i> Anzeigen
                            </button>
                            <ul class="user-streets-list-<?php echo e($user->id); ?>" style="display: none;">
                                <?php
                                    $displayedStreets = [];
                                ?>
                                <?php $__currentLoopData = $user->projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(!in_array($project->strasse, $displayedStreets)): ?>
                                        <li>
                                            <?php echo e($project->ort); ?>, <?php echo e($project->strasse); ?>

                                            <form action="<?php echo e(route('remove.street.from.project')); ?>" method="post" style="display: inline-block;">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="user_id" value="<?php echo e($user->id); ?>">
                                                <input type="hidden" name="strasse" value="<?php echo e($project->strasse); ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Entfernen</button>
                                            </form>
                                        </li>
                                        <?php
                                            $displayedStreets[] = $project->strasse;
                                        ?>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>            
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- Font Awesome-Icon-Set -->
<script>
    $(document).ready(function() {
        const locationZipcodeSelect = $('#location_zipcode');
        const streetSelect = $('#street');
    
        locationZipcodeSelect.change(function() {
            // Lösche alle vorherigen Optionen aus dem Straßen-Dropdown
            streetSelect.empty();
    
            const selectedLocationZipcode = $(this).val();
            const [projectId, ort, postleitzahl] = selectedLocationZipcode.split('_');
    
            fetch(`/get-streets-for-location-zipcode/${ort}/${postleitzahl}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                },
            })
            .then(response => response.json())
            .then(data => {
                const groupedStreets = {};
    
                data.forEach(street => {
                    if (!groupedStreets[street.strasse]) {
                        groupedStreets[street.strasse] = [];
                    }
                    groupedStreets[street.strasse].push(street.hausnummer);
                });
    
                $.each(groupedStreets, function(strasse, hausnummern) {
                    streetSelect.append($('<option>', {
                        value: strasse,
                        text: strasse
                    }));
                });
    
                streetSelect.multiselect({
                    enableFiltering: true,
                    maxHeight: 300,
                });
            })
            .catch(error => console.error('Error:', error));
        });

        // Toggle-Funktion für die Straßenliste
        $('.toggle-streets-btn').click(function() {
            const targetSelector = $(this).data('toggle-target');
            $(targetSelector).toggle();
            const icon = $(this).find('i');
            if ($(targetSelector).is(':visible')) {
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                $(this).text('Verbergen');
            } else {
                icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                $(this).text('Anzeigen');
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/hecz/SSP2/resources/views/projects/assign_project.blade.php ENDPATH**/ ?>