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
                <p class="text-center"><b>Payment method</b></p>
                <p> </p>
            </div>
        </div>
        <div class="container mt-3">
            <h2 style="text-align:center">Your order is created.</h2>
            <form autocomplete="off" method="POST">
                <div class="card">
                    <div class="row m-5"
                        <p><b>Payment method:</b></p>
  			<input type="radio" id="online" name="paymentmethod" value="online" data-role="none"  onclick="toggleWireTransfer()" checked> I pay online<br>
  			<input type="radio" id="wire" name="paymentmethod" value="wire"  onclick="toggleWireTransfer()" data-role="none"> I wire the amount to the account of <?=_MAILFROMNAME?><br>
  			<div class="text-primary" id="wiretransfer" style="display:none;">
  			    For the wire transfer, please use following data:
  			    <ul>
  				<li><b>IBAN:</b> BE80 0019 4503 2377</li>
  				<li><b>BIC:</b> GEBABEBB</li>
  				<li><b>Name:</b> <?=_MAILFROMNAME?></li>
  				<li><b>Amount:</b> <?=$payment->amount?> EUR</li>
  				<li><b>Reference:</b> <?=APP?> orderid <?=$payment->getId()?></li>
  			    </ul>
  			</div>
                    </div>
                </div>
                
                <!-- Submit button -->
                <button type="submit" class="btn btn-dark btn-block mt-4">Confirm</button>
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
