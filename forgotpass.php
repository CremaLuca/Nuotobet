<?php
	error_reporting(E_ALL);
    $useradmin = $_SESSION['is_admin'];
?>
    	<?php
        	if(!$_SESSION['username'] && !$_SESSION['id_utente'])
            {
            	if($_POST['resetbtn'])
                {
                	$user = $_POST['user'];
					$email = $_POST['email'];
                    if($user)
                    {
                    	if($email)
                    	{
                        	if((strlen($email) > 7) && (strstr($email, "@")) && (strstr($email, ".")))
                    		{
                            	$conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
                                
                                $query = mysqli_query($conn, "SELECT * FROM Utente WHERE username = '$user'");
                                $numrows = mysqli_num_rows($query);
                                if($numrows == 1)
                                {
                                	#prendi info sull'acoount
                                    $row = mysqli_fetch_assoc($query);
                        	        $dbemail = $row['email'];
                                    
                                    #controllo se email è corretta
                                    if($email == $dbemail)
                                    {
                                    	#generare una random password
                                        $pass = rand();
                                        $pass = md5($pass);
                                    	$pass = substr($pass, 0, 10);
                                        
                                        #update database con nuova pass
                                        mysqli_query($conn, "UPDATE Utente SET password='$pass' WHERE username='$user'" );
                                        
                                        $query = mysqli_query($conn, "SELECT * FROM NuotoBet WHERE username='$user' AND password='$pass'");
                                        $numrows = mysqli_num_rows($query);
                                        if($numrows == 1)
                                        {
                                        	$webmaster = "nuotobetcompany@gmail.com";
                                            $headers = "From: NuotoBet<$webmaster>";
                                            $subject = "La tua nuova password";
                                            $message = "La tua password e' la seguente.  ";
                                            $message .= "Password:  $pass\n";
                                            
                                            if( mail($email, $subject, $message, $headers) )
                                            {
                                            	
                                                echo "La tua password e' stata resettata. Controlla la tua mail, lì troverai la tua nuova password.";
                                                
                                            }
                                            else
                                            	{echo "Un errore non ha permesso l'invio della mail";}
									   }
                                        else
                                        	{echo "ERRORE, la password non e' stata creata";}
                                        
                                    }
                                    else
                                    	{echo "L'email non e' valida, non è stata trovata nei nostri database.";}
                                }
                                else
                                	{echo "L'username non è' valido, non è stato trovato nei nostri database.";}

                                mysqli_close($conn);
           
                    		}else
                    			{echo "Immetti una mail valida";}
                    	}
                    	else
                    		{echo "Inserisci mail";}
                    }
                    else
                    	{echo "Inserisci username";}
                  
                }
                
                echo "<form action='./index.php?p=forgotpass' method='post'>
                	<table>
                    	<tr>
                        	<td>Username:</td>
                            <td><input type='text' name='user' /</td>
                        </tr>
                    	<tr>
                        	<td>Email:</td>
                            <td><input type='text' name='email' /</td>
                        </tr>
                        <tr>
                        	<td></td>
                            <td><input type='submit' name='resetbtn' value='Reset Password' /</td>
                        </tr>
                    </table>
                    </form>";
            }
            else
            	{echo "Fai il logout.";};
        ?>