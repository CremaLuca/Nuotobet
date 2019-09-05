<style>
.form-signin {
    width: 100%;
    max-width: 330px;
    padding: 15px;
    margin: 0 auto;
    background-color:#f5f5f5;
}
.form-signin .checkbox {
    font-weight: 400;
}
.form-signin .form-control {
    position: relative;
    box-sizing: border-box;
    height: auto;
    padding: 10px;
    font-size: 16px;
}
.form-signin .form-control:focus {
    z-index: 2;
}
.form-signin input[type="text"] {
    margin-bottom: -1px;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
}
.form-signin input[type="password"] {
    margin-bottom: 10px;
    border-top-left-radius: 0;
    border-top-right-radius: 0;
}
</style>

<video autoplay muted loop class="video-bg">
    <source src="http://giant.gfycat.com/CandidAngelicHarrier.webm" type="video/webm">
</video>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/md5.js"></script>
<script>
function formCheck(){
    //Controlliamo che l'utente esista
    var username = $("#inputUsername").val();
    var md5password = CryptoJS.MD5($("#inputPassword").val());
    $.ajax({
        url: "a_login.php",
        type: "post",
        data: "login=1&username="+username+"&password="+md5password,
        success: function(response){
            console.log(response);
            window.location.href = "/?";
        },
        error: function(request, status, error) {
            formAlert(request.responseText);
        }
    });
}
</script>

<form class="form-signin text-center rounded p-4" method="post" onsubmit="formCheck(); return false;">
    <h1 class="h3 mb-0 font-weight-bold text-primary">NuotoBet</h1>
    <p class='muted text-muted pt-0 mt-0'>Accedi per scommettere</p>
    <div id='alert-box'></div>
    <label for="inputUsername" class="sr-only">Username</label>
    <input type="text" id="inputUsername" name="inputUsername" class="form-control" placeholder="Username" required autofocus>
    <label for="inputPassword" class="sr-only">Password</label>
    <input type="password" id="inputPassword" name="inputPassword" class="form-control" placeholder="Password" required>
    <button class="btn btn-lg btn-primary btn-block" type="submit"> Accedi </button>
    <p class="pt-3 extra-buttons">
        <a class="btn btn-secondary text-white" onclick="changePage('landing_registrazione')" role="button">Registrati</a>
        <a class="btn btn-secondary text-white" onclick="changePage('landing_recupero_password')"  role="button">Recupera password</a>
    </p>
</form>