<?php
  $useradmin = $_SESSION['is_admin'];
  if(!isset($useradmin) || $useradmin == false){
    refresh(0,'/');
    exit;
  }
  include_once("simple_html_dom.php");
  $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");

  $html = file_get_html("http://www.finveneto.org/");
  $html_table_manif = $html->find("div.principale div.col-C div.gare div#area1",0);
?>
<script>
  function OnIDManifestazione() {
    window.open(
      "/?p=admin&adminPage=iscrizione_gare&url=http://www.finveneto.org/nuoto_schedamanifestazione.php?id_manifestazione=" +
      $('#input_id_manifestazione').val());
  }
  function OnDataChange(id_manif,data){
    $.ajax({
      url: "a_manifestazione.php",
      type: "post",
      data: "modifica_data=1&id_manif="+id_manif+"&data="+data,
      success: function (response) {
        console.log(response);
      },
      error: function (jqXHR, textStatus, errorThrown) {
          console.log(jqXHR.responseText);
      }
    });
  }
  function OnNomeChange(id_manif,nome){
    $.ajax({
      url: "a_manifestazione.php",
      type: "post",
      data: "modifica_nome=1&id_manif="+id_manif+"&nome="+nome,
      success: function (response) {
        console.log(response);
      },
      error: function (jqXHR, textStatus, errorThrown) {
          console.log(jqXHR.responseText);
      }
    });
  }
  function OnLuogoChange(id_manif,luogo){
    $.ajax({
      url: "a_manifestazione.php",
      type: "post",
      data: "modifica_luogo=1&id_manif="+id_manif+"&luogo="+luogo,
      success: function (response) {
        console.log(response);
      },
      error: function (jqXHR, textStatus, errorThrown) {
          console.log(jqXHR.responseText);
      }
    });
  }
</script>
<table class="table">
  <thead class='thead-dark text-center'>
    <tr>
    <th colspan='4'><h2>Importa manifestazioni</h2></th>
    </tr>
    <tr>
    <th>Data</th>
    <th>Nome manifestazione</th>
    <th></th>
    <th></th>
    </tr>
  </thead>
  <tbody>
    <?
  foreach($html_table_manif->find("div.garepross-1 a, div.garepross-2 a") as $element){
    $finveneto_link = "http://www.finveneto.org/$element->href";
    $parti = parse_url($element->href, PHP_URL_QUERY);
    parse_str($parti, $query_url_manif);
    $id_finveneto_manifestazione = $query_url_manif['id_manifestazione'];
    $link_manif = "/?p=admin&adminPage=iscrizione_gare&is_fresh=1&url=http://www.finveneto.org/$element->href";
    $data_manif = substr($element->innertext,0,10);
    $nome_manif = substr($element->innertext,11);
    echo "<tr><td>$data_manif</td><td>$nome_manif</td><td class='text-center'><a href='$link_manif' class='btn btn-sm btn-dark'>Importa</td><td class='text-center'><a href='$finveneto_link' class='btn btn-sm btn-dark' target='_blank'>Visualizza su finveneto</a></td></tr>";
    //Controlliamo se la abbiamo già nel db
    $query_e_manif = mysqli_query($conn,"SELECT id,nome,data,luogo FROM manifestazione WHERE id_finveneto='$id_finveneto_manifestazione' LIMIT 1");
    echo mysqli_error($conn);
    if(mysqli_num_rows($query_e_manif) > 0){
      $db_manif = mysqli_fetch_assoc($query_e_manif);
      $m_nome = $db_manif['nome'];
      $m_id = $db_manif['id'];
      $m_data= $db_manif['data'];
      $m_luogo = $db_manif['luogo'];
      $datetime_input = strftime('%Y-%m-%dT%H:%M:%S', strtotime($m_data));
      ?>
      <tr>
        <td class='border-0'><input type='datetime-local' class='form-control' value="<?=$datetime_input?>" name='m_datetime' onchange='OnDataChange(<?=$m_id?>,this.value);' /></td>
        <td class='border-0'><input type='text' class='form-control' value="<?=$m_nome?>" placeholder='Nome manifestazione' onchange='OnNomeChange(<?=$m_id?>,this.value);'></td>
        <td class='border-0' colspan='2'><input type='text' class='form-control' value='<?=$m_luogo?>' placeholder='Luogo manifestazione' onchange='OnLuogoChange(<?=$m_id?>,this.value);'></td>
      </tr>
      <?
    }
  }
?>
  </tbody>
</table>

<div class='p-2'>
  <input type='text' id='input_id_manifestazione' placeholder='ID manifestazione'>
  <button class='btn btn-primary' onclick='OnIDManifestazione();'>Invia</button>
</div>