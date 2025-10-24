<?php
    $member = $request->get('member');
    $potentialParents = $request->get('potentialParents');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php'; ?>
        <title>Bewerk <?=$member->name?></title>
    </head>
    <body>
        <div class="container-fluid p-3 bg-secondary text-light">
            <div class="d-flex w-100 justify-content-between">
                <a class="btn btn-dark btn-sm" href="<?=$request->get('returnpath')?>" role="button" style="max-height: 32px"><i class="fa fa-calendar"></i></a>
                <p class="text-center"><b><?=$member->name?></b></p>
                <p> </p>
            </div>
        </div>
        <div class="container">     
            <div class="row">
                <div class="col-lg-3 mt-5">
                    <div class="list-group mt-3">
                    <?php if(!$member->payments->count() > 0){ ?>
                        <a class="list-group-item list-group-item-action" href=deleteMember?id=<?=$member->getId()?>>
                            <div class="d-flex w-100 justify-content-between"><i class="fa fa-trash">
                                     <span class="ff">
                                        Verwijder bezoeker
                                     </span></i>
                             </div>
                        </a>
                    <?php } ?>
                    <?php if(!$member->parent) { ?>
                        <a class="togglebtn list-group-item list-group-item-action" href=#?id=<?=$member->getId()?>>
                            <div class="d-flex w-100 justify-content-between"><i class="fa fa-plus">
                                 <span class="ff">
                                    Toevoegen aan een groep
                                 </span></i>
                             </div>
                        </a>
                    <?php } ?>
                     </div>
                </div>
                <div class="col-lg-9 mt-5">
                    <div class="card">
                    <div class="card-header bg-primary text-white">
                        Edit Data
                    </div>
                    <form name="updateMember" method="POST">                   
                        <div class="row m-1">
                          <div class="col-lg-5 mt-3">
                            <label for="name" class="form-label text-primary">Naam</label>
                            <input type="text" 
                                   name="name" 
                                   class="form-control"
                                   required
                                   value="<?=$member->name?>">
                            <?=$request->get('nameIsEmpty') ? 
                                '<div class="text-danger">
                                    * Voornaam is niet ingevuld! Probeer opnieuw aub
                                </div>':
                                '';
                            ?>
                          </div>
                          <div class="col-lg-7 mt-3">
                            <label for="lastname" class="form-label text-primary">Mail</label>
                            <input type="email" 
                                   name="email" 
                                   class="form-control"
                                   required
                                   placeholder="Geef geldig e-mailadres in ...." 
                                   value="<?=$member->email?>">
                            <?=$request->get('emailIsEmpty') ? 
                                '<div class="text-danger">
                                    * Gebruikersnaam is niet correct ingevuld! Probeer opnieuw aub
                                </div>':
                                '';
                            ?>
                            <?=$request->get('emailAlreadyExists') ? 
                                '<div class="text-danger">
                                    * Gebruikersnaam bestaat reeds! Probeer opnieuw aub
                                </div>':
                                '';
                            ?>    
                          </div>
                        </div>

                        <!-- Submit button -->
                        <button type="submit" class="btn btn-dark btn-block m-4">Bevestig</button>
                    </form>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header bg-primary text-white">                
                    <h3 class="">
                       Tickets 
                    </h3>
                </div>
                <div class="list-group">
                    <?php if($member->payments){
                        foreach ($member->payments as $payment) { ?>
                            <a class="list-group-item list-group-item-action" href=editPayment?id=<?= $payment->getId()?>>
                                <div class="d-flex w-100 justify-content-between">
                                    <i class="fa fa-pencil"><span class=" ff"> <?=$payment->date?> - 
                                            <?=$payment->source?> - 
                                            <?=$payment->status?>
                                        </span>
                                    </i>
                                    <p class="bg-secondary rounded-2 text-center" style="font-size: 0.75em; padding: 0.15em;"> 
                                            <span class="badge"><?=$payment->amount?></span>
                                    </p> 
                                </div>                                        
                            </a>
                            <div class="list-group">
                            <?php foreach ($payment->subscriptions as $subscription) { ?>
                                  <a href="#" class="list-group-item list-group-item-action ff">
                                      <?=$subscription->costitem->activity->date.' - '. $subscription->costitem->activity->description.' - '.
                                            $subscription->costitem->description.' - '.$subscription->costitem->price.'EUR - * '.$subscription->quantity?>
                                  </a>                                
                            <?php } ?>
                            </div>
                        <?php } 
                    }?>                                
                </div>
                <div class="card-footer bg-primary text-white">                
                    <p class="">Totale bedrag (effectief betaald): <?=$member->getTotalAmountReceived()?>.00EUR</p>
                </div>
            </div>
            <div class="row">
                <?php if($member->children) { ?>
                <h3 class="text-primary mt-5">
                    Bevat
                </h3>
                <div class="list-group">
                    <?php foreach ($member->children as $child) { ?>
                        <a class="list-group-item list-group-item-action" href=editMember?id=<?= $child->getId()?>>
                            <div class="d-flex w-100 justify-content-between">
                                <i class="fa fa-pencil"><span class="ff"> <?=$child->name?> <?=$child->email?></span></i>
                            </div>                                        
                        </a>
                <?php }?> 
                </div>
                <?php } ?>
            </div>
            <div class="row">
                <?php if($member->parent) { ?>
                <h3 class="text-primary mt-5">
                    Behoort tot
                </h3>
                <p><a href="editMember?id=<?= $member->parent->getId()?>"><?=$member->parent->name?></a>
                    <span><a  class="btn btn-dark btn-block m-5" href="removeMemberFromComposite?id=<?= $member->getId()?>">Verwijder uit groep</a></span>
                </p>
                <?php } else if ($potentialParents) { ?>
                <!--<button class="togglebtn btn btn-dark btn-block mt-4">Wil je deze bezoeker toevoegen aan een groep ?</button>-->
                <div id="myListgroup" class="list-group mt-4 invisible">
                    <p class="mt-'"><b>Kies uit volgende lijst een groep waar je deze bezoeker aan wil toevoegen:</b></p>
                    <?php foreach ($potentialParents as $potentialParent) { ?>
                        <a class="list-group-item list-group-item-action" href=addMemberToComposite?id=<?= $member->getId()?>&parent_id=<?= $potentialParent->getId()?>>
                            <div class="d-flex w-100 justify-content-between">
                                <i class="fa fa-pencil"><span class="ff"> <?=$potentialParent->name?></span></i>
                            </div>                                        
                        </a>
                <?php }?> 
                </div>
                <?php } ?>
            </div>
        </div>
        <?php echo'<script src="' . _APPDIR . 'MVCFramework/views/includes/js/toggleVisibility.js"></script>' ?>
    </body>
</html>