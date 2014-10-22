
<div id="dlg_about" class="easyui-window" title="Acerca de" style="width:600px;padding:20px">
	<p>
	<strong>Agility Contest</strong> es Copyright &copy; 2013,2014 de Juan Antonio Martínez &lt;juansgaviota@gmail.com&gt;
	</p>
	<p>
	Se permite su uso, copia, modificaci&oacute;n y redistribuci&oacute;n 
	bajo los t&eacute;rminos de la <a target="license" href="COPYING">Licencia General P&uacute;blica de GNU</a>
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
            collapsible:false,
            minimizable:false,
            maximizable:false,
            resizable:false,
            closable:true,
            iconCls: 'icon-dog',
            onClose: function() {loadContents('/agility/client/frm_main.php','');
            }
        })
</script>