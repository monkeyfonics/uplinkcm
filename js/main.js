/* check password that its the same */

function checkpass()
	{
		var jspas1 = document.getElementById("pass1").value;
		var jspas2 = document.getElementById("pass2").value;
		
		if (jspas1 == jspas2)
		{
			document.getElementById("pass2").setAttribute("class", "correct");
			document.getElementById("psubmit").disabled=false;
		}
		else
		{
			document.getElementById("psubmit").disabled=true;
			document.getElementById("pass2").setAttribute("class", " ");
		}
	}
	
function alerti() {
	window.alert("Hello! I am an alert box!!");
}
/* check which vat group is selected on new items */
function changeVat(){
    var option=document.getElementById('catn');
    var vat=option.options[option.selectedIndex].id;
	document.getElementById('vatn').value=vat;

    
}
/* check which vat group is selected on old items */
function changeOldVat(name){
	var itemname=name.id;
    var option=document.getElementById(itemname);
    var vat=option.options[option.selectedIndex].id;
   	window.alert(vat);
	document.getElementById('vat_'+itemname).value=vat;

    addVat(itemname);
}
function stripVat(id) {
	var pricevat = 'pricevat_'+id;
	var defvat = 'vat_'+id;
	var defprice = 'price_'+id;
	var vatprice=document.getElementById(pricevat).value;
	var vat=+document.getElementById(defvat).value + 1;
	/*converting price to use dot instead of comma*/
	vatprice=vatprice.replace(",", ".");
	var newprice = vatprice / vat;
	
	document.getElementById(defprice).value=newprice;
	
}
function addVat(id) {
	var pricevat = 'pricevat_'+id;
	var defvat = 'vat_'+id;
	var defprice = 'price_'+id;
	var price=document.getElementById(defprice).value;
	var vat=+document.getElementById(defvat).value + 1;
	/*converting price to use dot instead of comma*/
	price=price.replace(",", ".");
	
	var newprice = price * vat;

	document.getElementById(pricevat).value=newprice;
	
}
function stripVatn() {
	var vatprice=document.getElementById('pricevatn').value;
	var vat=+document.getElementById('vatn').value + 1;
	/*converting price to use dot instead of comma*/
	vatprice=vatprice.replace(",", ".");
	var newprice = vatprice / vat;
	
	document.getElementById('pricen').value=newprice;
	
	
}

function addVatn() {
	var price=document.getElementById('pricen').value;
	var vat=+document.getElementById('vatn').value + 1;
	/*converting price to use dot instead of comma*/
	price=price.replace(",", ".");
	var newprice = price * vat;
	
	document.getElementById('pricevatn').value=newprice;
	
}

function disableEnd()
	{
		var ongoing = document.getElementById('ongoing').value;
		var rec = document.getElementById('rec').value;
		
		if (ongoing == 't' || rec == 0)
		{
			
			
			document.getElementById('end').type = 'hidden';
			document.getElementById('endshown').type = 'text';
		}
		else
		{
			
			document.getElementById('end').type = 'text';
			document.getElementById('endshown').type = 'hidden';
			
		}
	}
	
function popItPrint(id)
	{
		var lastpopit = localStorage.getItem("popitid");
		//clear all popits first
		var popits = document.getElementsByClassName('popitbox');
		for(var i=0; i<popits.length; i++) { 
		  popits[i].style.display='none';
		}
		var tds = document.getElementsByClassName('selclass');
		for(var i=0; i<tds.length; i++) { 
		  tds[i].className = "";
		}
		//show selected popit
		document.getElementById('infopopprint_'+id).style.display='block';
		document.getElementById('print_td_'+id).className += " selclass";
		//remove if same id is clicked
		if (lastpopit == id) {
			document.getElementById('infopopprint_'+id).style.display='none';
			document.getElementById('print_td_'+id).className = "";
		}
		localStorage.setItem("popitid", id);
		
	}
function popItEmail(id)
	{
		var lastpopit = localStorage.getItem("popitid");
		//clear all popits first
		var popits = document.getElementsByClassName('popitbox');
		for(var i=0; i<popits.length; i++) { 
		  popits[i].style.display='none';
		}
		var tds = document.getElementsByClassName('selclass');
		for(var i=0; i<tds.length; i++) { 
		  tds[i].className = "";
		}
		//show selected popit
		document.getElementById('infopopemail_'+id).style.display='block';
		document.getElementById('email_td_'+id).className += " selclass";
		//remove if same id is clicked
		if (lastpopit == id) {
			document.getElementById('infopopemail_'+id).style.display='none';
			document.getElementById('email_td_'+id).className = "";
		}
		localStorage.setItem("popitid", id);
		
	}
