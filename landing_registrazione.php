<style>
.form-register {
    width: 100%;
    max-width: 330px;
    padding: 15px;
    margin: 0 auto;
    background-color:#f5f5f5;
}
.form-register .checkbox {
    font-weight: 400;
}
.form-register .form-control {
    position: relative;
    box-sizing: border-box;
    height: auto;
    padding: 10px;
    font-size: 16px;
}
.form-register .form-control:focus {
    z-index: 2;
}
.form-register input#inputUsername {
    margin-bottom: -1px;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
}
.form-register input#inputRipetiPassword {
    margin-bottom: -1px;
    border-top-right-radius: 0;
    border-top-left-radius: 0;
    margin-bottom: 10px;
}
</style>

<script>
    $(function() {
        $("#inputUsername").change(checkUsername);
    });
    function formCheck(){
        //Controlliamo che l'utente esista
        var username = $("#inputUsername").val();
        var password1 = $("#inputPassword").val();
        var password2 = $("#inputRipetiPassword").val();
        var email = $("#inputEmail").val();
        if(password1 == password2){
            var md5password = CryptoJS.MD5(password1);
            $.ajax({
                url: "a_register.php",
                type: "post",
                data: "register=1&username="+username+"&password="+md5password+"&email="+email,
                success: function(response){
                    console.log(response);
                    window.location.href = "/?";
                },
                error: function(request, status, error) {
                    formAlert(request.responseText);
                }
            });
        }else{
            formAlert("Le password non coincidono");
        }
    }
    function checkUsername(){
        var username = $("#inputUsername").val();
        $.ajax({
            url: "a_register.php",
            type: "post",
            data: "e_username=1&username="+username,
            success: function(response){
                console.log(response);
            },
            error: function(request, status, error) {
                formAlert(request.responseText);
            }
        });
    }
</script>

<video autoplay muted loop class="video-bg">
  <source src="https://media.giphy.com/media/26BRBupa6nRXMGBP2/giphy.mp4" type="video/mp4">
</video>

<form class="form-register text-center rounded p-4" method="post" onsubmit="formCheck(); return false;">
    <h1 class="h3 mb-0 font-weight-normal">Registrati</h1>
    <p class='muted text-muted pt-0 mt-0'>Crea un nuovo account di Nuotobet</p>
    <div id='alert-box'></div>
    <label for="inputUsername" class="sr-only">Username</label>
    <input type="text" id="inputUsername" name="inputUsername" pattern=".{4,}" class="form-control" value="<? if(isset($_POST['inputUsername'])) echo $_POST['inputUsername']; ?>" placeholder="Username" required autofocus>
    <label for="inputEmail" class="sr-only">E-mail</label>
    <input type="email" id="inputEmail" name="inputEmail" autocomplete="email" class="form-control rounded-0" value="<? if(isset($_POST['inputEmail'])) echo $_POST['inputEmail']; ?>" placeholder="E-mail (opzionale)">
    <label for="inputPassword" class="sr-only">Password</label>
    <input type="password" id="inputPassword" name="inputPassword" pattern=".{4,}" class="form-control mb-0 rounded-0" value="<? if(isset($_POST['inputPassword'])) echo $_POST['inputPassword']; ?>" placeholder="Password" required>
    <label for="inputRipetiPassword" class="sr-only">Ripeti password</label>
    <input type="password" id="inputRipetiPassword" name="inputRipetiPassword" pattern=".{4,}" class="form-control" value="<? if(isset($_POST['inputRipetiPassword'])) echo $_POST['inputRipetiPassword']; ?>" placeholder="Ripeti password" required>
    <input type="text" name="username" class="d-none">
    <button class="btn btn-lg btn-primary btn-block" type="submit"> Registrati </button>
    <p class="pt-3 extra-buttons">
    <a class="btn btn-secondary text-white" onclick="changePage('landing_login')" role="button">Accedi</a>
    <a class="btn btn-secondary text-white" onclick="changePage('landing_recupero_password')"  role="button">Recupera password</a>
    </p>
</form>