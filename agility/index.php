<?php

    /*
    index.php

    Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

    This program is free software; you can redistribute it and/or modify it under the terms
    of the GNU General Public License as published by the Free Software Foundation;
    either version 2 of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
    without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
     See the GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along with this program;
    if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
    */

    /*
     * This file acts as first an main entry point for webhosting install
     * WebHosting install lacks of system.ini file
     *
     * If system.ini file already exits act like normal (public) entry point
     * else re-create according web host info and user preferences
     */
    if (file_exists(__DIR__."/server/auth/system.ini")) {
        // just a simple redirector to public/index.php
        // from: https://stackoverflow.com/questions/15110355/how-to-safely-get-full-url-of-parent-directory-of-current-php-page
        $url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $url .= $_SERVER['SERVER_NAME'];
        $path= parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $res=str_replace("index.php","public/index.php",$path,$count);
        if ($count===0) $url .= $res ."public/index.php";
        else $url .= $res;
        header("Location: {$url}",false);
        die();
    }

    require_once(__DIR__."/server/tools.php");

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="application-name" content="Agility Contest" />
    <meta name="copyright" content="© 2013-2018 Juan Antonio Martinez" />
    <meta name="author" lang="en" content="Juan Antonio Martinez" />
    <meta name="description"
          content="A web client-server (xampp) app to organize, register and show results for FCI Dog Agility Contests" />
    <meta name="distribution"
          content="This program is free software; you can redistribute it and/or modify it under the terms of the
		GNU General Public License as published by the Free Software Foundation; either version 2 of the License,
		or (at your option) any later version." />
    <!-- try to disable zoom in tablet on double click -->
    <meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, maximum-scale=2.0, minimum-scale=0.5, user-scalable=yes"/>
    <title>AgilityContest (WebHost install)</title>
    <link rel="stylesheet" type="text/css" href="lib/jquery-easyui-1.4.2/themes/default/easyui.css" />
    <link rel="stylesheet" type="text/css" href="lib/jquery-easyui-1.4.2/themes/icon.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <link rel="stylesheet" type="text/css" href="css/datagrid.css" />
    <script src="lib/jquery-2.2.4.min.js" type="text/javascript" charset="utf-8" > </script>
    <script src="lib/jquery-easyui-1.4.2/jquery.easyui.min.js" type="text/javascript" charset="utf-8" ></script>
    <script src="scripts/common.js.php" type="text/javascript" charset="utf-8" > </script>
    <script type="text/javascript">

        function initialize() {

        }


        function read_regFile() {

        }

        function check_db() {

        }
    </script>
</head>
<body style="margin:0;padding:0" onload="initialize();">
<h2>AgilityContest WebHost install</h2>
<div id="install-window" class="easyui-window" style="width:800px;height:600px;padding:10px 20px" data-options="fit:false">

    <form id="install_form" name="install_form" class="easyui-form">
    <img src="images/AgilityContest.png"
         width="150" height="100" alt="AgilityContest Logo"
         style="border:1px solid #000000;margin:10px;float:right;padding:5px">
    <dl>
        <dt>
            <strong><?php _e('Version'); ?>: </strong><span id="reg_version">version</span> - <span id="reg_date">date</span>
        </dt>
        <dt>
            <strong>AgilityContest</strong> <?php _e('is Copyright &copy; 2013-2018 by'); ?> <em> Juan Antonio Martínez &lt;juansgaviota@gmail.com&gt;</em>
        </dt>
        <dd>
            <?php _e('Source code is available at'); ?> <a href="https://github.com/jonsito/AgilityContest">https://github.com/jonsito/AgilityContest</a><br />
            <?php _e('You can use, copy, modify and re-distribute under terms of'); ?>
            <a target="license" href="License"><?php _e('GNU General Public License'); ?></a>
        </dd>
    </dl>
    <p>
        <?php _e('Registered at'); ?> 'Registro Territorial de la Propiedad Intelectual de Madrid'. <em>Expediente: 09-RTPI-09439.4/2014</em>
    </p>
    <hr />

    <h3><?php _e("Server and Database information");?> </h3>
        <div class="fitem">
            <label for="install_host" style="width:250px;margin-top:5px"><?php _e("Server Name");?>: </label>
            <input type="text" class="easyui-textbox" id="install_host" name="install_host">
            <br/>
        </div>
        <div class="fitem">
            <label for="install_dbname" style="width:250px;margin-top:5px"><?php _e("Database Name");?>: </label>
            <input type="text" class="easyui-textbox" id="install_dbname" name="install_dbname">
            <br/>
        </div>
        <div class="fitem">
            <label for="install_dbpass" style="width:250px;margin-top:5px"><?php _e("Database root password");?>: </label>
            <input type="password" class="easyui-textbox" id="install_dbpass" name="install_dbpass">
            <br/>
        </div>
        <div class="fitem">
            <label for="install_dbcheck" style="width:250px;margin-top:5px"><?php _e("Check database connection");?>: </label>
            <a id="install_dbcheck" href="#" class="easyui-linkbutton"
               data-options="iconCls:'icon-help'" onclick="check_db()"><?php _e('Check'); ?></a>
        </div>
    <hr/>

    <h3><?php _e("AgilityContest default users:");?> </h3>
        <div class="fitem">
            <label for="install_admin" style="width:250px;margin-top:5px"><?php _e("Enter password for 'admin' user");?>: </label>
            <input type="password" class="easyui-textbox" id="install_admin" name="install_admin">
            <span id="install_admin_strength">&nbsp;hola</span>
            <br/>
        </div>
        <div class="fitem">
            <label for="install_admin2" style="width:250px;margin-top:5px"><?php _e("Enter password (again) for 'admin'");?>: </label>
            <input type="password" class="easyui-textbox" id="install_admin2" name="install_admin2">
            <span id="install_admin2_match">&nbsp;</span>
            <br/>&nbsp;<br/>
        </div>
        <div class="fitem">
            <label for="install_operator" style="width:250px;margin-top:5px"><?php _e("Enter password for 'operator' user");?>: </label>
            <input type="password" class="easyui-textbox" id="install_operator" name="install_operator">
            <span id="install_operator_strength">&nbsp;</span>
            <br/>
        </div>
        <div class="fitem">
            <label for="install_operator2" style="width:250px;margin-top:5px"><?php _e("Enter password (again) for 'operator'");?>: </label>
            <input type="password" class="easyui-textbox" id="install_operator2" name="install_operator2">
            <span id="install_operator2_match">&nbsp;</span>
            <br/>&nbsp;<br/>
        </div>
        <div class="fitem">
            <label for="install_assistant" style="width:250px;margin-top:5px"><?php _e("Enter password for 'assistant' user");?>: </label>
            <input type="password" class="easyui-textbox" id="install_assistant" name="install_assistant">
            <span id="install_assistant_strength">&nbsp;</span>
            <br/>
        </div>
        <div class="fitem">
            <label for="install_assistant2" style="width:250px;margin-top:5px"><?php _e("Enter password (again) for 'assistant'");?>: </label>
            <input type="password" class="easyui-textbox" id="install_assistant2" name="install_assistant2">
            <span id="install_assistant2_match">&nbsp;</span>
            <br/>&nbsp;<br/>
        </div>
    <hr/>

    <h3><?php _e("Licensing and registration");?> </h3>
        <div class="fitem">
            <label for="install_accept" style="width:300px;margin-top:5px"><?php _e("I've read, understand and accept License terms");?>: </label>
            <input type="checkbox" class="easyui-checkbox" id="install_accept" name="install_accept">
            <br/>
        </div>
        <div class="fitem">
            <label for="install_license" style="width:250px;margin-top:5px"><?php _e("Enter registration license file");?>: </label>
            <input name="install_license" id="install_license" style="width:350px;" onchange="read_regFile(this)"/>
            <input id="install_regdata" type="hidden" name="Data" value="">
        </div>

        <span style="float:right">
			<a id="install-okButton" href="#" class="easyui-linkbutton"
               data-options="iconCls:'icon-ok'"
               onclick="$('#dlg_register').window('close');"><?php _e('Aceptar'); ?></a>
	    </span>
        <br/>
    </form>
</div>
<script type="text/javascript">
    $('#install_host').textbox({required:true,value:'www.server.domain',validType:'length[1,255]'});
    $('#install_dbname').textbox({required:true,value:'dbname',validType:'length[1,255]'});
    $('#install_dbpass').textbox({required:true,value:'dbpassword',validType:'length[1,255]',iconCls:'icon-lock'});
    $('#install_admin').textbox({
        required:true,
        value:'admin',
        validType:'length[6,32]',
        iconCls:'icon-lock',
        inputEvents:$.extend({},$.fn.textbox.defaults.inputEvents,{
            keyup:function(e){
                var t = $(e.data.target).textbox('getText');
                passwordStrength(t,$('#install_admin_strength'));
            }
        })
    });
    $('#install_admin2').textbox({
        required:true,
        value:'admin',
        iconCls:'icon-lock',
        inputEvents:$.extend({},$.fn.textbox.defaults.inputEvents,{
            keyup:function(e){
                var p1 = $('#install_admin').textbox('getText');
                var p2 = $(e.data.target).textbox('getText');
                passwordMatch(p1,p2,$('#install_admin2_match'));
            }
        })
    });
    $('#install_operator').textbox({
        required:true,
        value:'operator',
        validType:'length[6,32]',
        iconCls:'icon-lock',
        inputEvents:$.extend({},$.fn.textbox.defaults.inputEvents,{
            keyup:function(e){
                var t = $(e.data.target).textbox('getText');
                passwordStrength(t,$('#install_operator_strength'));
            }
        })
    });
    $('#install_operator2').textbox({
        required:true,
        value:'operator',
        iconCls:'icon-lock',
        inputEvents:$.extend({},$.fn.textbox.defaults.inputEvents,{
            keyup:function(e){
                var p1 = $('#install_operator').textbox('getText');
                var p2 = $(e.data.target).textbox('getText');
                passwordMatch(p1,p2,$('#install_operator2_match'));
            }
        })
    });
    $('#install_assistant').textbox({
        required:true,
        value:'assistant',
        validType:'length[6,32]',
        iconCls:'icon-lock',
        inputEvents:$.extend({},$.fn.textbox.defaults.inputEvents,{
            keyup:function(e){
                var t = $(e.data.target).textbox('getText');
                passwordStrength(t,$('#install_assistant_strength'));
            }
        })
    });
    $('#install_assistant2').textbox({
        required:true,
        value:'assistant',
        iconCls:'icon-lock',
        inputEvents:$.extend({},$.fn.textbox.defaults.inputEvents,{
            keyup:function(e){
                var p1 = $('#install_assistant').textbox('getText');
                var p2 = $(e.data.target).textbox('getText');
                passwordMatch(p1,p2,$('#install_assistant2_match'));
            }
        })
    });
    var fb=$('#install_license');
    fb.filebox({
        accept:  ".info",
        buttonText: '<?php _e("Select"); ?>',
        buttonAlign: 'left',
        buttonIcon: 'icon-search',
        onChange: function(newfile,oldfile) {
            read_regFile(fb.next().find('.textbox-value')[0]); // locate real input text
        }
    });
    fb.next().find('.textbox-value').attr('accept', '.info');

    $('#install-window').window({
        title: '<?php _e("AgilityContest setup"); ?>',
        collapsible:false,
        minimizable:false,
        maximizable:false,
        resizable:false,
        closable:true,
        modal:true,
        iconCls: 'icon-dog',
        onOpen: function() {
        },
        onClose: function() {
        }
    });

    addTooltip($('#install_dbcheck').linkbutton(),'<?php _e("Check server/database conectivity"); ?>');

</script>
</body>
</html>