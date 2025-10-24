<?php
    $activity = $request->get('activity');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php'; ?>
        <title>Brussels Muzieque Ticketing</title>
    </head>
    <body>
        <div class="container-fluid p-3 bg-white text-black-50">
            <div class="d-flex w-100 justify-content-between">
                 <img src="assets/bm-WHITE.jpg" alt="Brussels Muzieque" width="35%"> 
                <p class="text-center"><b>Ticketing system</b></p>
                <p> </p>
            </div>
        </div>
        <div class="container mt-3">
            <h1 class="text-primary"><?="{$activity->description}"?></h1>   
            <div><?=$activity->longdescription?></div><br>

            <b>When: </b><?=date('d/m/Y', strtotime($activity->date))?>

            <?php $start = $activity->start;?>
            <?=($start <> "00:00" && $start)?
                " ({$start} -":
                ""
            ?>

            <?php $end = $activity->end;?>
            <?=($end <> "00:00" && $end)?
                " {$end})":
                ""
            ?><br>

            <br><b>Where: </b>
                <?=$activity->location ?? 
                    'Info volgt'
                ?><br>
            <form method="POST">
                <div class="card mt-5" style="width: 98%;">
                <div class="row m-3">
                    <?php if($activity->costitems){
                        $i = 1;
                        foreach ($activity->costitems as $costitem) { ?>
                            <div class="row mt-3">
                                <div class="col-lg-3">
                                    <b><label class="form-label" for="<?='qty_'.$i?>"><?=$costitem->description?>: </label></b> 
                                </div>
                                <div class="col-lg-2">
                                        <input type="number" min="0" max="99" value="" id="<?='qty_'.$i?>" name="<?=$costitem->getId()?>" class="qty form-control" />
                                </div>
                                <div class="col-lg-7">
                                    <input type="hidden" id="<?='price_'.$i?>" value="<?=$costitem->price?>">
                                </div>
                            </div>
                        <?php $i++; } 
                    }?>
                    <input type="hidden" id="numberOfCostitems" value="<?=($i - 1)?>">                            
                </div>
                <div class="row m-3">
                    <div class="col-lg-3">
                    <label for="name">
                        <b>Name: </b>
                    </label>
                    </div>
                    <div class="col-lg-9">
                    <input type="text" placeholder="First and last name" size="35" required id="name" name="name" value="<?=$request->get('name')?>" class="form-control">
                    </div>
                </div>
                <?php if ($request->get('nameIsEmpty')) { ?> 
                    <div class='row m-3 text-danger'>* Provide a name</div>
                <?php } ?>
                <div class="row m-3">
                    <div class="col-lg-3">
                    <label for="email">
                        <b>Email: </b>
                    </label>
                    </div>
                    <div class="col-lg-9">
                    <input type="text" placeholder="Valid mailaddress" size="35" required id="email" name="email" value="<?=$request->get('email')?>" class="form-control">
                    </div>
                </div>
                <?php if ($request->get('emailIsEmpty')) { ?> 
                    <div class='row m-3 text-danger'>* Provide a valid email address</div>
                <?php } ?>
                <div class="row m-3">               
                    <h3 class="text-primary">Total to pay: 
                    <span id="result">0</span>.00 €</h3>
                    <input type="hidden" id="total" name="result" value="0">                
                </div>
                </div>
                <!-- Submit button -->
                <button type="submit" class="btn btn-dark btn-block mt-4">Order your ticket(s)</button>
            </form>
        </div>
        <!-- Subscription script -->	
        <?php echo'<script src="' . _APPDIR . 'MVCFramework/views/includes/js/subscription.js"></script>' ?>
    </body>
</html>
