function creatFrom() {

    var divf 	= document.getElementById("form_div");
    divi	= document.getElementById("input_div");
    var frm     = document.createElement("form");
    var tbl     = document.createElement("table");
    var tblBody = document.createElement("tbody");
    
    tbl.appendChild(tblBody);
    frm.appendChild(tbl);
    frm.appendChild(divi);
    divf.appendChild(frm);
    
    tbl.setAttribute("id", "tbl_setList");
    tbl.setAttribute("class", "tablewidth");
    divi.setAttribute("align", "center");
    frm.setAttribute("action", "admin.php?go="+jslanfp[0]);
    frm.setAttribute("method", "post");
	
}
function creatSubmit(){
	Sub = document.createElement("input");
	setPref(Sub,"type","submit");
	setPref(Sub,"value",jslanfp[1]);
	divi.appendChild(Sub);
	Sub = document.createElement("input");
	setPref(Sub,"type","reset");
	setPref(Sub,"value",jslanfp[2]);
	divi.appendChild(Sub);
	Sub = document.createElement("input");
	setPref(Sub,"type","hidden");
	setPref(Sub,"value","save_list");
	setPref(Sub,"name","configjob");
	divi.appendChild(Sub);
}
function chkDelItem(index) {if(confirm(jslanfp[3])){delItem(index.parentNode.parentNode.rowIndex);}}
function delItem(index) { dyn_t.deleteRow(index);}
function setPref(objSet,prefName,pref){return objSet.setAttribute(prefName,pref);}
function add_Cell(myRow,name,value,size){return "<input type=\"text\" name=\""+name+"\" value=\""+value+"\" size=\""+size+"\" />";}
function add_fpRow(inum,identifier,title,creator,location,info,image,album,meta){
	var setOut;
	var re = /^\d+$/;
	var ntime=new Date();
	if(inum=="inum"){inum = document.getElementById("inum").value;if (inum!="" && !re.test(inum)) {inum=-1;}}
	if(identifier=="")identifier=ntime.getTime();
	name="fmp[" + identifier + "]";
	myRow = dyn_t.insertRow(inum);
	setOut = jslanfp[4]+":"+ identifier + "<br/>"+
	"<a href=\"JavaScript://\" onclick=\"moveItem('up',this)\">["+jslanfp[5]+"]</a>"+
	"<a href=\"JavaScript://\" onclick=\"moveItem('down',this)\">["+jslanfp[6]+"]</a>"+
	"<a href=\"JavaScript:showhidediv('"+identifier+"');\">["+jslanfp[7]+"]</a>"+
	"<a href=\"JavaScript://\" onclick=\"chkDelItem(this)\">["+jslanfp[8]+"]</a>";
	myCell = myRow.insertCell(-1);
	myCell.innerHTML = setOut;
	myCell.setAttribute("class", "prefleft");
	myCell.setAttribute("valign", "top");
	myCell.setAttribute("width", "200");
	
	//alert(myCell.getAttribute("class"));	
	if((dyn_t.rows.length % 2) == 0){myCell.style.background = "#CCCCCC";}
	setOut = "<table valign=\"top\"><tr>"+
	"<td class=\"prefsection\">"+jslanfp[9]+":<br/>" +add_Cell(myRow,name+"[title]",title,18) + "</td>"+
	"<td class=\"prefsection\">"+jslanfp[10]+":<br/>" +add_Cell(myRow,name+"[creator]",creator,18)+ "</td>"+
	"<td class=\"prefsection\">"+jslanfp[11]+":<br/>" +add_Cell(myRow,name+"[location]",location,60)+ "</td>"+
	"</tr></table>"+
	"<div id=\""+identifier+"\" style=\"display: none\">"+
	"<table valign=\"top\"><tr>"+
	"<td class=\"prefsection\">"+jslanfp[12]+":<br/>" + add_Cell(myRow,name+"[info]",info,40) + "</td>"+
	"<td class=\"prefsection\">"+jslanfp[13]+":<br/>" +add_Cell(myRow,name+"[image]",image,40) + "</td>"+
	"<td class=\"prefsection\">"+jslanfp[14]+":<br/>" +add_Cell(myRow,name+"[album]",album,6) + "</td>"+
	"<td class=\"prefsection\">"+jslanfp[15]+":<br/>" +add_Cell(myRow,name+"[meta]",meta,4) + "</td>"+
	"</tr></table></div>";
	myCell = myRow.insertCell(-1);
	myCell.innerHTML = setOut;
	myCell.setAttribute("class", "prefright");
	myCell.setAttribute("valign", "top");
	if((dyn_t.rows.length % 2) == 0){myCell.style.background = "#C0C0C0";}
}
function moveItem(act,index){
	index = index.parentNode.parentNode.rowIndex;
	if(act=="up" & index>0 | act=="down" & index<dyn_t.rows.length-1){
		var setIndex = new Array;
		switch(act){
			case "up":setIndex = Array(index-1,index,index+1);break;
			case "down":setIndex = Array(index+2,index,index);break;
		}
		dyn_t.insertRow(setIndex[0]);
		for(index=0;index<dyn_t.rows[setIndex[1]].cells.length;index++){
			myCell = dyn_t.rows[setIndex[0]].insertCell(-1);
			myCell.innerHTML = dyn_t.rows[setIndex[2]].cells[index].innerHTML;
		}
	delItem(setIndex[2]);
	}
}