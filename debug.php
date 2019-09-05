<?php
$conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
$query_res = mysqli_query($conn,"SELECT a.nome, a.id FROM atleta a");
echo mysqli_error($conn);
while($atleta = mysqli_fetch_assoc($query_res)){
    $new_name = translateAthleteName(mysqli_real_escape_string($conn,$atleta['nome']));
    $id = $atleta['id'];
    mysqli_query($conn,"UPDATE Atleta SET nome='$new_name' WHERE id='$id' ");
}
?>