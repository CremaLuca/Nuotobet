<style>
.form-recover {
    width: 100%;
    max-width: 330px;
    padding: 15px;
    margin: 0 auto;
    background-color:#f5f5f5;
}
.form-recover .checkbox {
    font-weight: 400;
}
.form-recover .form-control {
    position: relative;
    box-sizing: border-box;
    height: auto;
    padding: 10px;
    font-size: 16px;
}
.form-recover .form-control:focus {
    z-index: 2;
}
.form-recover input#inputUsername {
    margin-bottom: 10px;
}
</style>

<script>
    function recoverPassword(){
        var usermail = $("#inputUsername").val();
        $.ajax({
            url: "a_recover_password.php",
            type: "post",
            data: "recover=1&usermail="+usermail,
            success: function(response){
                console.log(response);
                formSuccess(response,10000); //10 secondi
            },
            error: function(request, status, error) {
                formAlert(request.responseText);
                console.log(error);
                console.log(status);
                console.log(request);
            }
        });
    }
</script>

<video autoplay muted loop class="video-bg">
  <source src="https://media3.giphy.com/media/xT0GqcCJJJH12hJvGM/giphy.mp4" type="video/mp4">
</video>

<form class="form-recover text-center rounded p-4" method="post" onsubmit="recoverPassword(); return false;">
    <h1 class="h3 mb-0 font-weight-normal">Recupera password</h1>
    <p class='muted text-muted pt-0 mt-0'>Recupera password perduta</p>
    <div id='alert-box'></div>
    <label for="inputUsername" class="sr-only">Username o e-mail</label>
    <input type="text" id="inputUsername" name="inputUsername" class="form-control" placeholder="Username oppure e-mail" required autofocus>
    <input type="text" name="username" class="d-none">
    <button class="btn btn-lg btn-primary btn-block" type="submit"> Recupera </button>
    <p class="pt-3 extra-buttons">
    <a class="btn btn-secondary text-white" onclick="changePage('landing_login')" role="button">Accedi</a>
    <a class="btn btn-secondary text-white" onclick="changePage('landing_registrazione')"  role="button">Registrati</a>
    </p>
</form>