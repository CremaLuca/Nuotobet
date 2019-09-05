<?
    $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
    $query_utenti = mysqli_query($conn,"SELECT crediti,username FROM Utente ORDER BY crediti DESC LIMIT 15");
?>
<table class='table text-center'>
    <thead class='thead-dark'>
        <tr>
            <th colspan='5'><h1 class='h2'>Classifica utenti per crediti</h1></th>
        </tr>
        <tr>
            <th>Posizione</th>
            <th>Utente</th>
            <th>Crediti</th>
        </tr>
    </thead>
    <tbody>
    <?
        $i=0;
        while($utente = mysqli_fetch_assoc($query_utenti)){
            $i++;
            $color = "#FFF";
            $text_color = "#000";
            switch($i){
                case 1:
                    $color = "#D6AF36";
                break;
                case 2:
                    $color = "#D7D7D7";
                break;
                case 3:
                    $color = "#A77044";
                    $text_color = "#FFF";
                break;
            }
    ?>
        <tr class="font-weight-bold" style="background-color:<?= $color?>;color:<?= $text_color ?>"><td><?= $i ?></td><td><?= $utente['username'] ?></td><td><?= $utente['crediti'] ?></td></tr>
    <?
        }
    ?>
    </tbody>
</table>