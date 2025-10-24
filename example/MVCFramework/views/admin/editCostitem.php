<?php
    $costitem = $request->get('costitem');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php'; ?>
        <title>Bewerk kostitem: <?=$costitem->activity->description?></title>
    </head>
    <body>
        <div class="container-fluid p-3 bg-secondary text-light">
            <div class="d-flex w-100 justify-content-between">
                <a class="btn btn-dark btn-sm" href="<?=$request->get('returnpath')?>" role="button" style="max-height: 32px"><i class="fa fa-calendar"></i></a>
                <p class="text-center"><b><?=date('d/m/Y', strtotime($costitem->activity->date)) .' - '
                . $costitem->activity->description?></b></p>
                <p> </p>
            </div>
        </div>
        <div class="container mt-3">     
            <div class="row">
                <div class="col-lg-3 mt-5">
                    <div class="list-group mt-3">
                        <a class="list-group-item list-group-item-action" href=deleteCostitem?id=<?=$costitem->getId()?>>
                            <div class="d-flex w-100 justify-content-between"><i class="fa fa-trash">
                                     <span class="ff">
                                        Verwijder kostelement
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
                    <div class="card-body">
                    <form name="updateCostitem" method="POST">                   
                        <div class="row">
                            <div class="col-lg-8 m-3">
                                <label for="description" class="form-label text-primary">
                                    Bewerk kostitem
                                </label>
                                <input type="text" name="description" class="form-control" required autofocus
                                       placeholder="Omschrijving van het kostitem"
                                       value="<?=$costitem->description?>">
                                <?=$request->get('descriptionIsEmpty') ? 
                                    '<div class="text-danger">
                                        * Kostelement titel is niet ingevuld! Probeer opnieuw aub
                                    </div>':
                                    '';
                                ?>
                            </div>                       
                            <div class="col-lg-2 mt-3">
                                <label for="price" class="form-label text-primary">
                                    Prijs
                                </label>
                                <input type="text" 
                                       name="price" 
                                       placeholder="0.00" 
                                       class="form-control" required
                                       value="<?=$costitem->price?>">
                                <?=$request->get('priceIsEmpty') ? 
                                    '<div class="text-danger">
                                        * Prijs is niet of niet correct ingevuld! Probeer opnieuw aub
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
            </div>
        </div>
    </body>
</html>