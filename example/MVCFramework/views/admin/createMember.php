<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php'; ?>
        <title>Nieuw lid</title>
    </head>
    <body>
        <div class="container-fluid p-3 bg-secondary text-light">
            <div class="d-flex w-100 justify-content-between">
                <a class="btn btn-dark btn-sm" href="<?=$request->get('returnpath')?>" role="button" style="max-height: 32px"><i class="fa fa-calendar"></i></a>
                <p class="text-center"><b>Nieuw lid</b></p>
                <p> </p>
            </div>
        </div>
        <div class="container mt-3">                
            <form method="POST">
                <div class="row">
                  <div class="col-lg-5 mt-3">
                    <label for="name" class="form-label text-primary">Naam</label>
                    <input type="text" 
                           name="name" 
                           class="form-control"
                           required
                           value="<?=$request->get('name')?>">
                    <?=$request->get('nameIsEmpty') ? 
                        '<div class="text-danger">
                            * Voornaam is niet ingevuld! Probeer opnieuw aub
                        </div>':
                        '';
                    ?>
                  </div>
                  <div class="col-lg-7 mt-3">
                     <label for="email" class="form-label text-primary">Mail</label>
                    <input type="email" 
                           name="email" 
                           class="form-control"
                           required
                           placeholder="Geef geldig e-mailadres in ...." 
                           value="<?=$request->get('email')?>">
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
                <button type="submit" class="btn btn-dark btn-block mt-4">Bevestig</button>
            </form>                
        </div>
    </body>
</html>