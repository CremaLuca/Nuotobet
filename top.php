<?php
	error_reporting(E_ALL ^ E_NOTICE);
    $useradmin = $_SESSION['is_admin'];
    $usermoney = $_SESSION['crediti'];
?>
<style>
    .top {
        height: 34px;
        width: 100%;
        background-color: #DDDDDD;
    }

    .toptab {
        float: right;
    }
</style>
<div class="sticky-top top px-2">
    <?php
    	if($_SESSION['username'] && $_SESSION['id_utente']){
    ?>

    <table class="toptab">
        <tr>
            <td>
                <?php
            echo "Sei connesso come ".$_SESSION['username']." ";
            echo "<a href='disconnessione.php'>disconnettiti</a>";
        ?>
            </td>
            <td>
                <?php
            echo "Crediti: $usermoney ";
        ?>
            </td>
        </tr>
    </table>
    <?php
    	}
    ?>
</div>