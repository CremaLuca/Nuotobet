<?
    if(!isset($_SESSION['username'])){
        refresh(0,'/');
        exit;
    }
?>
<style>
.cambio-password{
    max-width:380px;
}
#input_old_password{
    margin-bottom:1rem;
}
#input_new_password{
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
}
#input_repeat_new_password{
    border-top-right-radius: 0;
    border-top-left-radius: 0;
}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/md5.js"></script>
<script>
 $(function() {
    $("#input_repeat_new_password").change(checkPassword);
});

function checkPassword(){
    var pass1 = $("#input_new_password").val();
    var pass2 = $("#input_repeat_new_password").val();
    if(pass1 != pass2){
        formAlert("Le nuove password non coincidono!",1500);
        return false;
    }
    return true;
}
function formAlert(text,time){
    $("#alert-box").fadeIn(400).html("<div class='alert alert-danger' role='alert'><i class='fa fa-exclamation-circle'></i>"+text+"</div>").delay(time).fadeOut(500);
}
function formSuccess(text,time){
    $("#alert-box").fadeIn(400).html("<div class='alert alert-success' role='alert'><i class='fa fa-exclamation-circle'></i>"+text+"</div>").delay(time).fadeOut(500);
}
function changePassword(){
    if(!checkPassword()){
        //ridondanza;
        formAlert("Le nuove password non coincidono!",1500);
        return false;
    }
    var new_password = CryptoJS.MD5($("#input_new_password").val());
    var old_password = CryptoJS.MD5($("#input_old_password").val());
    $.ajax({
        url: "a_recover_password.php",
        type: "post",
        data: "change=1&new_password="+new_password+"&old_password="+old_password,
        success: function(response){
            console.log(response);
            formSuccess(response,10000); //10 secondi
        },
        error: function(request, status, error) {
            formAlert(request.responseText,2500);
            console.log(error);
            console.log(status);
            console.log(request);
        }
    });
}
</script>

<?php
?>
<div class="d-flex flex-row flex-wrap m-0">
    <div class="cambio-password card shadow m-3">
    <div class="card-header">
        <h1 class="h4 mb-0 font-weight-bold mb-2">Cambio password</h1>
        <p class='muted text-muted pt-0 my-0'>Non ti piace la tua password? Cambiala qui</p>
    </div>
    <div class="card-body">
        <form method="post" class="rounded my-0 mx-auto" onsubmit="changePassword(); return false;">
            <div id='alert-box'></div>
            <label for="input_old_password" class="sr-only">Vecchia password</label>
            <input type="password" id="input_old_password" name="input_old_password" class="form-control" placeholder="Vecchia password" required autofocus>
            <label for="input_new_password" class="sr-only">Nuova password</label>
            <input type="password" id="input_new_password" name="input_new_password" autocomplete="new-password" class="form-control" placeholder="Nuova password" required>
            <label for="input_repeat_new_password" class="sr-only">Ripeti nuova password</label>
            <input type="password" id="input_repeat_new_password" name="input_repeat_new_password" autocomplete="new-password" class="form-control" placeholder="Ripeti nuova password" required>
            <button class="btn btn-primary mt-3" type="submit">Cambia password</a>
        </form>
        </div>
    </div>
</div>