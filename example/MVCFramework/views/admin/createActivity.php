<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php'; ?>
        <title><?=$request->get('title')?></title>
    </head>
    <body>
        <div class="container-fluid p-3 bg-secondary text-light">
            <div class="d-flex w-100 justify-content-between">
                <a class="btn btn-dark btn-sm" href="admin" role="button" style="max-height: 32px"><i class="fa fa-calendar"></i></a>
                <p class="text-center"><?=$request->get('title')?></p>
                <p> </p>
            </div>
        </div>                        
        <div class="container mt-3">                
                <form name="createActivity" method="POST">                   
                    <div class="row">
                        <div class="col-lg-2 mt-3">
                        <label for="date" class="form-label text-primary">Datum</label>
                        <input type="text" 
                               name="date" 
                               class="form-control"
                               placeholder="dd/mm/jjjj" 
                               autofocus="" 
                               required
                               value="<?=$request->get('date')?>">                        
                        <?=$request->get('dateIsEmpty') ? '<div class="text-danger">
                        * Datum is niet correct ingevuld! Probeer opnieuw aub</div>':'';?>
                        </div>                        
                        <div class="col-lg-10 mt-3">
                        <label for="description" class="form-label text-primary">Titel</label>
                        <input type="text" 
                               name="description" 
                               class="form-control"
                               placeholder="<?=$request->get('placeholderTitle')?>"
                               required
                               value="<?=$request->get('description')?>">                        
                        <?=$request->get('descriptionIsEmpty') ? '<div class="text-danger">
                        * Titel is niet ingevuld! Probeer opnieuw aub</div>':'';?>
                        </div>                        
                    </div>                    
                    <!-- Submit button -->
                    <button type="submit" class="btn btn-dark btn-block mt-4">Bevestig</button>                    
                </form>                
            </div>
    </body>
</html>