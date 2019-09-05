<?
function get_email_template($username,$password){
    if(isset($username) && isset($password)){
$mail_template = <<<MAIL
        <html>
        <head>
        </head>
        <body style="padding:0px; margin:0px; font-size:1rem; font-family:'Segoe UI',Roboto,'Helvetica Neue',Arial,'Noto Sans',sans-serif;">
            <div class="container" style="margin-left:2rem;margin-right:2rem;">
                <div class="header" style="display:flex; justify-content:start; background-color: #343a40; padding:1rem;"> 
                    <a href="nuotobet.altervista.org" style="color:#FFF; text-decoration:none;"><h3>Nuotobet</h3></a>
                </div>
                <div class="content" style="display:block; padding:1rem;">
                    <h2>Recupera password di Nuotobet</h2>
                    <p>
                        Sembra che tu ti sia dimenticato la password dell'account di Nuotobet, $username, e che tu stia cercando di recuperarla.
                        Non ti daremo la tua vecchia password poichè non la sappiamo neanche noi, però te ne possiamo dare una nuova
                    </p>
                    <div class="pre" style="display:inline-block; font-weight:bold; padding-top:1rem; padding-bottom:1rem; padding-left:3rem; padding-right:3rem; background-color:#FFF9C4;">
                        <pre>$password</pre>
                    </div>
                    <p>
                        Custodiscila con cura questa volta, e ricordati che puoi cambiarla nella pagina utente del sito, sempre che questa non ti piaccia abbastanza.
                    </p>
                </div>
                <div class="footer" style="display:block; color:#FFF; background-color: #343a40; padding-left:1rem; padding-right:1rem; padding-top:5px; padding-bottom:5px;">
                    Nuotobet 2019 ©
                </div>
            </div>
        </body>
        </html>
MAIL;

        return $mail_template;
    }else{
        return "Errore nella funzione get_email_template, mancano i parametri";
    }
}
?>