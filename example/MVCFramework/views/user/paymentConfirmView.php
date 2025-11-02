<?php
$payment = $request->get('payment');
$activity = $request->get('activity');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php'; ?>
        <title>Feedback</title>
    </head>
    <body>
        <div class="container-fluid p-3 bg-white text-black-50">
            <div class="d-flex w-100 justify-content-between">
                 <img src=<?=_LOGO?> alt="Logo ontbreekt" width="35%"> 
                <p class="text-center"><b>Feedback</b></p>
                <p> </p>
            </div>
        </div>
        <div class="container text-primary m-5">
            <div class="row">
                <p>We have received your payment. A mail with your tickets has also been sent to your mailaddress <b>
                        <?=$payment->member->email?></b></p>
                <p><?=$payment->paymenttypeimplementation->getOrderedTicketsText($payment)?></p>
                <p><?=$payment->paymenttypeimplementation->getDownloadText($payment, $activity)?></p>
                <p><a href=<?=_HOMEPAGE?> class="btn btn-primary btn-block m-4" role="button">Back to website</a></p>
            </div>
        </div>
    </body>
</html>
