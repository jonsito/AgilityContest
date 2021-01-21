<?php
define("AC_EPOCH","blog_1900-01-01_00:00:00");
$from=AC_EPOCH;
if (array_key_exists("TimeStamp",$_REQUEST)) {
    $from="blog_{$_REQUEST['TimeStamp']}";
}
?>

<?php if ($from==AC_EPOCH) { ?>

<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="application-name" content="Agility Contest" />
    <meta name="copyright" content="© 2013-2021 by Juan Antonio Martinez" />
    <meta name="author" lang="en" content="Juan Antonio Martinez" />
    <title>AgilytyContest blog</title>
</head>
<body>

<div style="display:inline-block;width:100%">
    <p style="font: italic bold 20px/30px Georgia, serif;">
            <span style="float:left">AgilytyContest News &amp; Blog</span>
    </p>
    <p>
    <span style="float:right">
        <img alt="logo" src="https://raw.github.com/jonsito/AgilityContest/master/agility/images/AgilityContest.png">
    </span>
    </p>

</div>
    <a id="top"></a>
<?php } ?>

<!--- comienzo del changelog -->

<?php if ( strcmp($from,"blog_2020-08-20_00:00:00")<0) { ?>
    <strong>2020-Ago-20 00:00:00</strong><br/>
    <p>
        Nueva versi&oacute;n 4.3.2
    </p>
    <p>
        En esta nueva versi&oacute;n se ha cambiado la nomenclatura de las mangas de Grado 1 RSCE<br/>
        Ahora, en las nuevas pruebas aparecer&aacute;n como <em>Manga 1</em>, <em>Manga 2</em>, y <em>Manga 3</em>
    </p>
    <p>
        Adicionalmente, en la ventana de desarrollo de la prueba aparece (solo para Grado 1) la opción de
        seleccionar si la manga es de Agility o de Jumping, o si se le quiere dar una denominación especial
    </p>
    <p>
        A la hora de generar los listados de Grado 1, el programa tiene en cuenta esta selecci&oacute;n indicando
        &eacute;sta en el PDF generado
    </p>
<?php } ?>

<?php if ( strcmp($from,"blog_2020-03-39_11:50:00")<0) { ?>
    <strong>2020-Mar-09 11:50:00</strong><br/>
    <p>
        Un apunte sobre los n&uacute;meros de versiones del programa
    </p>
    <ol>
        <li>
            <em>AgilityContest</em> utiliza el formato de versionado <em>X.Y.Z</em>, donde:
            <ol>
                <li><strong>X</strong> Corresponde a cambios estructurales en el programa</li>
                <li><strong>Y</strong> Corresponde a nuevas funcionalidades</li>
                <li><strong>Z</strong> Corresponde a correcci&oacute;n de errores</li>
            </ol>
        </li>
        <li>
            Adicionalmente se proporciona un n&uacute;mero de revisi&oacute;n en formato
            <em>AAAAMMDD_hhmm</em> que corresponde a la fecha en que ha sido generada la nueva versi&oacute;n
        </li>
        <li>
            Los cambios en la fecha de revisi&oacute;n que no corresponden a una nueva versi&oacute;n
            del programa, normalmente corresponden a correcciones menores que no afectan al funcionamiento
            de &eacute;ste
        </li>
        <li>
            En todo momento, se puede consultar la lista completa de cambios y su descripci&oacute;n en
            el <a target="changelog" href="https://raw.githubusercontent.com/jonsito/AgilityContest/master/ChangeLog">registro de cambios</a>
            del programa
        </li>

    </ol>
<?php } ?>

<?php if ( strcmp($from,"blog_2020-02-27_15:00:00")<0) { ?>
    <strong>2020-Feb-27 15:00:00</strong><br/>
    <p>
        Un apunte sobre el Pre-Agility:
    </p>
    <p>
        Los perros de Pre-Agility son por su propia naturaleza (edad,nivel de entrenamiento,o experiencia del gu&iacute;a)
        no pueden ni deben participar en una competici&oacute;n real, no solo por la evidente desigualdad de condiciones,
        sino tambi&eacute;n porque en algunos casos, sobre todo en perros j&oacute;venes o demasiado viejos,
        su participaci&oacute;n puede acarrear riesgos para su salud
    </p>
    <p>
        Es por ello que AgilityContest <em>no permite</em> inscribir perros de Pre-Agility en ning&uacute;n tipo
        de prueba salvo en mangas de Pre-Agility. (De paso esquivo alg&uacute;n problema de Responsabilidad Civil,
        y posibles demandas, pero eso es otra historia... )
    </p>
    <p>
        No obstante, y si a pesar de todo alguien quiere hacer el bestia, y permitir que esos perros compitan en
        otras modalidades, lo que tiene que hacer es <em>bajo su responsabilidad</em> cambiar el grado del perro
        a "-- Sin especificar --", con lo cual los podrían inscribir en pruebas open, por equipos, rondas ko y
        mangas especiales; pero no en categorías de competición normales

    </p>
    <p>
        No: esa restricci&oacute;n no la voy a quitar. Ya en su d&iacute;a me echaron la bronca por permitir
        inscribir a pre-agilitys en otras competiciones; y con toda la raz&oacute;n del mundo
    </p>
<?php } ?>

<?php if ( strcmp($from,"blog_2020-02-21_19:00:00")<0) { ?>
<strong>2020-Feb-21 00:00:00</strong><br/>
    <p>
        !Hola!
    </p><p>
        Al final diversos "incidentes" me han obligado a quedarme en casa mientras el resto del universo va hoy de camino a Barcelona para la selectiva :-(
    </p><p>
        Como venganza le he pegado un empujón final a la nueva versión. A lo largo del fin de semana estará publicadada. Los impacientes, pueden usar el botón actualizar desde el menú de administración
    </p><p>
        Como todavía tengo pendiente alguna base de datos por recibir ( tsk, tsk :-) ), pero se me echa el tiempo encima lo que voy a hacer es sacar una nueva versión "4.3.0" y para la 4.3.1 añadiré la base de datos actualizada
    </p><p>
    <ul>
        Lista de cambios: ( rollo )
    <li>
        Correcciones para el instalador en MacOS Catalina. Recordad que hay que instalar el XAMPP version 7.12 o superior; los anteriores no funcionan en la última versión de Mac-OSX
    </li><li>
        Ahora, cuando se seleccionan recorridos a 5 alturas separadas, en los parciales, podio y finales se pueden agrupar los resultados CONSERVANDO cada altura su TRS. Ideal para ahorrar en escarapelas, o para cumplir al 100% con la normativa RSCE sobre listados
    </li><li>
        Ahora, cuando NO se inicia sesión, el programa emite un aviso bien gordo si se intenta acceder al menú de administración o al de registro de licencia
    </li><li>
        En los diálogos de selección de jornada, se indica en un "bocadillo", el tipo de competición, para facilitar la búsqueda
    </li><li>
        Se ha añadido una entrada "enlaces" en el menú principal, con acceso directo a la web de agilitycontest.es, al grupo de Facebook y al (nuevo) blog de AgilityContest
    </li><li>
        Para los que no leen el correo ni usan Facebook, ahora al iniciar sesión se abre una ventana con las ultimas noticias desde la última vez que se inició sesión. Las noticias se extraen del (nuevo) Blog, al que se puede acceder en todo momento desde el menú principal.
    </li><li>
        La opción de desplegar noticias se puede deshabilitar, desde el menú de preferencias. Por defecto viene activada, para que nadie pueda decir que no se entera de las novedades
    </li><li>
        Evidentemente: en www.agilitycontest.es ahora hay una nueva página "/blog" donde se pondrán las noticias y avisos que se ponen aquí y nadie lee :-(
    </li><li>
        Y como siempre, alguna cucaracha (bug) suelta que he corregido, especialmente en el tema de Grado 1 a tres mangas y cinco alturas...
    </li>
    </ul>
    <p>
    Como hay funcionalidades nuevas, corresponde un cambio de numeración ( 4.2.1 a 4.3.0 ) de versión. Como digo: en cuanto tenga lista la nueva base de datos, sacaré la 4.3.1. Entretanto, recomiendo en el instalador la opción de "actualizar" en lugar de "instalar" si ya tenéis una base de datos "razonable"
    <br/>    A disfrutar
    </p><p>
        PS: ¿Alguien sabe si se transmite la selectiva por streaming? :-) Suerte a los participantes. Bueno, a tí no
    </p>

<?php } ?>

<?php if ( strcmp($from,"blog_2020-01-10_00:00:00")<0) { ?>
<strong>2020-Jan-10 00:00:00</strong><br/>
51<br/>
52<br/>
53<br/>
54<br/>
55<br/>
56<br/>
57<br/>
58<br/>
59<br/>
60<br/>
<?php } ?>


<?php if ( strcmp($from,"blog_2020-01-01_00:00:00")<0) { ?>
<strong>2020-Jan-01 00:00:00</strong><br/>
61<br/>
62<br/>
<?php } ?>

<?php if ($from==AC_EPOCH) { ?>
<a id="bottom"></a>

</body>
</html>
<?php } ?>