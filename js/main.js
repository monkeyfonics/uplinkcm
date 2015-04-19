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
    var option=document.getElementById('catn').value;
	document.getElementById('vatn').value=option;

    
}
/* check which vat group is selected on old items */
function changeOldVat(name){
	var itemname=name.id;
    var option=document.getElementById(itemname).value;
   	window.alert('vat_'+itemname);
	document.getElementById('vat_'+itemname).value=option;

    addVat(itemname);
}
function stripVat(id) {
	var pricevat = 'pricevat_'+id;
	var defvat = 'vat_'+id;
	var defprice = 'price_'+id;
	var vatprice=document.getElementById(pricevat).value;
	var vat=+document.getElementById(defvat).value + 1;
	
	var newprice = vatprice / vat;
	
	document.getElementById(defprice).value=newprice;
	
}
function addVat(id) {
	var pricevat = 'pricevat_'+id;
	var defvat = 'vat_'+id;
	var defprice = 'price_'+id;
	var price=document.getElementById(defprice).value;
	var vat=+document.getElementById(defvat).value + 1;
	
	var newprice = price * vat;
	
	document.getElementById(pricevat).value=newprice;
	
}
function stripVatn() {
	var vatprice=document.getElementById('pricevatn').value;
	var vat=+document.getElementById('vatn').value + 1;
	
	var newprice = vatprice / vat;
	
	document.getElementById('pricen').value=newprice;
	
	
}

function addVatn() {
	var price=document.getElementById('pricen').value;
	var vat=+document.getElementById('vatn').value + 1;
	
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
	

