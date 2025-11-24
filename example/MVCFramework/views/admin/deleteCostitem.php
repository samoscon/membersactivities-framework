<?php
    $costitem = $request->get('costitem');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php'; ?>
        <title>Verwijder kostitem</title>
    </head>
    <body>
        <div class="container-fluid p-3 bg-secondary text-light">
            <div class="d-flex w-100 justify-content-between">
                <a class="btn btn-dark btn-sm" href="<?=$request->get('returnpath')?>" role="button" style="max-height: 32px"><i class="fa fa-calendar"></i></a>
                <p class="text-center"><b><?=$costitem->activity->description .' - '. $costitem->description?></b></p>
                <p> </p>
            </div>
        </div>
        <div class="container mt-3">                
            <p class="text-danger">                    
                <?= $costitem->subscriptionsExisting ?
                    'Let op, verwijderen van een kostelement is niet toegelaten als er nog inschrijvingen voor dit kostelement zijn.' :
                    'Ben je zeker dat je kostelement wil verwijderen ?'?>
            </p>
                
            <form name="deleteCostitem" method="POST">                    
                <div class="row ff">
                    <p>
                        <b>Omschrijving: </b><?=$costitem->activity->description .' - '. $costitem->description?>
                    </p>
                    <p>
                        <b>Prijs: </b><?=$costitem->price?>
                    </p>
                </div>                    
                <button type="submit" class="btn btn-dark btn-block mt-4" <?= $costitem->subscriptionsExisting ? ' disabled="true"': '';?>>
                    Verwijder
                </button>                   
            </form>                
        </div>
    </body>
</html>