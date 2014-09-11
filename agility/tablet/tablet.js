function resultados_update() {
    // call 'submit' method of form plugin to submit the form
    $('#tdialog-form').form('submit', 
    	{
    		url:'/agility/database/resultadosFunctions.php',
    		onSubmit: function() { return true; },
    		success:function(data){
    			var obj=formToObject('#tdialog-form');
    			$('#tablet_competicion-EntradaDatos').datagrid('updateRow',{index: obj.Parent,row: obj});
    		}
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

function tablet_swap(id) {
	var n= parseInt($(id).val());
	$(id).val(n==0)?1:0;
	resultados_update();
}

function tablet_startstop() {
	// TODO: write
}
function tablet_lapreset() {
	// TODO: write
}

function tablet_cancel() {
	$('#tdialog-form').form('reset'); // restore to original state
	resultados_update(); // save results 
	$('#tdialog-dialog').dialog('close'); // and close window
}

function tablet_accept() { 
	resultados_update(); // save results 
	$('#tdialog-dialog').dialog('close'); // and close window
}
