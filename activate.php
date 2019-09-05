<?php
	error_reporting(E_ALL);
    $getuser = $_GET['user'];
    $getcode = $_GET['code'];
?>
		<?php
        	if($_POST['activatebtn']){
            	$getuser = $_POST['user'];
                $getcode = $_POST['code'];
                if($getuser){
                	if($getcode){
                    	$conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
                        
                        $query=mysqli_query($conn, "SELECT * FROM NuotoBet WHERE username='$getuser'");
                        $numrows=mysqli_num_rows($query);
                        if($numrows == 1){
                        	$numrows=mysqli_fetch_assoc($query);
                            $dbcode = $numrows['code'];
                            $dbactive = $numrows['active'];
                            	if($dbactive == 0){
                                	if($dbcode == $getcode){
                                    	mysqli_query($conn, "UPDATE NuotoBet SET active='1' WHERE username='$getuser'");
                                        $query=mysqli_query($conn, "SELECT * FROM NuotoBet WHERE username='$getuser' AND active='1'");
                                        $numrows = mysqli_num_rows($query);
                                        if($numrows == 1){
                                        	$errormsg = "Sei stato attivato con successo!";
                                        	$getuser="";
                                            $getcode="";
                                        }else
                                        	$errormsg = "Errore, l'account non e' stato attivato";
                                    }
                                    else
                                    	$errormsg = "Il codice non e' corretto, il tuo codice: $dbcode il codice che hai messo:$getcode";
                                        
                                }
                                else
                                	$errormsg = "L'utente e' gia attivo!";
                        }
                        else
                            $errormsg = "Quel nome utente non esiste!";
                    }
                    else
                    	$erromsg = "Devi mettere un codice!";
                }else
                	$errormsg = "Devi immettere il nome!";
                   
            }
            else
            	$errormsg = "Attiva il tuo account";
        
        	$form = "<form action='index.php?p=activate' method='post'>
            	<table>
                 <tr>
                	<td>Console:</td>
                    <td>$errormsg</td>
                </tr>
                <tr>
                	<td>Username:</td>
                    <td><input type='text' name='user' value='$getuser'/></td>
                </tr>
                <tr>
                	<td>Codice:</td>
                    <td><input type='text' name='code' value='$getcode'/></td>
                </tr>
                <tr>
                	<td>Pigia:</td>
                    <td><input type='submit' name='activatebtn' value='Attivati!'/></td>
                </tr>
                </table>
            </from>";
            
            echo $form;
        ?>