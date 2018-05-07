<div id="plugins-dlg-admintab" style="width:100%;height:500px;">
	<table id="plugins-datagrid"></table>
</div>
<div id="plugins-datagrid-toolbar" style="width:100%;display:inline-block">
	<span style="float:left;padding:5px">
		<a id="plugins-newBtn" href="#" class="easyui-linkbutton"
		   data-options="iconCls:'icon-new'"
		   onclick="newPlugin()"><?php _e('New plugin'); ?></a>
   		<a id="plugins-editBtn" href="#" class="easyui-linkbutton"
		   data-options="iconCls:'icon-edit'"
		   onclick="updatePlugin('#plugins-datagrid')"><?php _e('Upgrade'); ?></a>
   		<a id="plugins-delBtn" href="#" class="easyui-linkbutton"
		   data-options="iconCls:'icon-trash'"
		   onclick="deletePlugin('#plugins-datagrid')"><?php _e('Delete'); ?></a>

   	</span>
	<span style="display:none" id="plugins-datagrid-search"><?php _e("-- Search --");?></span>
	<span style="float:right;padding:5px">
   		<a id="plugins-reloadBtn" href="#" class="easyui-linkbutton"
		   data-options="iconCls:'icon-reload'"
		   onclick="  $('#plugins-datagrid').datagrid('load');"
		><?php _e('Refresh'); ?></a>
   	</span>
</div>

<script type="text/javascript">
	$('#plugins-datagrid').datagrid({
		// datos del panel padre asociado
		fit: true,
		border: false,
		closable: false,
		collapsible: false,
		expansible: false,
		collapsed: false,
		title: '<?php _e('Current installed modules'); ?>',
		// datos de la conexion ajax
		url: '../ajax/modules/moduleFunctions.php',
        queryParams: { 'Operation' : 'competitions' },
		loadMsg: '<?php _e('Loading competitions module list'); ?>',
		method: 'get',
		toolbar: '#plugins-datagrid-toolbar',
		pagination: false,
		rownumbers: true,
		fitColumns: true,
		singleSelect: true,
		view: scrollview,
		pageSize: 50,
		multiSort: true,
		remoteSort: false,
		columns: [[
			{ field:'FederationID',		hidden:true },
			{ field:'ModuleName',		width:35, sortable:true,title:'<?php _e('Name'); ?>' },
            { field:'FederationName',	width:10, sortable:true,title:'<?php _e('Federation'); ?>' },
            { field:'Email',	        width:25, sortable:true,title:'<?php _e('Email'); ?>' },
            { field:'ModuleID',		    width:5,  sortable:true,title:'<?php _e('ID'); ?>'},
			{ field:'ModuleVersion',	width:10, sortable:true,title:'<?php _e('Version'); ?>' },
			{ field:'ModuleRevision',	width:20, sortable:true,title:'<?php _e('Revision'); ?>' }
		]],
		// colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
		rowStyler:myRowStyler
	});
	addKeyHandler('#plugins-datagrid',"",newPlugin,updatePlugin,deletePlugin);
	addTooltip($('#plugins-newBtn').linkbutton(),'<?php _e("Search and install new competition modules"); ?>');
	addTooltip($('#plugins-editBtn').linkbutton(),'<?php _e("Update selected competition module"); ?>');
	addTooltip($('#plugins-delBtn').linkbutton(),'<?php _e("Delete selected competition module"); ?>');
	addTooltip($('#plugins-reloadBtn').linkbutton(),'<?php _e("Reload competition module list"); ?>');
</script>