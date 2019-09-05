<?php
    $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
    $query_manif = mysqli_query($conn,"SELECT m.nome,m.id_finveneto,a.nome as nome_atleta,g.stile,g.metri,g.categoria,sq.nome as nome_squadra FROM manifestazione m INNER JOIN gara g ON g.id_manifestazione = m.id INNER JOIN partecipazione p ON p.id_gara = g.id LEFT JOIN Risultato r ON p.id = r.id_partecipazione INNER JOIN atleta a ON a.id = p.id_atleta INNER JOIN squadra sq ON sq.id = a.id_squadra WHERE (data <= NOW() AND (r.tempo = '0' OR r.tempo IS NULL)) ORDER BY data DESC");
?>
<script>
function svuotaTutto(){
    $.ajax({
        url: "a_manifestazione.php",
        type: "post",
        data: "rimuovi_partecipazioni_senza_risultato=1",
        success: function (response) {
            // you will get response from your php page (what you echo or print)                 
            console.log(response);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
}
</script>
<table class='table table-striped'>
<thead class='thead-dark text-center'>
    <tr>
    <th colspan="10"><h1 class="h2">Partecipazioni senza risultato</h1></th>
</tr>
    <tr>
        <th>ID Manif</th>
        <th>Nome atleta</th>
        <th>Squadra</th>
        <th>Stile</th>
        <th>Metri</th>
        <th>Categoria</th>
    </tr>
    <tr>
    <th colspan="10"><a class="btn btn-primary w-60" onclick="svuotaTutto();">Svuota tutto</a></th>
</tr>
</thead>
<tbody class="text-center">
<?
    while($row = mysqli_fetch_assoc($query_manif)){
        ?>
        <tr>
            <td><? echo $row['id_finveneto']; ?></td>
            <td><? echo $row['nome_atleta']; ?></td>
            <td><? echo $row['nome_squadra']; ?></td>
            <td><? echo $row['stile']; ?></td>
            <td><? echo $row['metri']; ?></td>
            <td><? echo $row['categoria']; ?></td>
        </tr>
        <?
    }
?>
</tbody>
</table>