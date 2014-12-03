var isCtrl = false;
document.onkeyup = function(e){ if(e.which == 17) isCtrl = false; } 
document.onkeydown = function(e){
	if(e.which == 17) isCtrl = true;
	if(e.which == 32 && isCtrl == true){
		vrExpandCollapse('vr-trace');
		return false;
	}
}
function vrExpandCollapse(){
	for(var i=0; i<vrExpandCollapse.arguments.length; i++){
		var element = document.getElementById(vrExpandCollapse.arguments[i]);
		element.style.display = (element.style.display == "none") ? "block" : "none";
	}
}
function vrExpandColTable(){
	for(var i=0; i<vrExpandColTable.arguments.length; i++){
		var element = document.getElementById(vrExpandColTable.arguments[i]);
		element.style.display = (element.style.display == "none") ? "table-cell" : "none";
	}
}