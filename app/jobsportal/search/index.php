<?php

  session_start();

  include_once('../../include/config.php');
  
  $index = "../people";
  $logout_path = "../login/logout.php";         
  
  
$skills = strtoupper($_GET['skl']);
$loc = strtoupper($_GET['loc']);

if ( !$skills || !$loc ){}
else{

$submitRes = $people = "";
 
if($skills == "ALL" AND $loc=="ALL"){
    
    $people = $connection->query("SELECT jobseeker.NAMES, jobseeker.GENDER,jobseeker.EMAIL, certificates.NAME, jobseeker.EMAIL, jobseeker.AGE, MAX(certificates.NAME) "
            . "FROM jobseeker "
            . "JOIN certificates "
            . "ON jobseeker.EMAIL=certificates.PERSON "
            . "GROUP BY jobseeker.NAMES");
    }elseif( $skills == "ALL" ){
            $people = $connection->query("SELECT jobseeker.NAMES, jobseeker.GENDER,jobseeker.EMAIL, certificates.NAME, jobseeker.EMAIL, jobseeker.AGE, MAX(certificates.NAME) "
            . "FROM jobseeker "
            . "JOIN certificates "
            . "ON jobseeker.EMAIL=certificates.PERSON "
            . "WHERE jobseeker.RESIDENTIAL LIKE '%$loc%'"
            . "GROUP BY jobseeker.NAMES"); 
        
    }elseif( $loc == "ALL" ){
            $people = $connection->query("SELECT * "
                    . "FROM ( "
                    . "SELECT skills.SNAME, skills.PERSON, certificates.NAME "
                    . "FROM skills "
                    . "JOIN certificates "
                    . "ON skills.PERSON=certificates.PERSON "
                    . "WHERE skills.SNAME LIKE '%$skills%'"
                    . ") AS T1 "
                    . "JOIN jobseeker ON T1.PERSON=jobseeker.EMAIL GROUP BY jobseeker.NAMES"); 
    }else{
            $people = $connection->query("SELECT * "
                    . "FROM ( "
                    . "SELECT skills.SNAME, skills.PERSON, certificates.NAME "
                    . "FROM skills "
                    . "JOIN certificates "
                    . "ON skills.PERSON=certificates.PERSON "
                    . "WHERE skills.SNAME LIKE '%$skills%'"
                    . ") AS T1 "
                    . "JOIN jobseeker AS T2 ON T1.PERSON=T2.EMAIL "
                    . "WHERE T2.RESIDENTIAL LIKE '%$loc%'"
                    . "GROUP BY T2.NAMES"); 
    }

?>
  
<!DOCTYPE html> 
<html lang="en">
    <head>
        <title>Candidate Search</title>
        <?php require '../commons/head.php'; ?>
    </head>    
  <body>

  <section id="container" >
      <!-- **********************************************************************************************************************************************************
      TOP BAR CONTENT & NOTIFICATIONS
      *********************************************************************************************************************************************************** -->
      <!--header start-->
      <?php require '../commons/header.php'; ?>
      <!--header end-->
      
      <!-- **********************************************************************************************************************************************************
      MAIN SIDEBAR MENU
      *********************************************************************************************************************************************************** -->
      <!--sidebar start-->
      <aside>
          <?php require '../commons/aside.php'; ?>          
      </aside>
      <!--sidebar end-->
      
      <!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->
      <!--main content start-->
      <section id="main-content">
          <section class="wrapper">
              <div class="row">
                  <div class="col-lg-11 animated bounceIn">
                      <br />
                      <hr />
                      <div class="panel panel-primary panel-hovered panel-stacked mb30" >
                          <div class="panel-body">
                              <table class="table" id="table_vivio">
                                  <thead> 
                                      <?php if($people->num_rows == 0){ ?>
                                      <tr>
                                          <td>
                                              <h4 class="panel-title text-center text-capitalize">
                                                  <strong>
                                                      Oops! We couldn't find anything. <br /> <br />
                                                      Please try again. Use "all" on both fileds to find all people.
                                                  </strong>
                                              </h4>
                                          </td>
                                      </tr>
                                  </thead>
                              </table>
                                  
                                  <?php }else{ ?>
                              <tr>       
                                  <td class="text-center col-md-3">
                                      <strong>NAMES</strong>
                                  </td>
                                <td class="text-center text-uppercase col-md-4">
                                    <strong>Qualifications</strong>
                                </td>
                                <td class="text-center col-md-2">
                                    <strong>GENDER</strong>
                                </td>
                                <td class="text-center col-md-2">
                                    <strong>AGE</strong>
                                </td>
                                <td class="text-center col-md-1" id="search_id">

                                </td>
                            </tr>
                        </thead>
                        <tbody> 

            <?php
            foreach ($people as $person) { 
                $personEmail = $person['EMAIL'];
                ?>
            <tr class="alert alert-info text-uppercase text-center hvr-grow" style="margin: 10px">
                <td class="col-md-3">
                    <div>
                        <strong><?php echo $person['NAMES'];?></strong>
                    </div>
                </td>
                <td class="col-md-4">
                    <div>
                        <strong><?php echo $person['NAME'];?></strong>
                    </div>
                </td>
                <td class="col-md-2">
                    <div>
                        <strong><?php echo $person['GENDER'];?></strong>
                    </div>
                </td>
                <td class="col-md-2">
                    <div>
                        <strong><?php echo $person['AGE'];?></strong>
                    </div>
                </td>
                <td class="col-md-1">
                    <div>
                        <a data-toggle="modal" data-target="#_person" href="modal_person.php?id=<?php echo $personEmail?>" class="list-group-item active">VIEW</a>
                    </div>

                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>


                    </div>
                      <?php } ?>
                </div>
                  </div>
              
              </div><! --/row -->
          </section>
          
          
          
        <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="_person" class="modal fade">    
            <div class="modal-dialog animated bounceIn" role="document">
                <div class="modal-content" >
                </div>
            </div>
        </div>
          
          
      </section>
      <!--main content end-->      
      
      <!--footer start-->
          <?php require '../commons/footer.php'; ?>
      <!--footer end-->
  </section>
      
      <!-- Import JS -->
      <?php require '../commons/js_1.php'; ?>
      
      
      <!--script for this page-->
      <script type="text/javascript" src="../../assets/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
    /* table tag 1*/
    $(document).ready(function() {
          $("#table_vivio").DataTable();
          $("#search_id").append($("#table_vivio_filter"));
          $("input").attr('placeholder','Search Keyword');
          $("#table_vivio_length").remove();
          
        $('#_person').on('hidden.bs.modal', function () {
            $(this).removeData('bs.modal');
        });
    } );
    </script>
    
    
      <script>
      
        $(function(){
              $('select.styled').customSelect();
          });
                
      </script>
      
  </body>
</html>
<?php }