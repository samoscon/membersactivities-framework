<?php
    $member = $request->get('member');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php'; ?>
        <title>Verwijder bezoeker</title>
    </head>
    <body>
        <div class="container-fluid p-3 bg-secondary text-light">
            <div class="d-flex w-100 justify-content-between">
                <a class="btn btn-dark btn-sm" href="<?=$request->get('returnpath')?>" role="button" style="max-height: 32px"><i class="fa fa-calendar"></i></a>
                <p class="text-center"><b><?=$member->name .' - '. $member->email?></b></p>
                <p> </p>
            </div>
        </div>
        <div class="container mt-3">                
            <p class="text-danger">                    
                <?= $member->subscriptionsExisting ?
                    'Let op, verwijderen van een member is niet toegelaten als er nog inschrijvingen zijn.' :
                    'Ben je zeker dat je '. $member->name .' - '. $member->email . ' wil verwijderen ?'?>
            </p>
                
            <form name="deleteMember" method="POST">                    
                <button type="submit" class="btn btn-dark btn-block mt-4" <?= $member->subscriptionsExisting ? ' disabled="true"': '';?>>
                    Verwijder
                </button>                   
            </form>                
        </div>
    </body>
</html>