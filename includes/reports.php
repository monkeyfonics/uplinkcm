<?php

require_once("tfpdf/tfpdf.php");

class PDF extends tFPDF {
	var $info;
	
}

class report {
	var $pdf;
	
	function report () {
		$this->pdf = new PDF('L','mm','A4');
		$this->pdf->AddPage();
		$this->pdf->SetDrawColor(0,0,0);
		$this->pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
		$this->pdf->AddFont('DejaVu','B','DejaVuSansCondensed-Bold.ttf',true);
		$this->pdf->SetFont('DejaVu','',10);
		
		
	}
	function content ($info) {
		
		$this->pdf->info = $info;
		$this->pdf->SetFont('DejaVu','B',14);
		$this->pdf->Cell(0,10,$info['account'],0,1,C);
		// Line break
		$this->pdf->Cell(0,10,$info['header'],0,1,C);
		
    	
		$this->pdf->SetFont('DejaVu','',12);
		$this->pdf->Cell(0,10,$info['from']." - ".$info['to'],0,1,C);
		
		//cell widths
		$cell1 = 40;
		$cell2 = 40;
		$cell3 = 75;
		$cell4 = 85;
		$cell5 = 30;
		$collcomb = $cell1+$cell2+$cell3+$cell4;
		//loop header names
		foreach ($info['hitem'] as $hitems) {
			$this->pdf->Cell($cell1,5,$hitems['date'],B,0,C);
			$this->pdf->Cell($cell2,5,$hitems['invid'],B,0,C);
			$this->pdf->Cell($cell4,5,$hitems['name'],B,0,C);
			$this->pdf->Cell($cell3,5,$hitems['contact'],B,0,C);
			$this->pdf->Cell($cell5,5,$hitems['amount'],B,0,C);
		}
		$this->pdf->Ln();
		$this->pdf->SetFont('DejaVu','',10);
		$oddeven = 0;
		$this->pdf->SetFillColor(225);
		//loop content
		foreach ($info['items'] as $nr => $item) {
			$this->pdf->Cell($cell1,5,$item['date'],1,0,C,$oddeven);
			$this->pdf->Cell($cell2,5,$item['invid'],1,0,L,$oddeven);
			$this->pdf->Cell($cell4,5,$item['name'],1,0,L,$oddeven);
			$this->pdf->Cell($cell3,5,$item['contact'],1,0,L,$oddeven);
			$this->pdf->Cell($cell5,5,$item['amount'],1,0,R,$oddeven);
			$this->pdf->Ln();
			if ($oddeven == 0) {
				$oddeven = 1;
				
			} else {
				$oddeven = 0;
			}
		}
		$this->pdf->SetFont('DejaVu','B',10);
		$this->pdf->Cell($collcomb,5,$info['totalname'],0,0,R);
		$this->pdf->Cell($cell5,5,$info['total'],0,0,R);
	}
	
	function display ($name) {
		$this->pdf->Output($name.".pdf",'I');
	}
}


?>