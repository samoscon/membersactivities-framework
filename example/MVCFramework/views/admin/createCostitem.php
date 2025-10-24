<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php'; ?>
        <title>Nieuw kostitem: <?=$request->get('activity')->description?></title>
    </head>
    <body>
        <div class="container-fluid p-3 bg-secondary text-light">
            <div class="d-flex w-100 justify-content-between">
                <a class="btn btn-dark btn-sm" href="<?=$request->get('returnpath')?>" role="button" style="max-height: 32px"><i class="fa fa-calendar"></i></a>
                <p class="text-center"><b><?=date('d/m/Y', strtotime($request->get('activity')->date)) .' - '
                . $request->get('activity')->description?> - Nieuw kostitem</b></p>
                <p> </p>
            </div>
        </div>
        <div class="container mt-3">                
                <form name="createCostitem" method="POST">                   
                    <div class="row">
                      <div class="col-lg-8 mt-3">
                        <label for="description" class="form-label text-primary">
                            Kostitem
                        </label>
                        <input type="text" name="description" class="form-control" required autofocus
                               placeholder="Omschrijving van het kostitem"
                               value="<?=$request->get('costitem')?>">
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
                               value="<?=$request->get('price')?>">
                        <?=$request->get('priceIsEmpty') ? 
                            '<div class="text-danger">
                                * Prijs is niet of niet correct ingevuld! Probeer opnieuw aub
                            </div>':
                            '';
                        ?>
                      </div>
                    </div>
                <!-- Submit button -->
                <button type="submit" class="btn btn-dark btn-block mt-4">Bevestig</button>
                </form>
            </div>
        </body>
</html>