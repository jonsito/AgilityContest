function resultados_update() {
    // call 'submit' method of form plugin to submit the form
    $('#tdialog-form').form('submit', 
    	{
    		url:'/agility/database/resultadosFunctions.php',
    		onSubmit: function() { return true; },
    		// !do not update parent tablet row! 
    		// as form('reset') seems not to work as we want, we use it as backup
    		// success:function(data){
    		// 	 var obj=formToObject('#tdialog-form');
    		//	 $('#tablet_competicion-EntradaDatos').datagrid('updateRow',{index: obj.Parent,row: obj});
    		// }
    	});
}

function tablet_add(val) {
	var str=$('#tdialog-Tiempo').val();
	if (parseInt(str)==0) str=''; // clear espurious zeroes
	if(str.length>=6) return; // sss.xx 6 chars
	var n=str.indexOf('.');
	if (n>=0) {
		var len=str.substring(n).length;
		if (len>2) return; // only two decimal digits
	}
	$('#tdialog-Tiempo').val(''+str+val);
	resultados_update();
}

function tablet_dot() {
	var str=$('#tdialog-Tiempo').val();
	if (str.indexOf('.')>=0) return;
	tablet_add('.');
	resultados_update();
}

function tablet_del() {
	var str=$('#tdialog-Tiempo').val();
	if (str==='') return;
	$('#tdialog-Tiempo').val(str.substring(0, str.length-1));
	resultados_update();
}

function tablet_up(id){
	var n= 1+parseInt($(id).val());
	$(id).val(''+n);
	resultados_update();
}

function tablet_down(id){
	var n= parseInt($(id).val());
	var m = (n<=0) ? 0 : n-1;
	$(id).val(''+m);
	resultados_update();
}
function tablet_np() {
	var n= parseInt($('#tdialog-NoPresentado').val());
	if (n==0) {
		$('#tdialog-NoPresentado').val(1);
		// si no presentado borra todos los demas datos
		$('#tdialog-Eliminado').val(0);
		$('#tdialog-Faltas').val(0);
		$('#tdialog-Rehuses').val(0);
		$('#tdialog-Tocados').val(0);
		$('#tdialog-Tiempo').val(0);
	} else {
		$('#tdialog-NoPresentado').val(0);
	}
	resultados_update();
}

function tablet_elim() {
	var n= parseInt($('#tdialog-Eliminado').val());
	if (n==0) {
		$('#tdialog-Eliminado').val(1);
		// si eliminado, poner nopresentado y tiempo a cero, conservar todo lo demas
		$('#tdialog-NoPresentado').val(0);
		$('#tdialog-Tiempo').val(0);
	} else {
		$('#tdialog-Eliminado').val(0);
		
	}
	resultados_update();
}

function tablet_startstop() {
	// TODO: write
}
function tablet_lapreset() {
	// TODO: write
}

function tablet_cancel() {
	// retrieve original data from parent datagrid
	var rows = $('#tablet_competicion-EntradaDatos').datagrid('getRows'); 
	var row = rows[$('#tdialog-Parent')];  
	 // restore form
    $('#tdialog-form').form('load',row);
    // save restored results 
	resultados_update(); 
	 // and close window
	$('#tdialog-dialog').dialog('close');
}

function tablet_accept() {
	// save results 
	resultados_update();
	// send back data to parent tablet datagrid form
	var obj=formToObject('#tdialog-form');
	$('#tablet_competicion-EntradaDatos').datagrid('updateRow',{index: obj.Parent,row: obj});
	// and close windows
	$('#tdialog-dialog').dialog('close'); // and close window
}
