<?php 
$payment = $request->get('payment');
?>

<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php'; ?>
        <title>Brussels Muzieque payment</title>
    </head>
    <body>
        <div class="container-fluid p-3 bg-white text-black-50">
            <div class="d-flex w-100 justify-content-between">
                 <img src=<?=_LOGO?> alt="Logo ontbreekt" width="35%"> 
                <p class="text-center"><b>Betalingswijze</b></p>
                <p> </p>
            </div>
        </div>
        <div class="container mt-3">
            <h2 style="text-align:center">Uw inschrijving is aangemaakt.</h2>
            <form autocomplete="off" method="POST">
                <div class="card">
                    <div class="row m-5"
                        <p><b>Welke betalingswijze verkies je:</b></p>
  			<input type="radio" id="online" name="paymentmethod" value="online" data-role="none"  onclick="toggleWireTransfer()" checked> Ik betaal online<br>
  			<input type="radio" id="wire" name="paymentmethod" value="wire"  onclick="toggleWireTransfer()" data-role="none"> Ik schrijf het bedrag rechtstreek over<br>
  			<div class="text-primary" id="wiretransfer" style="display:none;">
  			    Om over te schrijven, gebruik de volgende gegevens:
  			    <ul>
  				<li><b>IBAN:</b> BExx xxxx xxxx xxxx</li>
  				<li><b>BIC:</b> GEBABEBB</li>
  				<li><b>Naam:</b> <?=_MAILFROMNAME?></li>
  				<li><b>Bedrag:</b> <?=$payment->amount?> EUR</li>
  				<li><b>Referentie:</b> <?=APP?> orderid <?=$payment->getId()?></li>
  			    </ul>
  			</div>
                    </div>
                </div>
                
                <!-- Submit button -->
                <button type="submit" class="btn btn-dark btn-block mt-4">Bevestig</button>
            </form>              
        </div>
<script>
function toggleWireTransfer() {
  var x = document.getElementById("wiretransfer");
  if(document.getElementById('wire').checked == true) { 
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}
</script>
    </body>
</html>
