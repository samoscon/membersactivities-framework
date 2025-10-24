<?php
$user = $request->get('user');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php'; ?>
        <title><?=$request->get('title')?></title>
    </head>
    <body>
        <div class="offcanvas offcanvas-start" style="width: 270px;" id="Menu">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title"> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                <div class="list-group list-group-flush">
                    <!--<a class="list-group-item list-group-item-action" href="sendMailMembers"><i class="fa fa-envelope"><span class="ff"> Mail alle bezoekers</span></i></a>-->
                    <a class="list-group-item list-group-item-action" 
                       href="searchMembers"><i class="fa fa-address-book"><span class="ff"> <?=$request->get('labelMembersMenu')?></span></i>
                    </a>
                    <div class="dropdown list-group-flush m-6">
                    <a class="list-group-item dropdown-toggle" id="Downloads" data-bs-toggle="dropdown"><i class="fa fa-map"><span class="ff"> Downloads</span></i></a>
                    <ul class="dropdown-menu"  style="border-style: none; width: 290px;"aria-labelledby="#Downloads">
                      <li>
                         <a class="dropdown-item ff" href="xlsMembers" target="_blank"><i class="fa fa-cog">
                            Bezoekersbestand
                            </i></a>
                      </li>
                    </ul>
                    </div>
                    <a class="list-group-item list-group-item-action" href="logout"><i class="fa fa-sign-out"><span class="ff"> Logout</span></i></a>
                </div>
            </div>
        </div>
        <div class="container-fluid p-3 bg-secondary text-light">
            <div class="d-flex w-100 justify-content-between">
                <button class="btn btn-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#Menu">
                    <i class="fa fa-bars"></i>
                </button>
                <b><?=$request->get('title')?></b>
                <p> </p>
            </div>
        </div>
        <div class="container mt-3">
            <form autocomplete="off" method="POST">
                <div class="row">
                    <h5 class="text-primary">
                        <?=$request->get('labelActivitiesList')?>  <a class="btn btn-dark btn-sm" title="Nieuwe activiteit" 
                                      href="createActivity" 
                                      role="button" style="max-height: 32px"><i class="fa fa-plus"></i></a>
                    </h5>
                </div>
                <div class="row">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                          <th class="bg-primary text-white">Omschrijving</th>
                        </tr>
                      </thead>
                      <tbody id="myTable">
                          <?php foreach ($request->get('activities') as $activity) : 
                              $color = $activity->isComposite() ? 'text-primary' : '';
                              if ($activity->isComposite()) {
                                  if ($activity->parent_id) {
                                      $description = '<b>--> '.$activity->description.'</b>';
                                  } else {
                                      $description = '<b>'.$activity->description.'</b>';
                                  }
                              } else {
                                  $description = date('d/m/Y', strtotime($activity->date)) . ' - '.$activity->description;
                              }                               
                          ?>
                            <tr>
                              <td>
                                  <a href="editActivity?id=<?=$activity->getId()?>" class="<?=$color?> list-group-item list-group-item-action">
                                      <?=$description?>
                                  </a>                                
                              </td>
                            </tr>
                          <?php endforeach;?>
                    </table>
                </div>
            </form>              
        </div>
    </body>
</html>
