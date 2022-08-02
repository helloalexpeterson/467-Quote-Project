<?php    
session_start(['name' => 'quotes']); 
//This page is for an associate to create a new quote
//Make sure user has permissions
if(!(isset($_SESSION['userType']) && $_SESSION['userType'] =='Sales Associate')){
    header("Location: login.php", 303);
}
$pagetitle = "Create a new quote";
include 'header.php'; 
include '../lib/func.php';
include '../lib/db.php';

?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mt-3">
                    <div class="card-header">    
                        <h4 class="mb-3">Create a new quote</h4>
                    </div>
                        <div class="card-body">
                            <form action='quoteTemplate.php' method='POST'>
                                <div class="form-group row">
                                    <label for='id' class="col-md-6 col-form-label text-md-right"><b>Select a customer:</b></label>
                                    <div class="col-md-6">
                                        <select id='id' name='id' required class="form-control" >
                                            <option value='' disabled selected>Choose one</option>
<?php 
//connect to our db and ege db
$pdo = connectlegacy();
$mydb = connectdb();
        $sql = "SELECT id, name FROM customers";
        $result = $pdo->query($sql);
        $row = $result->fetchAll(PDO::FETCH_ASSOC);
        $custCount = 0;
                // Iterating through the array of customers
                foreach($row as $customer => $index){
                    $custCount++;
                    echo "                                    <option value={$index['id']}>{$index['name']}</option>\n";
            }
?>
                                        </select>     
                                    </div>
                                </div> 
        <div class="form-group row ">
            <div class="row justify-content-end "><?php echo $custCount; ?> total customers</div>                              
        
            <label for='email' class="col-md-6 col-form-label text-md-right"><b>Input the customer's email to begin quote:</b></label>
                            
            <div class="col-md-6 p-1"> <input type='email' name='email' required required class="form-control" placeholder="Email"> </div>  
        </div>
        
        <div class="form-group row justify-content-center mt-3 ">
             <div class="col-md-4 "> <input type='submit' name='newquote' value='Create New Quote' required class="form-control btn btn-primary"></div>  
             </div>  
             
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
