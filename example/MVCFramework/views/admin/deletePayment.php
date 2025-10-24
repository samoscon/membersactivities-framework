<?php
    $payment = $request->get('payment');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php'; ?>
        <title>Verwijder betaling</title>
    </head>
    <body>
        <div class="container-fluid p-3 bg-secondary text-light">
            <div class="d-flex w-100 justify-content-between">
                <a class="btn btn-dark btn-sm" href="<?=$request->get('returnpath')?>" role="button" style="max-height: 32px"><i class="fa fa-calendar"></i></a>
                <p class="text-center"><b>Order <?=$payment->getId()?> van <?=$payment->member->name?> voor een bedrag van <?=$payment->amount?>EUR</b></p>
                <p> </p>
            </div>
        </div>
        <div class="container mt-3">                
            <p class="text-danger">                    
                <?= $payment->status === 'paid' ?
                    'Let op, verwijderen van een betaling is niet toegelaten als status op paid staat.' :
                    'Ben je zeker dat je deze betaling wil verwijderen ?'?>
            </p>
                
            <form name="deletePayment" method="POST">                    
                <div class="row ff">
                    <p>
                        Betaling verwijderen ?
                    </p>
                </div>                    
                <button type="submit" class="btn btn-dark btn-block mt-4" <?= $payment->status === 'paid' ? ' disabled="true"': '';?>>
                    Verwijder
                </button>                   
            </form>                
        </div>
    </body>
</html>