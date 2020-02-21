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
    <meta name="copyright" content="© 2013-2020 Juan Antonio Martinez" />
    <meta name="author" lang="en" content="Juan Antonio Martinez" />
    <title>AgilytyContest blog</title>
</head>
<body>

<div style="display:inline-block;width:100%">
    <span style="float:left">
        <p style="font: italic bold 20px/30px Georgia, serif;">AgilytyContest News &amp; Blog</p>
    </span>
    <span style="float:right">
        <img alt="logo" src="https://raw.github.com/jonsito/AgilityContest/master/agility/images/AgilityContest.png">
    </span>
</div>
    <a id="top"></a>
<?php } ?>

<?php if ( strcmp($from,"blog_2020-02-01_00:00:00")<0) { ?>
<strong>2020-Mar-01 00:00:00</strong><br/>
2<br/>
3<br/>
4<br/>
5<br/><img alt="logo" src="https://raw.github.com/jonsito/AgilityContest/master/agility/images/AgilityContest.png">
6<br/>
7<br/>
8<br/>
9<br/>
10<br/>
<?php } ?>


<?php if ( strcmp($from,"blog_2020-02-20_00:00:00")<0) { ?>
<strong>2020-Feb-22 00:00:00</strong><br/>
11<br/>
12<br/>
13<br/>
14<br/>
15<br/><img alt="logo" src="https://raw.github.com/jonsito/AgilityContest/master/agility/images/AgilityContest.png">
16<br/>
17<br/>
18<br/>
19<br/>
20<br/>
<?php } ?>


<?php if ( strcmp($from,"blog_2020-02-10_00:00:00")<0) { ?>
<strong>2020-Feb-10 00:00:00</strong><br/>
21<br/>
22<br/>
23<br/>
24<br/>
25<br/>
26<br/>
27<br/>
28<br/>
29<br/>
30<br/>
<?php } ?>


<?php if ( strcmp($from,"blog_2020-01-30_00:00:00")<0) { ?>
<strong>2020-Jan-30 00:00:00</strong><br/>
31<br/>
32<br/>
33<br/>
34<br/>
35<br/>
36<br/>
37<br/>
38<br/>
39<br/>
40<br/>
<?php } ?>


<?php if ( strcmp($from,"blog_2020-01-20_00:00:00")<0) { ?>
<strong>2020-Jan-20 00:00:00</strong><br/>
41<br/>
42<br/>
43<br/>
44<br/>
45<br/>
46<br/>
47<br/>
48<br/>
49<br/>
50<br/>
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