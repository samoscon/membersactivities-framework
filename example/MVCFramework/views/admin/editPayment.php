<?php
    $payment = $request->get('payment');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php'; ?>
        <title>Bewerk ticket: <?=$payment->getId()?></title>
    </head>
    <body>
        <div class="container-fluid p-3 bg-secondary text-light">
            <div class="d-flex w-100 justify-content-between">
                <a class="btn btn-dark btn-sm" href="<?=$request->get('returnpath')?>" role="button" style="max-height: 32px"><i class="fa fa-calendar"></i></a>
                <p class="text-center">
                    <b>
                        Orderid <?=$payment->getId()?> van <?=$payment->member->name?> voor een bedrag van <?=$payment->amount?> EUR
                    </b></p>
                <p> </p>
            </div>
        </div>
        <div class="container mt-3">     
            <div class="row">
                <div class="col-lg-3 mt-5">
                    <div class="list-group mt-3">
                        <a class="list-group-item list-group-item-action" href=deletePayment?id=<?=$payment->getId()?>>
                            <div class="d-flex w-100 justify-content-between"><i class="fa fa-trash">
                                     <span class="ff">
                                        Verwijder betaling
                                     </span></i>
                             </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-9 mt-5">                    
                    <div class="card">
                    <div class="card-header bg-primary text-white">
                        Edit Data
                    </div>
                    <form name="updateSubscription" method="POST">                   
                        <div class="row m-1">
                            <div class="col-lg-6 mt-3">
                                <label for="status" class="form-label text-primary">
                                    status
                                </label>
                                <input type="text" name="status" class="form-control" required autofocus
                                       placeholder="huidige status van de betaling"
                                       value="<?=$payment->status?>">
                            </div>                       
                            <div class="col-lg-6 mt-3">
                                <label for="amount" class="form-label text-primary">
                                    bedrag (effectief betaald)
                                </label>
                                <input type="text" name="amount" class="form-control" required autofocus
                                       placeholder="effectief betaalde bedrag van de betaling"
                                       value="<?=$payment->amount?>">
                            </div>                       
                        </div>
                        <!-- Submit button -->
                        <button type="submit" class="btn btn-dark btn-block m-4">Bevestig</button>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>