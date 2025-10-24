<?php 
    $activity = $request->get('activity');
    $potentialParents = $request->get('potentialParents');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php'; ?>
        <script src="<?=_APPDIR?>vendor/tinymce/tinymce/jquery.tinymce.min.js"></script>
        <script src="<?=_APPDIR?>vendor/tinymce/tinymce/tinymce.min.js"></script>
        <title><?=$activity->description?></title>
    </head>
    <body>
        <div class="container-fluid p-3 bg-secondary text-light">
            <div class="d-flex w-100 justify-content-between">
                <a class="btn btn-dark btn-sm" href="<?=$request->get('returnpath')?>" role="button" style="max-height: 32px"><i class="fa fa-calendar"></i></a>
                <p class="text-center"><b><?=$activity->description?></b></p>
                <p><?= $activity->subscriptionPeriodOver() ? 'Inschrijven is niet langer mogelijk' : '';?> </p>
            </div>
        </div>                        
        <div class="container mt-3">                
            <div class="row">
                <div class="col-lg-3 mt-5">
                    <div class="list-group mt-3">
                        <?php if(!$activity->costitems){ ?>
                        <a class="list-group-item list-group-item-action" href=deleteActivity?id=<?=$activity->getId()?>>
                            <div class="d-flex w-100 justify-content-between"><i class="fa fa-trash">
                                 <span class="ff">
                                    Verwijder activiteit
                                 </span></i>
                             </div>
                        </a>
                        <?php } ?>
                        <?php if(!$activity->parent) { ?>
                            <a class="togglebtn list-group-item list-group-item-action" href='#'?>
                                <div class="d-flex w-100 justify-content-between"><i class="fa fa-plus">
                                     <span class="ff">
                                        Toevoegen aan een groepsactiviteit
                                     </span></i>
                                 </div>
                            </a>
                        <?php } ?>
                         <a class="list-group-item list-group-item-action" href=xlsParticipants?id=<?=$activity->getId()?>>
                            <div class="d-flex w-100 justify-content-between"><i class="fa fa-table">
                                 <span class="ff">
                                    Volledige bezoekerslijst
                                 </span></i>
                             </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-9 mt-5">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                        <h3 class="">
                            Edit data 
                            <a class="btn btn-dark btn-sm m-1" title="Zie de activiteit op de website" 
                                          href="<?='activity?id='.$activity->getId()?>" 
                                          role="button" style="max-height: 32px"><i class="fa fa-eye"></i>
                            </a>
                        </h3>

                        </div>
                        <div class="card-body">
                        <form name="editActivity" method="POST">                   
                            <input type="hidden" 
                                   name="classification" 
                                   value="RGLR">

                            <div class="row ff">
                                <label for="date" class="form-label text-primary">Datum</label>
                                <input type="text" 
                                       name="date" 
                                       class="form-control"
                                       placeholder="dd/mm/jjjj" 
                                       autofocus=""
                                       value="<?=$activity->date?>">
                                <?=$request->get('dateIsEmpty') ? 
                                    '<div class="text-danger">
                                        * Datum is niet correct ingevuld! Probeer opnieuw aub
                                    </div>':
                                    '';
                                ?>

                                <label for="description" class="form-label text-primary">Titel</label>
                                <input type="text" 
                                       name="description" 
                                       class="form-control"
                                       value="<?=$activity->description?>">
                                <?=$request->get('descriptionIsEmpty') ? 
                                    '<div class="text-danger">
                                        * Titel is niet ingevuld! Probeer opnieuw aub
                                    </div>':
                                    '';
                                ?>

                                <label for="duedate" class="form-label text-primary">Inschrijven tot</label>
                                <input type="text" 
                                       name="duedate" 
                                       class="form-control"
                                       placeholder="dd/mm/jjjj"
                                       value="<?=$activity->duedate?>">
                                <?=$request->get('duedateIsEmpty') ? 
                                    '<div class="text-danger">
                                        * Datum is niet correct ingevuld! Probeer opnieuw aub
                                    </div>':
                                    '';
                                ?>
                            </div>
                            <br>
                            <div class="t-3">
                                <textarea name="longdescription" id="mytextarea"
                                    style="width: 100%"><?=$activity->longdescription?></textarea>
                            </div>
                            <br/>
                            <div class="row">
                                <label for="location" class="form-label text-primary">Locatie</label>
                                <input type="text" 
                                       name="location" 
                                       class="form-control"
                                       required
                                       id="location" 
                                       value="<?=$activity->location?>">
                            </div>

                            <div class="row">
                                <div class="col-lg-6 mt-3">
                                <label for="start" class="form-label text-primary">Startuur</label>
                                <input type="text" 
                                       name="start" 
                                       class="form-control"
                                       required
                                       placeholder="uu:mm" 
                                       value="<?=$activity->start?>">
                                </div>
                                <div class="col-lg-6 mt-3">
                                <label for="end" class="form-label text-primary">Einduur</label>
                                <input type="text" 
                                       name="end" 
                                       class="form-control"
                                       required
                                       placeholder="uu:mm" 
                                       value="<?=$activity->end?>">
                                </div>
                            </div>
                        </div>                    
                            <!-- Submit button -->
                            <button type="submit" class="btn btn-dark btn-block m-5">Bevestig</button>

                        </form>                                   
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header bg-primary text-white">                
                <h3 class="">
                    Tickets 
                    <a class="btn btn-dark btn-sm m-1" title="Voeg kostelement toe" 
                                  href="<?='createCostitem?id='.$activity->getId()?>" 
                                  role="button" style="max-height: 32px"><i class="fa fa-plus"></i>
                    </a>
                </h3>
                </div>
                <div class="list-group">
                    <?php if($activity->costitems){
                        foreach ($activity->costitems as $costitem) { ?>
                            <a class="list-group-item list-group-item-action" href=editCostitem?id=<?= $costitem->getId()?>>
                                <div class="d-flex w-100 justify-content-between">
                                    <i class="fa fa-pencil"><span class=" text-primary"> <?=$costitem->description?></span></i>
                                    <p class="bg-secondary rounded-2 text-center" style="font-size: 0.75em; padding: 0.15em;"> 
                                            <span class="badge"><?=$costitem->numberOfSubscriptions?></span>
                                    </p> 
                                </div>                                        
                            </a>
                            <div class="list-group">
                            <?php foreach ($costitem->subscriptions as $subscription) { ?>
                                  <a href="editPayment?id=<?=$subscription->payment->getId()?>" class="list-group-item list-group-item-action">
                                      <?=$subscription->member->name .' - '.$subscription->member->email .' - '
                                        .$subscription->quantity .' - '.$subscription->payment->status .' - Orderid '
                                        . $subscription->payment->getId()?>
                                  </a>                                
                            <?php } ?>
                            </div>
                        <?php } 
                    }?>
                </div>
                <div class="card-footer bg-primary text-white">                
                    <p class="">Totale bedrag (effectief betaald): <?=$activity->getTotalAmountReceived()?>.00EUR</p>
                </div>
                
            </div>
            <div class="row">
                <?php if($activity->children) { ?>
                <h3 class="text-primary mt-5">
                    Bevat
                </h3>
                <div class="list-group">
                    <?php foreach ($activity->children as $child) { ?>
                        <a class="list-group-item list-group-item-action" href=editActivity?id=<?= $child->getId()?>>
                            <div class="d-flex w-100 justify-content-between">
                                <i class="fa fa-pencil"><span class="ff"> <?=$child->date?> <?=$child->description?></span></i>
                            </div>                                        
                        </a>
                <?php }?> 
                </div>
                <?php } ?>
            </div>
            <div class="row">
                <?php if($activity->parent) { ?>
                <h3 class="text-primary mt-5">
                    Behoort tot
                </h3>
                <p><a href="editActivity?id=<?= $activity->parent->getId()?>"><?=$activity->parent->description?></a>
                    <span><a  class="btn btn-dark btn-block m-5" href="removeActivityFromComposite?id=<?= $activity->getId()?>">Verwijder uit groep</a></span>
                </p>
                <?php } else if ($potentialParents) { ?>
<!--                <button class="togglebtn btn btn-dark btn-block mt-4">Wil je deze activiteit toevoegen aan een groepsactiviteit ?</button>-->
                <div id="myListgroup" class="list-group mt-4 invisible">
                    <p class="mt-'"><b>Kies uit volgende lijst een activiteit waartoe deze activiteit behoort:</b></p>
                    <?php foreach ($potentialParents as $potentialParent) { ?>
                        <a class="list-group-item list-group-item-action" href=addActivityToComposite?id=<?= $activity->getId()?>&parent_id=<?= $potentialParent->getId()?>>
                            <div class="d-flex w-100 justify-content-between">
                                <i class="fa fa-pencil"><span class="ff"> <?=$potentialParent->description?></span></i>
                            </div>                                        
                        </a>
                <?php }?> 
                </div>
                <?php } ?>
            </div>
        </div>                
        <script type="text/javascript">
        tinymce.init({
            selector: "#mytextarea",
            theme: "modern",
            branding: false,
            plugins: [
                "advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste"
            ],
            height: 380,
            menubar: false,
            convert_urls: false,
            statusbar: false,
            toolbar: "insertfile undo redo | styleselect fontselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image table",
        });
        </script>
        <?php echo'<script src="' . _APPDIR . 'MVCFramework/views/includes/js/toggleVisibility.js"></script>' ?>
    </body>
</html>