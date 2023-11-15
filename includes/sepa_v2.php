<?php

require_once("tfpdf/rotate.php");

class PDF extends PDF_Rotate {
	public $invoice;
	public $pgnr;
	public $pdflang;
	
}


class sepa {
	public $pdf;
	public $lastalias = 0;
	public $pdflang;
	
	function sepa () {
		$this->pdf = new PDF('P','mm','A4');
		$this->pdf->SetAutoPageBreak(false);
		$this->pdf->SetMargins(10, 5);
		$this->pdf->SetDrawColor(0,0,0);
		$this->pdf->AddFont('DejaVu','','DejaVuSerifCondensed.ttf',true);
		$this->pdf->AddFont('DejaVu','B','DejaVuSerifCondensed-Bold.ttf',true);
		$this->pdf->SetFont('DejaVu','',10);
		
	}
	
	function header ($invoice) {
		if (file_exists($invoice['logo']))		
			$this->pdf->Image($invoice['logo'],15,3,0,24);
		$this->pdf->SetFontSize(8);
		$this->pdf->SetXY(45,5);
		$this->pdf->MultiCell(35,3.5,
			$invoice['recipient']['name']."\n".
			$invoice['recipient']['street']."\n".
			$invoice['recipient']['zip']." ".$invoice['recipient']['city']."\n"
			.$invoice['recipient']['country']
			,0,L,0);
		$this->pdf->SetXY(85,8);
		$this->pdf->MultiCell(40,3.5,
			$this->pdflang->__('Telephone').": ".$invoice['recipient']['phone']."\n".
			$this->pdflang->__('Email').": ".$invoice['recipient']['email']."\n".
			$this->pdflang->__('VAT-nr').": ".$invoice['recipient']['vatnr']
			,0,L,0);
		$this->pdf->SetFontSize(16);
		$this->pdf->SetXY(130,5);
		$this->pdf->MultiCell(30,3.8,$this->pdflang->__('Invoice'));
		$this->pdf->SetFontSize(10);
		$this->pdf->SetXY(180,5);
		$this->pdf->MultiCell(30,3.8,$this->pdflang->__('Page')." ".$this->pdf->pgnr);
		$this->pdf->SetXY(130,15);
		$this->pdf->MultiCell(25,3.8,$this->pdflang->__('Number').":",0,R,0);
		$this->pdf->SetXY(130,20);
		$this->pdf->MultiCell(25,3.8,$this->pdflang->__('Date').":",0,R,0);
		$this->pdf->SetXY(160,15);
		$this->pdf->MultiCell(40,3.8,$invoice['nr'],0,L,0);
		$this->pdf->SetXY(160,20);
		$this->pdf->MultiCell(40,3.8,$invoice['dat'],0,L,0);
		$this->pdf->Line(10,27,200,27);
	}
	
	function invoice ($invoice) {
		/*language settings*/
		$this->pdflang = new Translator($invoice['locale']); //$outputlanguage: ISO code (example: de,en,fi,sv...) --> these are the names of each file
		$this->pdflang->setPath('lang/pdf/');
		$this->pdf->pdflang &= $this->pdflang;
		
		$bp = 175;
		$this->pdf->invoice = $invoice;
		$this->pdf->pgnr = 1;
		$this->pdf->AddPage();
		$this->header($invoice);
		$this->giro($invoice);
		


		$this->pdf->SetXY(23,32);
		$this->pdf->SetFontSize(14);
		$this->pdf->MultiCell(80,5,$invoice['payer']['name']."\n".(!empty($invoice['payer']['contact'])?$invoice['payer']['contact']."\n":'').$invoice['payer']['street']."\n".$invoice['payer']['zip']." ".$invoice['payer']['city']."\n".$invoice['payer']['country']);
		$this->pdf->SetFontSize(10);
		$this->pdf->SetXY(110,32);
		$this->pdf->MultiCell(35,4.5,$this->pdflang->__('Time').":\n".$this->pdflang->__('Due Date').":\n".$this->pdflang->__('Reference').":\n".$this->pdflang->__('Delay penalty').":\n".$this->pdflang->__('VAT-nr').":");
		$this->pdf->SetXY(150,32);
		$this->pdf->MultiCell(35,4.5,$invoice['terms']." ".$this->pdflang->__('Days')."\n".$invoice['due']."\n".$invoice['custref']."\n".$invoice['rate']."%\n".$invoice['recipient']['vatnr']);
		$this->pdf->Line(10,60,200,60);
		

		$y = 70;
		$this->pdf->SetXY(10,65);
		$this->pdf->SetFontSize(14);
		$this->pdf->MultiCell(0,4.5,$invoice['specification'],0,'L');
		$this->pdf->Ln();
		$this->pdf->SetFontSize(10);
		
		if (!empty($invoice['text']['head'])) {
			$this->pdf->MultiCell(200,4.5,$invoice['text']['head']);
			$this->pdf->Ln();
		}
		$dhead = 0;
		$c = Array(8,45,25,27,27,27);
		$comb = $c[1]+$c[2]+$c[3]+$c[4];
		
		foreach ($invoice['items'] as $nr => $item) {
			$clearvat = $item['vatpros'] * 100;
			$this->pdf->SetFontSize(10);
			if ($this->pdf->GetY() > $bp) {
				$this->pdf->Ln();
				$this->pdf->Cell(0,5,$this->pdflang->__('See next page'),'',0,'R');
				$this->pdf->pgnr++;
				$this->pdf->AddPage();
				$dhead = 0;
				$bp = 270;
				$this->pdf->SetXY(10,40);
			}
			if (!$dhead) {
				$dhead = 1;
				$this->pdf->Cell($c[0],5,'','B');
				$this->pdf->Cell($c[1],5,$this->pdflang->__('Product'),'B');
				$this->pdf->Cell($c[2],5,$this->pdflang->__(''),'B',0,'R');
				$this->pdf->Cell($c[3],5,$this->pdflang->__('Unit price'),'B',0,'R');
				$this->pdf->Cell($c[4],5,$this->pdflang->__('Tot. 0%'),'B',0,'R');
				$this->pdf->Cell($c[5],5,$this->pdflang->__('VAT'),'B',0,'C');
				$this->pdf->Cell(0,5,$this->pdflang->__('Total %'),'B',0,'R');
				$this->pdf->Ln();
			}
			$this->pdf->Cell($c[0],5,($nr+1).".");
			$this->pdf->Cell($c[1],5,$item['prod']);
			$this->pdf->Cell($c[2],5,$item['qty'].' '.$this->pdflang->__($item['unit']),0,0,'R');
			$this->pdf->Cell($c[3],5,number_format($item['price'],2,',',' ')."€",0,0,'R');
			$this->pdf->Cell($c[4],5,"= ".number_format($item['price'] * $item['qty'],2,',',' ')."€",0,0,'R');
			$this->pdf->Cell($c[5],5,number_format($item['vat'] * $item['qty'],2,',',' ')."€ ",0,0,'R');
			$this->pdf->Cell(0,5,number_format($item['total'],2,',',' ')."€",0,0,'R');
			$this->pdf->Ln();
			$this->pdf->SetTextColor(120);
			$this->pdf->SetFontSize(9);
			$this->pdf->Cell($c[0],5,'');
			$this->pdf->Cell($comb,5,$item['text']);
			$this->pdf->Cell($c[5],5,"(".$clearvat."%)",0,0,'R');
			$this->pdf->Ln();
			$this->pdf->SetTextColor(0);
		}
		 
		$this->pdf->SetFont('DejaVu','B',10);
		$this->pdf->Cell($c[0]+$c[1]+$c[2]+$c[3],5,$this->pdflang->__('Total'),'T',0,'R');
		$this->pdf->Cell($c[4],5,number_format($invoice['total'],2,',',' ')."€",'T',0,'R');
		$this->pdf->Cell($c[5]+$c[6],5,number_format($invoice['vat'],2,',',' ')."€",'T',0,'R');
		$this->pdf->Cell(0,5,number_format($invoice['total']+$invoice['vat'],2,',',' ')."€",'T',0,'R');
		$this->pdf->SetFont('DejaVu','',10);

		if (!empty($invoice['text']['tail'])) {
			if ($this->pdf->GetY() > ($bp-10)) {
				$this->pdf->Ln();
				$this->pdf->Cell(0,5,$this->pdflang->__('See next page'),'',0,'R');
				$this->pdf->pgnr++;
				$this->pdf->AddPage();
				$dhead = 0;
				$bp = 270;
				$this->pdf->SetXY(10,40);
			}
	
			$this->pdf->Ln();
			$this->pdf->Ln();
			$this->pdf->SetX(10);
			$this->pdf->MultiCell(200,4.5,$invoice['text']['tail']);
		}
		/*		
		for ($n = $this->lastalias+1;$n <= count($this->pdf->pages);$n++) {
			$this->pdf->pages[$n] = str_replace('{np}', $this->pdf->pgnr, $this->pdf->pages[$n]);
			$this->lastalias = $n;	
		}
		*/
	}

	function giro ($invoice) {
		$y = 198;
		$h1 = 16;
		$h2 = 9;
		$this->pdf->SetLineWidth(0.05);
		$this->pdf->Line(10,$y,200,$y);
		$this->pdf->SetLineWidth(0.2);

		// Botten
		$this->pdf->SetXY(10,$y);
		$this->pdf->Cell(18,$h1,'','B');
		$this->pdf->Cell(90,$h1,'','BL');
		$this->pdf->Cell(82,$h1,'','BL');
		
		$this->pdf->Ln();
		$this->pdf->Cell(18,$h1,'','B');
		$this->pdf->Cell(90,$h1,'','BL');
		$this->pdf->Cell(0,45,'','BL');
		$this->pdf->Ln();
		$this->pdf->Cell(108,$h2,'','B');
		$this->pdf->Cell(12,$h2,'','BL');
		$this->pdf->Cell(0,$h2,'','BL');
		$this->pdf->Ln();
		$this->pdf->Cell(18,$h2,'','B');
		$this->pdf->Cell(90,$h2,'','BL');
		$this->pdf->Cell(12,$h2,'','BL');
		$this->pdf->Cell(35,$h2,'','BL');
		$this->pdf->Cell(0,$h2,'','BL');

		
		// Labels
		$this->pdf->SetFontSize(6);
		$l = $y+1;
		$this->pdf->SetXY(10,$l);
		$this->pdf->MultiCell(18,2.5,"Saajan tilinumero\nMottagarens kontonummer",'','R');
		$this->pdf->SetXY(28,$l);
		$this->pdf->MultiCell(58,2.5,"IBAN",'','L');
		$this->pdf->SetXY(118,$l);
		$this->pdf->MultiCell(53,2.5,"BIC",'','L');
		$l += $h1;
		$this->pdf->SetXY(10,$l);
		$this->pdf->MultiCell(18,2.5,"Saaja\nMottagare",'','R');
		$this->pdf->SetXY(120,$l);
		$this->pdf->MultiCell(35,2.5,"Virtuaaliviivakoodi:\nVirtual referensnummer:",'','L');
		
		$l += $h1;
		$this->pdf->SetXY(10,$l);
		$this->pdf->MultiCell(18,2.5,"Maksajan nimi ja osoite\nBetalarens namn och adress",'','R');
		$l += 29;
		$this->pdf->SetXY(10,$l);
		$this->pdf->MultiCell(18,2.5,"Allekirjoitus\nUnderskrift",'','R');
		$this->pdf->SetXY(118,$l);
		$this->pdf->MultiCell(12,2.5,"Viitenro.\nRef.nr.",'','L');
		$l += $h2;
		$this->pdf->SetXY(10,$l);
		$this->pdf->MultiCell(18,2.5,"Tililtä nro.\nFrån konto nr.",'','R');
		$this->pdf->SetXY(118,$l);
		$this->pdf->MultiCell(12,2.5,"Eräpäivä\nFörf.dag",'','L');
		$this->pdf->SetXY(165,$l);
		$this->pdf->MultiCell(12,2.5,"Euro",'','L');
		$this->pdf->SetFontSize(8);
		$this->pdf->RotatedText(12,$l-4,"TILISIIRTO GIRERING",90);
		
		
		
		$this->pdf->SetFont('DejaVu','B',10);
		$l = $y+2;
		foreach ($invoice['accounts'] as $account) {
			$this->pdf->SetXY(35,$l);
			$this->pdf->Cell(25,2.5,$this->bankname($account['bic']));
			$this->pdf->Cell(70,2.5,$account['iban']);
			$this->pdf->Cell(53,2.5,$account['bic']);
			
			$l += 3.8;
		}
		$l = $y+17;
		$this->pdf->SetXY(28,$l);
		$this->pdf->SetFont('DejaVu','',10);
		$this->pdf->MultiCell(80,3.8,$invoice['recipient']['name']."\n".$invoice['recipient']['street']."\n".$invoice['recipient']['zip']." ".$invoice['recipient']['city']);
		$this->pdf->SetFont('DejaVu','B',10);
		$l = $y+64;
		$this->pdf->SetXY(130,$l);
		$this->pdf->Cell(80,3.8,$this->refFormat($invoice['ref'],4,"R"));
		$l = $y+73;
		$this->pdf->SetXY(130,$l);
		$this->pdf->Cell(75,3.8,$invoice['due']);
		$this->pdf->SetXY(170,$l);
		$this->pdf->Cell(30,3.8,number_format($invoice['total']+$invoice['vat'],2,',',' '),'',0,'R');


		$l = $y+35;
		$this->pdf->SetFont('DejaVu','',10);
		$this->pdf->SetXY(28,$l);
		$this->pdf->MultiCell(80,4,$invoice['payer']['name']."\n".$invoice['payer']['street']."\n".$invoice['payer']['zip']." ".$invoice['payer']['city']);
		
		$this->pdf->SetFont('DejaVu','',7);
		$this->pdf->SetXY(120,$l-12);
		$this->pdf->MultiCell(85,2.5,$invoice['virtual'],'','L');
		
		$this->barcode($invoice['virtual'],278);
		
		
	}
	/* I for opeing in browser or D for forcing download of pdf*/
	function display ($name) {
		$this->pdf->Output($name.".pdf",'I');
	}
	/* F saves to file */
	function savef ($name) {
		$this->pdf->Output($name.".pdf",'F');
	}
	function fetch () {
 		return $this->pdf->Output("",'S');
 	}
	
	function rf_ref ($nr) {
		$nr = strtoupper($nr);
		$tnr = $nr.'RF00';
		$fail = 0;
		$base = "";
		foreach (str_split($tnr) as $char) {
			if (preg_match('/[0-9]/', $char)) {
				$base .= $char;
			} else if (preg_match('/[A-Z]/', $char)) {
				$base .= ord($char) - 55;
			} else {
				$fail = 1;	
			}
		}
		$v = 98 - ($base % 97);
		if ($v < 10)
			$v = '0'.$v;
		if ($fail || strlen($nr) > 25)
			return false;
		else
			return 'RF '.$v.' '.implode(" ",str_split($nr,4));
	}
	function refFormat ($nr,$clen,$dir="L") {
		if ($dir == "L") {
			return implode(" ",str_split($nr,$clen));
		} else {
			$r = strlen($nr)%$clen;
			$f = substr($nr,0,$r);
			$l = substr($nr,$r);
			return $f." ".implode(" ",str_split($l,$clen));
		}
		
	}

	function bankname ($bic) {
		$bank = Array(
			"NDEAFIHH" => "Nordea",
			"HELSFIHH" => "Aktia",
			"OKOYFIHH" => "OKO",
			"AABAFI22" => "Ålandsbanken",
			"DABAFIHH" => "Sampo Pankki",
			"HANDFIHH" => "Handelsbanken",
			"ESSEFIHX" => "SEB",
			"DABAFIHX" => "Danske Bank",
			"DNBAFIHX" => "DnB NOR Bank",
			"TAPIFI22" => "Tapiola Pankki",
			"SWEDFIHH" => "Swedbank",
			"SBANFIHH" => "S-Pankki",
			);
		return $bank[strtoupper($bic)];
	}
	function stripiban ($iban) {
		$nr = substr($iban,4);
		switch (substr($nr,0,1)) {
			case 4:
			case 5:
				$p1 = substr($nr,0,7);
				$p2 = substr($nr,7);
			break;
			default:
				$p1 = substr($nr,0,6);
				$p2 = substr($nr,6);
			break;
		}
		$nr = $p1.ltrim($p2,"0");

		return substr($nr,0,6)."-".substr($nr,6);
	}
	function barcode ($code,$y=120,$x=50) {
 		$this->pdf->Code128($x,$y,$code,105,12);
	}

}
/*
function locale ($loc,$txt) {
	global $lng;
	$pdflanguage = $_SESSION['lang'];

	$lng = new Translator($pdflanguage);//$outputlanguage: ISO code (example: de,en,fi,sv...) --> these are the names of each file
 	$lng->setPath('lang/pdf/');
	//if (function_exists('loc'))
		//return loc($txt,$loc);
		return $lng->__($txt);
	//else
		//return $txt;	
}

*/

?>
