<?php
include("include/head.php");
include("include/header.php");
include("include/nav.php");
?>


<script language="JavaScript">
    function setVisibility(id, visibility) {
        document.getElementById(id).style.display = visibility;
    }
</script>


<div class="container contain-bg">
    <div class="col-sm-12">
        <div class="pageicon">
            <!--<h5><i class="fa fa-graduation-cap fa-5x" aria-hidden="true"></i></h5>-->
            <h4><strong>DISCRIPTION OF RESEARCH WORK</strong></h4><br />
        </div>
        <div class="text-right pb-1">
            <a href="stu_board.php" type="submit" class="btn btn-primary">Close <i class="fa fa-times" aria-hidden="true"></i></a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" width="100%">
                <thead>
                    <tr>
                        <th width="10%"></th>
                        <th width="50%" rowspan="2" class="align-middle">Title of Project/ Thesis</th>
                        <th width="10%" class="align-middle">Year</th>
                        <th width="30%" class="align-middle">Institution</th>
                        
                </thead>
                <tbody>
                    <tr>
                        <td>
                          <!--  <a class="btn btn-primary updateEdu" href="javascript:;" eduId='401' ><i class="fa fa-pencil-square-o fa-2x" aria-hidden="true"></i></a>-->
                            <a href="javascript:;" class="btn btn-default btn-sm updateEdu" eduId='29820'>
                            <i class="fa fa-pencil-square-o" aria-hidden="true">
                            </i>&nbsp; Edit
                            </a>
                            <a href="javascript:;" class="btn btn-default btn-sm delEdu" eduId='29820'>
                            <i class="fa fa-trash-o" aria-hidden="true">
                            </i>&nbsp; Delete
                            </a>
                        </td>
                        <td>1</td>
                        <td>2</td>
                        <td>3</td>
                        
                     </tr>
                </tbody>    
            </table>                       
        </div>

        <form>
            <div class="form-group row">
                <div class="col-sm-12 col-md-12">
                    <input type="button" name="" value="+" class="btn btn-dark btn-lg float-right plus" onclick="setVisibility('acRecord', 'inline');";>
                </div>
            </div>
            <div id="acRecord">
                <div class="form-group row">
                    <label for="" class="col-sm-12 text-left required">Title of Project/ Thesis</label>
                    <div class="col-md-12 col-sm-12">
                        <input type="text" class="form-control" id="researchTitle" name="researchTitle" placeholder="">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="col-sm-12 text-left required">Year</label>
                    <div class="col-md-4 col-sm-12">
                        <select class="custom-select" id="researchYear" name="researchYear">
                            <option value="">Select a Year</option>
                            <?php
                            for ($x = date("Y"); $x > 1900; $x--) {
                                echo "<option value=" . $x . ">" . $x . "</option>";
                            }
                            ?> 
                        </select>
                    </div>
                </div> 
                 <div class="form-group row">
                    <label for="" class="col-sm-12 text-left required">Institution</label>
                    <div class="col-md-4 col-sm-12">
                        <input type="text" class="form-control" id="institution" name="institution" placeholder="">
                    </div>
                </div>
                  
                <div class="form-group row">
                    <div class="col-sm-12">
                        <a href="academics.php" type="submit" class="btn btn-block btn-primary btn-lg">Submit</a>
                    </div>
                </div>

            </div>
        </form>
        <br />
        
        	<div class="form-group row">
                    <label for="career" class="col-sm-12 text-left required"><strong>Career Objectives</strong></label>
                    <div class="col-md-12 col-sm-12">
                    	Please outline your career objectives and how this degree will help you to achieve them.
                        <textarea type="text" class="form-control" rows="5" id="career" name="career" placeholder=""></textarea>
                    </div>
                </div>
		<div class="form-group row">
                    <label for="gist" class="col-sm-12 text-left required"><strong>Gist of Research</strong></label>
                    <div class="col-md-12 col-sm-12">
                    	Please describe the gist of your research problem with respect to your area of interest. Your response should
be focused and concise.
                        <textarea type="text" class="form-control" rows="5" id="gist" name="gist" placeholder=""></textarea>
                    </div>
                </div>
        
        
        
        
    </div>        
</div>



<?php

include("include/foot.php");
include("include/footer.php");
?>
    


