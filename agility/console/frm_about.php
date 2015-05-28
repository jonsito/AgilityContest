<!-- 
frm_about.php

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->
<div id="dlg_about" class="easyui-window" style="width:640px;padding:10px">
	<img src="/agility/images/AgilityContest.png" 
		width="150" height="100" alt="AgilityContest Logo" 
		style="border:1px solid #000000;margin:10px;float:right;padding:5px">
	<dl>
		<dt>
			<strong>Version: </strong><span id="about_version">version</span> - <span id="about_date">date</span> 
		</dt>
		<dt>
			<strong>AgilityContest</strong> es Copyright &copy; 2013-2015 de <em>Juan Antonio Martínez &lt;juansgaviota@gmail.com&gt;</em>
		</dt>
		<dd>
		El código fuente está disponible en <a href="https://github.com/jonsito/AgilityContest">https://github.com/jonsito/AgilityContest</a><br />
		Se permite su uso, copia, modificación y redistribución bajo los t&eacute;rminos de la 
		<a target="license" href="/agility/License">Licencia General P&uacute;blica de GNU</a>
		</dd>
		<dt>&nbsp;</dt>
		<dt>
			<strong>AgilityContest Logo</strong> está basado en un diseño original de <em>Britta Schweikl &lt;ltBritta.Schweikl@t-online.de&gt;</em>
		</dt>
		<dd>
		El diseño original -y muchos otros- se encuentra en <a href="http://www.hundestempel.de/">http://www.hundestempel.de/</a>. <br />
		Este logotipo se distrubuye con autorización del autor original. Esta nota de copyright debe ser incluída en toda copia y redistribución
		</dd>
	</dl>
	<hr />
	<p>
	Inscrito en el Registro Territorial de la Propiedad Intelectual de Madrid. <em>Expediente: 09-RTPI-09439.4/2014</em> 
	</p>
	<p>	
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
	</p><p>
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
	</p><p>
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
	</p>
</div>
<script type="text/javascript">
        $('#dlg_about').window({
            title: "Acerca de AgilityContest",
            collapsible:false,
            minimizable:false,
            maximizable:false,
            resizable:false,
            closable:true,
            modal:true,
            iconCls: 'icon-dog',
            onOpen: function() { 
                $('#about_version').html(ac_config.version_name);
                $('#about_date').html(ac_config.version_date);
            },  
            onClose: function() {loadContents('/agility/console/frm_main.php','');
            }
        })
</script>