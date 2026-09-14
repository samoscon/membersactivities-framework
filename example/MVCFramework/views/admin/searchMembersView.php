<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php'; ?>
        <title>Bezoekers</title>
    </head>
    <body>
    	<div class="container-fluid p-3 bg-secondary text-light">
    	    <div class="d-flex w-100 justify-content-between">
		<a class="btn btn-dark btn-sm" href="admin" role="button" style="max-height: 32px"><i class="fa fa-calendar"></i></a>
		<p class="text-center"><b>Bezoekers</b></p>
		<p> </p>
	    </div>
    	</div>
	<div class="container mt-3 ff">
	  <div class="row">
		<div class="container">
                  <h5 class="text-primary mt-3">
                    Bezoekers <a class="btn btn-dark btn-sm" title="Nieuw lid" 
                                  href="createMember" 
                                  role="button" style="max-height: 32px"><i class="fa fa-plus"></i></a>
                  </h5>
                  <input class="form-control" id="myInput" type="text" placeholder="Zoek.." autofocus>
		  <br>
		  <table class="table table-bordered table-hover">
		    <thead>
		      <tr>
			<th>Naam - email</th>
		      </tr>
		    </thead>
		    <tbody id="myTable">
                        <?php foreach ($request->get('members') as $member) : 
                          $color = $member->isComposite() ? 'text-primary' : '';
                          $friend ='';
                          if($member->parent_id === 3) {
                              $friend = ' (friend)';
                              $color = 'text-success';
                          }
                          ?>
                          <tr>
                            <td>
                                <a href="editMember?id=<?=$member->getId()?>" class="<?=$color?> list-group-item list-group-item-action">
                                    <?=$member->name .' - '.$member->email . $friend?>
                                </a>                                
                            </td>
                          </tr>
                        <?php endforeach;?>
		    </tbody>
		  </table>
		</div>
	  </div>
	</div>
        <!-- Search script -->	
        <?php echo'<script src="' . _APPDIR . 'MVCFramework/views/includes/js/search.js"></script>' ?>
    </body>
</html>