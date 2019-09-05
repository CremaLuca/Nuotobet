<?
if(!isset($_GET['id_squadra'])){
    refresh(0,'/p=visual_manif');
    exit;
}
$id_squadra = $_GET['id_squadra'];
$conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");

$query_info_squadra = mysqli_query($conn,"SELECT nome FROM squadra WHERE id='$id_squadra' LIMIT 1");
$info_squadra = mysqli_fetch_assoc($query_info_squadra);
$nome_squadra = $info_squadra['nome'];

$query_atleti = mysqli_query($conn,"SELECT id,nome,anno FROM atleta WHERE id_squadra = '$id_squadra' ORDER BY anno,nome");
?>
<title>Nuotobet - <? echo $nome_squadra; ?></title>
<meta name="description" content="Lista degli atleti della squadra <? echo $nome_squadra; ?>" />
<table class='table'>
    <thead class='thead-dark'>
        <tr>
            <th colspan='2'>
            <h1 class='h2'><? echo $nome_squadra; ?></h1>
            </th>
        </tr>
        <tr>
            <th>Nome</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
<?
$last_anno = 0;
    while($atleta = mysqli_fetch_assoc($query_atleti)){
        if($atleta['anno'] != $last_anno){
            $last_anno = $atleta['anno'];
            echo "<tr><th colspan='2'><h1 class='h3'>$last_anno</h1></th></tr>";
        }
        echo "<tr><td>".retrieveAtletheName($atleta['nome'])."</td><td><a class='btn-sm btn-dark' href='/?p=visual_atleta&id_atleta=".$atleta['id']."'>Visualizza</a></td></tr>";
    }
?>
    </tbody>
</table>