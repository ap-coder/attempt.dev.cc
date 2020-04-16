<?php

echo '<pre>'.print_r($_REQUEST, 1).'</pre>';
die;

set_time_limit(0);

//////////////START////////////////
//////////GLOBAL TERMS/////////////

$GTbasedon = "Configuration barcodes generated based on current firmware versions";
$GTwebgenerated = "WEB GENERATED";
$GTtoc = "Table of Contents";
$GTintenblank = "Intentionally Blank";
$GTwebgenconfigguide = "Web Generated Configuration Guide";
$GTconfigguide = "Configuration Guide";
$GTnotechanges = "NOTE: Configuration changes will take effect immediately and be saved to memory";
$GTforquestions = "For questions regarding reader configuration contact support@codecorp.com.";
$GTdefault = "Default";

$GTsetprimaryage = "Under Age";
$GTsetsecondaryage = "Over Age";
$GTsetrealtime = "Real Time Clock";

//////////////END//////////////////
//////////GLOBAL TERMS/////////////

$product = 'CR1400';

if(isset($_REQUEST['product'])) {
$product = $_REQUEST['product'];
}

$font = 'trade';
$font2 = '2';
$global = '';


require('fpdf/tcpdf.php');
require('fpdi/fpdi.php');
require('fpdf/html2text.php');

	$subsections = null;
	$subsection = null; //make array for table of contents if statement below


	if(isset($_REQUEST['barcodes-all'])) {
		$barcodes_all = stripslashes($_REQUEST['barcodes-all']); 
	}

	//second query is to get barcodes that work with selected reader
	
	//Get total num table of contents items and total number of table of contents pages
		$totalTOC = 0;
		$totalRowsTOC = $totalRows * 4;
		$totalTOC = $totalSections * 8;
		$totalTOC = $totalRowsTOC + $totalTOC;
		//$totalTOC = $totalTOC / 240;
		$numTOCPages = $totalTOC / 240;
		$numTOCPages = ceil($numTOCPages);

		$oneCount = 1;
		$fourCount = 1;
		$twentyCount = 1;
		$TOCarray = null;
		$barcodeSpaceArray = null;
		$sort_order = 0;
		$pageCount = $numTOCPages + 2;
		$letter = array(
			1 => "A",
			2 => "A",
			3 => "A",
			4 => "A",
			5 => "B",
			6 => "B",
			7 => "B",
			8 => "B",
			9 => "C",
			10 => "C",
			11 => "C",
			12 => "C",
			13 => "D",
			14 => "D",
			15 => "D",
			16 => "D",
			17 => "E",
			18 => "E",
			19 => "E",
			20 => "E",
			);
			$toPage = null;
			$finalBarcodeTitle = null;
			$finalBarcodeImage = null;
			$subsectionTitles = null;

	# This creates the ARRAY's that will render the QR code table on the PDF.
	# ============================================================================================
	
	foreach( $barcodes_all as $bca ){

		$product = $bca['product'];
		$feature_name = $bca['feature_name'];
		$short_name = $bca['feature_name'];
		$image_file = $bca['image_file'];

		//check first if global that there is content. if not, use english
		
		$default = null;
		//if(preg_match('/.*'.$product.'.*/', $row_ContentRS['default'])) { $default = ' - '.$GTdefault; }

		if($fourCount == 5) { $fourCount = 1; }

		if($twentyCount == 21) {$twentyCount = 1; $pageCount++; }
		
		// Makes each cell of the QR code table.
		if($fourCount == 4) {
			$finalBarcodeTitle[$oneCount] = $GTintenblank;
			$finalBarcodeImage[$oneCount] = "blank.jpg";
			$subsectionTitles[$oneCount] = false;
			$oneCount++;
			$fourCount++;
			$twentyCount++;
			if($fourCount == 5) { $fourCount = 1; }
			if($twentyCount == 21) {$twentyCount = 1; $pageCount++; }
			$barcodeSpaceArray[$oneCount] = $pageCount;
			$TOCarray[$sort_order] = $pageCount;
			$finalBarcodeTitle[$oneCount] = $feature_name;
			$finalBarcodeImage[$oneCount] = "subsection.jpg";
			$subsectionTitles[$oneCount] = true;
			$oneCount++;
			$fourCount++;
			$twentyCount++;
			if($fourCount == 5) { $fourCount = 1; }
			if($twentyCount == 21) {$twentyCount = 1; $pageCount++; }
		}
		
		else {
			$barcodeSpaceArray[$oneCount] = $pageCount;
			$finalBarcodeTitle[$oneCount] = $feature_name;
			$finalBarcodeImage[$oneCount] = "subsection.jpg";
			$subsectionTitles[$oneCount] = true;
			$oneCount++;
			$fourCount++;
			$twentyCount++;
			$TOCarray[$sort_order] = $pageCount;
			if($fourCount == 5) { $fourCount = 1; }
			if($twentyCount == 21) {$twentyCount = 1; $pageCount++; }
		}
		/*
		if($twentyCount == 1 && $row_ContentRS['subset_order'] == $sort_order) {
				$toPage[$sort_order] = $pageCount;
		}
		*/
		$barcodeSpaceArray[$oneCount] = " ".$pageCount." (".$letter[$twentyCount].$fourCount.")";
		$finalBarcodeTitle[$oneCount] = $short_name;
		$finalBarcodeImage[$oneCount] = $image_file;
		$subsectionTitles[$oneCount]  = false;
		$oneCount++;
		$fourCount++;
		$twentyCount++;
		$sort_order = ' -- SORT ORDER --?';
	}

	$pdf = new FPDI();
	//below is for 8.5X11. For now can't be used because document is set up for A4 page size. Will require some work.
	//$pdf = new FPDI('P', 'mm', 'Letter');

	$pdf->SetAutoPageBreak(false);
	$pdf->SetPrintHeader(false);
	$pdf->SetPrintFooter(false);

	if($product == "CR900FD") { $pageCounts = $pdf->setSourceFile("assets/pdfs/AfterTranslation/CR900FD_Template.pdf"); $xhd_spacing = 2; }
		else if($product == "CR1000") { $pageCounts = $pdf->setSourceFile("assets/pdfs/AfterTranslation/CR1000_Template.pdf"); }
		else if($product == "CR1000-XHD") { $pageCounts = $pdf->setSourceFile("assets/pdfs/AfterTranslation/CR1000_Template.pdf"); $xhd_spacing = 8.5; }
		else if($product == "CR1400") { $pageCounts = $pdf->setSourceFile("assets/pdfs/AfterTranslation/CR1400_Template.pdf"); }
		else if($product == "CR1500") { $pageCounts = $pdf->setSourceFile("assets/pdfs/AfterTranslation/CR1500_Template.pdf"); }
		else if($product == "CR1400-XHD") { $pageCounts = $pdf->setSourceFile("assets/pdfs/AfterTranslation/CR1400_Template.pdf"); $xhd_spacing = 8.5; }
		else if($product == "CR1428") { $pageCounts = $pdf->setSourceFile("assets/pdfs/AfterTranslation/CR1428_Template.pdf"); }
		else if($product == "CR8000") { $pageCounts = $pdf->setSourceFile("assets/pdfs/AfterTranslation/CR8000_Template.pdf"); }
		else if($product == "CR950") { $pageCounts = $pdf->setSourceFile("assets/pdfs/AfterTranslation/CR950_Template.pdf"); }
		else if($product == "CR8200") { $pageCounts = $pdf->setSourceFile("assets/pdfs/AfterTranslation/CR8200_Template.pdf"); }
		else if($product == "CR2300") { $pageCounts = $pdf->setSourceFile("assets/pdfs/AfterTranslation/CR2300_Template.pdf"); }
		else if($product == "CR2600") { $pageCounts = $pdf->setSourceFile("assets/pdfs/AfterTranslation/CR2600_Template.pdf"); }
		else if($product == "CR2600-XHD") { $pageCounts = $pdf->setSourceFile("assets/pdfs/AfterTranslation/CR2600_Template.pdf"); $xhd_spacing = 8.5; }
		else if($product == "CR3600") { $pageCounts = $pdf->setSourceFile("assets/pdfs/AfterTranslation/CR3600_Template.pdf"); }
		else if($product == "CR3600-DPM") { $pageCounts = $pdf->setSourceFile("assets/pdfs/AfterTranslation/CR3600_Template.pdf"); $xhd_spacing = 8.5; }
		else if($product == "CR4405") { $pageCounts = $pdf->setSourceFile("assets/pdfs/AfterTranslation/CR44X5_Template.pdf"); }
		else if($product == "CR4900") { $pageCounts = $pdf->setSourceFile("assets/pdfs/AfterTranslation/CR4900_Template.pdf"); }
		else if($product == "CR5000") { $pageCounts = $pdf->setSourceFile("assets/pdfs/AfterTranslation/CR5000_Template.pdf"); }
		else if($product == "CR6000") { $pageCounts = $pdf->setSourceFile("assets/pdfs/AfterTranslation/CR6000_Template.pdf"); }
		else if($product == "T500") { $pageCounts = $pdf->setSourceFile("assets/pdfs/AfterTranslation/T500_Template.pdf"); $xhd_spacing = -6; }
		else { $pageCounts = $pdf->setSourceFile("assets/pdfs/AfterTranslation/CRXXXX_Template.pdf"); }

	$tplIdx = $pdf->importPage(1);
	$tplIdx2 = $pdf->importPage(2);
	$tplIdx3 = $pdf->importPage(3);

	$pdf->addPage();
	$pdf->useTemplate($tplIdx, 0, 0, 210, 300);

	//Title document (red)
		$pdf->SetXY(138.51, 25);
		$pdf->SetFont($font);
		$pdf->SetFontSize(34);
		$pdf->SetTextColor(177,30,42);
		$pdf->Cell(60, 5, strtoupper($GTconfigguide), 0, 0, 'R');

	//place current date under 'web generated' splash page
		$pdf->SetXY(138.51, 156.26);
		$pdf->SetTextColor(255,255,255);
		$pdf->SetFont($font);
		$pdf->SetFontSize(9);
		$pdf->Cell(60, 5, $GTbasedon, 0, 0, 'R');
		$pdf->SetXY(138.72, 160.5);
		$pdf->SetFont($font, 'B');
		$pdf->SetFontSize(12);
		$pdf->Cell(60, 5, $GTwebgenerated, 0, 0, 'R');
		$pdf->SetXY(138.6, 165);
		$pdf->SetFont($font);
		$pdf->SetFontSize(9);
		$pdf->Cell(60, 5, date("n-j-Y"), 0, 1, 'R');

	// if($notes) {
	// 	$pdf->SetXY(5, 250);
	// 	$pdf->SetTextColor(0,0,0);
	// 	$pdf->MultiCell(125,4,"Author: ".$author."\nNotes:\n".$notes,0,'L');
	// }

	$pdf->addPage();
	$pdf->useTemplate($tplIdx2, 0, 0, 210, 300);

	//first TOC page
		$pdf->SetXY(16, 17.5);
		$pdf->SetFont($font, 'B');
		$pdf->SetTextColor(255,255,255);
		$pdf->SetFontSize(15);
		$pdf->Cell(60, 5, $GTtoc, 0, 0);

		$pdf->SetFont($font.$font2);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFontSize(6.7);

	//set footer texts
		$pdf->SetXY(17, -7.35);
		$pdf->Cell(60, 5, $GTwebgenconfigguide, 0, 0);
		$pdf->SetFont($font.$font2, 'I');
		$pdf->SetXY(80, -6);
		$pdf->MultiCell(110, 5, $GTforquestions,0,'R');
		$pdf->SetFont($font);
		$pdf->SetFontSize(8);

		$yVar = 33;
		$sort_order = 0;

		$oneCount = 1;
		$fourCount = 1;

		$pdf->SetXY(-17, -6);
		$pdf->SetTextColor(128,130,133);
		$pdf->Write(0, $pdf->PageNo());
		$pdf->SetTextColor(0,0,0);


	foreach( $barcodes_all as $bca ){

		$product = $bca['product'];
		$feature_name = $bca['feature_name'];
		$short_name = $bca['feature_name'];

		//check first if global that there is content. if not, use english

		if($fourCount == 5) { $fourCount = 1; }

		// TABLE OF CONTENTS
			if($yVar > 273) {
				$pdf->addPage();
				
				//set toc, footer texts
				$pdf->useTemplate($tplIdx2, 0, 0, 210, 300);
				$pdf->SetXY(16, 17);
				$pdf->SetFont($font, 'B');
				$pdf->SetTextColor(255,255,255);
				$pdf->SetFontSize(13);
				$pdf->Cell(60, 5, $GTtoc, 0, 0);
				//set footer texts
				$pdf->SetFont($font.$font2);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFontSize(6.7);
				$pdf->SetXY(17, -7.35);
				$pdf->Cell(60, 5, $GTwebgenconfigguide, 0, 0);
				$pdf->SetFont($font.$font2, 'I');
				$pdf->SetXY(80, -6);
				$pdf->MultiCell(110, 5, $GTforquestions,0,'R');

				//set page number
				$pdf->SetFont($font);
				$pdf->SetTextColor(255,255,255);
				$pdf->SetFontSize(8);
				$pdf->SetXY(-17, -6);
				$pdf->SetTextColor(128,130,133);
				$pdf->Write(0, $pdf->PageNo());
				$pdf->SetTextColor(0,0,0);
				$yVar = 33;			
			}

		if($fourCount == 4) {
			$oneCount++;
			$fourCount = 1;
		}
		if($yVar != 33) {
			$yVar = $yVar + 4;
		}

		$pdf->SetXY(20, $yVar);
		$pdf->SetFont($font, 'b');
		$pdf->SetFontSize(9);
		$pdf->Write(0, $feature_name);
		
		do {
			$pdf->Write(0, '.');
			//$pdf->Write(0, $pdf->GetX());
			$xVar = $pdf->GetX();
		} while ($xVar < 150);

		//writing subsection page counts
		//OLD - $pdf->Write(0, " ".$barcodeSpaceArray[$oneCount]);
		$pdf->SetXY(149.5, $pdf->GetY());
		
		
		//if subsection spans multiple pages, write to page e.g. 3-4 or 3-6
		//OLD - if($toPage[$row_ContentRS['subset_order']] != $barcodeSpaceArray[$oneCount] && $toPage[$row_ContentRS['subset_order']]) { $pdf->Write(0, "-".$toPage[$row_ContentRS['subset_order']]); }
		// if($toPage[$row_ContentRS['subset_order']] != $barcodeSpaceArray[$oneCount] && $toPage[$row_ContentRS['subset_order']]) { 
		// 	$barcodeSpaceArray[$oneCount] = $barcodeSpaceArray[$oneCount]."-".$toPage[$row_ContentRS['subset_order']]; 
		// }
		
		$pdf->Cell(15, 2, " ".$barcodeSpaceArray[$oneCount], 0, 0, 'L');
		$oneCount++;
		$fourCount++;
		$yVar = $yVar + 4;
		$pdf->SetFont($font);

		$pdf->SetFont($font.$font2);
		$pdf->SetFontSize(8);
		$pdf->SetXY(20, $yVar);

		$default = null;

		//if(preg_match('/.*'.$product.'.*/', $row_ContentRS['default'])) { $default = ' - '.$GTdefault; }

		$pdf->Write(0, $short_name);

		do {
			$pdf->Write(0, '.');
			//$pdf->Write(0, $pdf->GetX());
			$xVar = $pdf->GetX();
		} while ($xVar < 150);

		//$pdf->Write(0, $barcodeSpaceArray[$oneCount]);
		$pdf->SetXY(149.9, $pdf->GetY());
		$pdf->Cell(15, 2, $barcodeSpaceArray[$oneCount], 0, 0, 'L');
			
		$yVar = $yVar + 4;
		$oneCount++;
		$fourCount++;
		$sort_order = '-- SORT ORDER ? --';
	}



	//First barcodes page
	$pdf->addPage();
	$pdf->useTemplate($tplIdx3, 0, 0, 210, 300);

	//set header/footer texts
		$pdf->SetXY(16, 18.2-($font2/4));
		$pdf->SetFont('trade', 'B');
		$pdf->SetTextColor(255,255,255);
		$pdf->SetFontSize(15);
		$pdf->Cell(60, 5, $product, 0, 0);
		$pdf->SetFont($font, 'B');
		$pdf->SetXY(33.5+$xhd_spacing, 16.7+($font2/2));
		$pdf->Cell(60, 5, $GTconfigguide, 0, 0);

		$pdf->SetXY(-110, 18.6);
		$pdf->SetFont($font, 'I');
		$pdf->SetFontSize(7);
		$pdf->MultiCell(96, 5, $GTnotechanges, 0,'R');

		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont($font.$font2);
		$pdf->SetFontSize(6.7);
		$pdf->SetXY(17, -7.35);
		$pdf->Cell(60, 5, $GTwebgenconfigguide, 0, 0);
		$pdf->SetFont($font.$font2, 'I');

		$pdf->SetXY(80, -6);
		$pdf->MultiCell(110, 5, $GTforquestions,0,'R');

		$pdf->SetFont($font);
		$pdf->SetFontSize(8);
		$pdf->SetXY(-17, -6);
		$pdf->SetTextColor(128,130,133);
		$pdf->Write(0, $pdf->PageNo());
		$pdf->SetXY(12.3, 25.8);
		$pdf->SetFillColor(128, 130, 133);

		$oneCount2 = 1;
		$twentyCount = 1;

		$pdf->SetFont($font, 'B');
		$pdf->SetTextColor(128,130,133);
		$pdf->SetFontSize(10);

	do {

		if($twentyCount == 21) {
			$twentyCount = 1;
			$pdf->addPage();
			$pdf->useTemplate($tplIdx3, 0, 0, 210, 300);
			//set header/footer texts
			$pdf->SetXY(16, 18.2-($font2/4));
			$pdf->SetFont('trade', 'B');
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFontSize(15);
			$pdf->Cell(60, 5, $product, 0, 0);
			$pdf->SetFont($font, 'B');
			$pdf->SetXY(33.5+$xhd_spacing, 16.7+($font2/2));
			$pdf->Cell(60, 5, $GTconfigguide, 0, 0);
				
			$pdf->SetXY(-110, 18.6);
			$pdf->SetFont($font, 'I');
			$pdf->SetFontSize(7);
			$pdf->MultiCell(96, 5, $GTnotechanges, 0,'R');
			
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetFont($font.$font2);
			$pdf->SetFontSize(6.7);
			$pdf->SetXY(17, -7.35);
			$pdf->Cell(60, 5, $GTwebgenconfigguide, 0, 0);
			$pdf->SetFont($font.$font2, 'I');
			
			$pdf->SetXY(80, -6);
			$pdf->MultiCell(110, 5, $GTforquestions,0,'R');
			
			$pdf->SetFont($font);
			$pdf->SetFontSize(8);
			$pdf->SetXY(-17, -6);
			$pdf->SetTextColor(128,130,133);
			$pdf->Write(0, $pdf->PageNo());
			$pdf->SetXY(12.3, 25.8);
			$pdf->SetFillColor(128, 130, 133);
					
			$pdf->SetFont($font, 'B');
			$pdf->SetFontSize(10);
			$pdf->SetTextColor(128,130,133);
			$pdf->SetXY(12.3, 25.8);
		}	

		$pdf->SetFontSize(10);
		$x = $pdf->GetX();
		$y = $pdf->GetY();
		if($subsectionTitles[$oneCount2] == true) { $pdf->MultiCell(46.2,15," ",0,'C', true); }
		else { $pdf->SetXY($x, $y + 1); $pdf->MultiCell(46.2,5,$finalBarcodeTitle[$oneCount2],0,'C'); }
		$pdf->SetXY($x + 46.2, $y);
		$x = $pdf->GetX();
		$y = $pdf->GetY();
		
		
		if(!$finalBarcodeTitle[$oneCount2+1]) { $pdf->SetXY($x, $y + 1); $pdf->MultiCell(46.2,5,$GTintenblank,0,'C'); $finalBarcodeImage[$oneCount2+1] = "blank.jpg"; }
		if($subsectionTitles[$oneCount2+1] == true) { $pdf->MultiCell(46.3,15," ",0,'C', true); }
		else { $pdf->SetXY($x, $y + 1); $pdf->MultiCell(46.2,5,$finalBarcodeTitle[$oneCount2+1],0,'C'); }
		$pdf->SetXY($x + 46.2, $y);
		$x = $pdf->GetX();
		$y = $pdf->GetY();
		
		
		if(!$finalBarcodeTitle[$oneCount2+2]) { $pdf->SetXY($x, $y + 1); $pdf->MultiCell(46.2,5,$GTintenblank,0,'C'); $finalBarcodeImage[$oneCount2+2] = "blank.jpg"; }
		if($subsectionTitles[$oneCount2+2] == true) { $pdf->MultiCell(46.2,15," ",0,'C', true); }
		else { $pdf->SetXY($x, $y + 1); $pdf->MultiCell(46.6,3,$finalBarcodeTitle[$oneCount2+2],0,'C'); }
		$pdf->SetXY($x + 46.2, $y);
		$x = $pdf->GetX();
		$y = $pdf->GetY();
		
		
		if(!$finalBarcodeTitle[$oneCount2+3]) { $pdf->SetXY($x, $y + 1); $pdf->MultiCell(46.2,5,$GTintenblank,0,'C'); $finalBarcodeImage[$oneCount2+3] = "blank.jpg"; }
		else { $pdf->SetXY($x, $y + 1); $pdf->MultiCell(46.2,5,$finalBarcodeTitle[$oneCount2+3],0,'C'); }
		$pdf->SetXY($x + 46.2, $y);
		$pdf->Cell(1,15," ",0,1,'C');
		$pdf->SetX(12.3);
		
		
		if($subsectionTitles[$oneCount2] == true) { $x = $pdf->GetX(); $y = $pdf->GetY(); $pdf->SetTextColor(255,255,255); $pdf->SetFontSize(14); $pdf->Cell(46.2,37," ",0,0,'C', true); $pdf->SetXY($x, $y); $pdf->MultiCell(46.2,5,$finalBarcodeTitle[$oneCount2],0,'C'); $pdf->SetXY($x, $y); $pdf->Cell(46.2,37," ",0,0,'C'); $pdf->SetTextColor(128,130,133); $pdf->SetFontSize(12); }
		else { 
			list($imgWidth, $imgHeight) = getimagesize('assets/barcode_sources/'.$finalBarcodeImage[$oneCount2]);
			$imgWidth = $imgWidth / 22;
			$imgHeight = $imgHeight / 22;
			$imgWidth2 = 46.2 - $imgWidth;
			$imgHeight2 = 38 - $imgHeight;
			$imgWidth2 = $imgWidth2 / 2;
			$imgHeight2 = $imgHeight2 / 2;
			if($finalBarcodeImage[$oneCount2] == 'blank.jpg') {
			$pdf->Cell(46.2,37,' ',0,1,'C');
			}
			else {
			$pdf->Cell(46.2,37,$pdf->Image('assets/barcode_sources/'.$finalBarcodeImage[$oneCount2],$pdf->GetX()+$imgWidth2,$pdf->GetY()+$imgHeight2, $imgWidth, $imgHeight),0,0,'C'); 
			}
		}

		if($subsectionTitles[$oneCount2+1] == true && $finalBarcodeTitle[$oneCount2+1]) { $x = $pdf->GetX(); $y = $pdf->GetY(); $pdf->SetTextColor(255,255,255); $pdf->SetFontSize(14); $pdf->Cell(46.3,37," ",0,0,'C', true); $pdf->SetXY($x, $y); $pdf->MultiCell(46.3,5,$finalBarcodeTitle[$oneCount2+1],0,'C'); $pdf->SetXY($x, $y); $pdf->Cell(46.3,37," ",0,0,'C'); $pdf->SetTextColor(128,130,133); $pdf->SetFontSize(12); }
		else { 
			//$pdf->Cell(46.3,37,$pdf->Image('assets/barcode_sources/'.$finalBarcodeImage[$oneCount2+1],$pdf->GetX()+16,$pdf->GetY()+8, 15),0,0,'C');
			list($imgWidth, $imgHeight) = getimagesize('assets/barcode_sources/'.$finalBarcodeImage[$oneCount2+1]);
			$imgWidth = $imgWidth / 22;
			$imgHeight = $imgHeight / 22;
			$imgWidth2 = 46.2 - $imgWidth;
			$imgHeight2 = 38 - $imgHeight;
			$imgWidth2 = $imgWidth2 / 2;
			$imgHeight2 = $imgHeight2 / 2;
			if($finalBarcodeImage[$oneCount2+1] == 'blank.jpg') {
			$pdf->Cell(46.2,37,' ',0,1,'C');
			}
			else {
			$pdf->Cell(46.2,37,$pdf->Image('assets/barcode_sources/'.$finalBarcodeImage[$oneCount2+1],$pdf->GetX()+$imgWidth2,$pdf->GetY()+$imgHeight2, $imgWidth, $imgHeight),0,0,'C'); 
			}
		}

		if($subsectionTitles[$oneCount2+2] == true && $finalBarcodeTitle[$oneCount2+2]) { $x = $pdf->GetX(); $y = $pdf->GetY(); $pdf->SetTextColor(255,255,255); $pdf->SetFontSize(14); $pdf->Cell(46.2,37," ",0,0,'C', true); $pdf->SetXY($x, $y); $pdf->MultiCell(46.2,5,$finalBarcodeTitle[$oneCount2+2],0,'C'); $pdf->SetXY($x, $y); $pdf->Cell(46.2,37," ",0,0,'C'); $pdf->SetTextColor(128,130,133); $pdf->SetFontSize(12); }
		else { 
			//$pdf->Cell(46.2,37,$pdf->Image('assets/barcode_sources/'.$finalBarcodeImage[$oneCount2+2],$pdf->GetX()+16,$pdf->GetY()+8, 15),0,0,'C'); 
			list($imgWidth, $imgHeight) = getimagesize('assets/barcode_sources/'.$finalBarcodeImage[$oneCount2+2]);
			$imgWidth = $imgWidth / 22;
			$imgHeight = $imgHeight / 22;
			$imgWidth2 = 46.2 - $imgWidth;
			$imgHeight2 = 38 - $imgHeight;
			$imgWidth2 = $imgWidth2 / 2;
			$imgHeight2 = $imgHeight2 / 2;
			if($finalBarcodeImage[$oneCount2+2] == 'blank.jpg') {
			$pdf->Cell(46.2,37,' ',0,1,'C');
			}
			else {
			$pdf->Cell(46.2,37,$pdf->Image('assets/barcode_sources/'.$finalBarcodeImage[$oneCount2+2],$pdf->GetX()+$imgWidth2,$pdf->GetY()+$imgHeight2, $imgWidth, $imgHeight),0,0,'C');
			}
		}

		//if($finalBarcodeTitle[$oneCount2+1]) {
		//$pdf->Cell(46.2,37,$pdf->Image('assets/barcode_sources/'.$finalBarcodeImage[$oneCount2+3],$pdf->GetX()+16,$pdf->GetY()+8, 15),0,1,'C');// }
		list($imgWidth, $imgHeight) = getimagesize('assets/barcode_sources/'.$finalBarcodeImage[$oneCount2+3]);
		$imgWidth = $imgWidth / 22;
		$imgHeight = $imgHeight / 22;
		$imgWidth2 = 46.2 - $imgWidth;
		$imgHeight2 = 38 - $imgHeight;
		$imgWidth2 = $imgWidth2 / 2;
		$imgHeight2 = $imgHeight2 / 2;

		if($finalBarcodeImage[$oneCount2+3] == 'blank.jpg') {
			$pdf->Cell(46.2,37,' ',0,1,'C');
		}
		else {
			$pdf->Cell(46.2,37,$pdf->Image('assets/barcode_sources/'.$finalBarcodeImage[$oneCount2+3],$pdf->GetX()+$imgWidth2,$pdf->GetY()+$imgHeight2, $imgWidth, $imgHeight),0,1,'C');
		}

		$pdf->SetX(12.3);

		$twentyCount = $twentyCount + 4;
		$oneCount2 = $oneCount2 + 4;

	} while($oneCount2 < $oneCount);

	if($twentyCount < 21) {
		$pdf->SetY($pdf->GetY()+1);
		$pdf->SetFont($font, 'B');
		$pdf->SetFontSize(10);
		do {
			if($twentyCount == 1) { $y = 25.8; }
			else if($twentyCount == 5) { $y = 78.8; }
			else if($twentyCount == 9) { $y = 130.8; }
			else if($twentyCount == 13) { $y = 182.8; }
			else if($twentyCount == 17) { $y = 234.8; }
			$x = $pdf->GetX();
			//$y = $pdf->GetY();
			$pdf->SetY($y);
			$pdf->MultiCell(46.2,5,$GTintenblank,0,'C');
			$pdf->SetXY($x + 46.2, $y);
			$x = $pdf->GetX();
			//$y = $pdf->GetY();
			$pdf->MultiCell(46.2,5,$GTintenblank,0,'C');
			$pdf->SetXY($x + 46.2, $y);
			$x = $pdf->GetX();
			//$y = $pdf->GetY();
			$pdf->MultiCell(46.2,5,$GTintenblank,0,'C');
			$pdf->SetXY($x + 46.2, $y);
			$x = $pdf->GetX();
			//$y = $pdf->GetY();
			$pdf->MultiCell(46.2,5,$GTintenblank,0,'C');
			$pdf->SetXY($x + 46.2, $y);
			$pdf->Cell(1,15," ",0,1,'C');
			$pdf->SetX(12.3);
			if($twentyCount != 17 ) {
				$pdf->Cell(46.2,37," ",0,0,'C');
				$pdf->Cell(46.2,37," ",0,0,'C');
				$pdf->Cell(46.2,37," ",0,0,'C');
				$pdf->Cell(46.2,37," ",0,1,'C');
				$pdf->SetX(12.3);
			}
			else {
				$pdf->Cell(46.2,27," ",0,0,'C');
				$pdf->Cell(46.2,27," ",0,0,'C');
				$pdf->Cell(46.2,27," ",0,0,'C');
				$pdf->Cell(46.2,27," ",0,0,'C');
			}
		$twentyCount = $twentyCount + 4;
		} while($twentyCount < 21);
	}

	// OUTPUT OR DOWNLOAD
	if(isset($_REQUEST['barcodes-print-save']) && $_REQUEST['barcodes-print-save'] == "save") {
		$pdf->Output('WebGenerated-Configuration-Guide.pdf', 'D');
		echo "<script>window.close();</script>";
	}
	else {
		$pdf->Output();
		//$pdf->Output($_REQUEST['global'].$product.'_WebGenerated-Configuration-Guide.pdf', 'D');
	}
