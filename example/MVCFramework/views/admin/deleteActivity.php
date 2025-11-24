<?php
    $activity = $request->get('activity');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php'; ?>
        <title>Verwijder activiteit</title>
    </head>
    <body>
        <div class="container-fluid p-3 bg-secondary text-light">
            <div class="d-flex w-100 justify-content-between">
                <a class="btn btn-dark btn-sm" href="<?=$request->get('returnpath')?>" role="button" style="max-height: 32px"><i class="fa fa-calendar"></i></a>
                <p class="text-center"><b><?=$activity->date .' - '. $activity->description?></b></p>
                <p> </p>
            </div>
        </div>
        <div class="container mt-3">                
            <p class="text-danger">                    
                <?= $activity->costitemsExisting ?
                    'Let op, verwijderen van een activiteit is niet toegelaten als er nog kostelementen zijn.' :
                    'Ben je zeker dat je deze activiteit wil verwijderen ?'?>
            </p>
                
            <form name="deleteCostitem" method="POST">                    
                <div class="row ff">
                    <p>
                        <b>Omschrijving: </b><?=$activity->date .' - '. $activity->description?>
                    </p>
                </div>                    
                <button type="submit" class="btn btn-dark btn-block mt-4" <?= $activity->costitemsExisting ? ' disabled="true"': '';?>>
                    Verwijder
                </button>                   
            </form>                
        </div>
    </body>
</html>