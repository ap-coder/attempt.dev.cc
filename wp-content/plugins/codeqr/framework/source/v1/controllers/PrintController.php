<?php
namespace CODEQR;

class PrintController extends Controller {

	public function __construct(){

		parent::__construct();

		define('MAX_PAGE_LEN_Y', 260);

		$this->GTbasedon = "Configuration barcodes generated based on current firmware versions";
		$this->GTwebgenerated = "WEB GENERATED";
		$this->GTtoc = "Table of Contents";
		$this->GTintenblank = "Intentionally Blank";
		$this->GTwebgenconfigguide = "";
		$this->GTconfigguide = "Configuration Guide";
		$this->GTnotechanges = "";
		$this->GTforquestions = "For questions regarding reader configuration contact support@codecorp.com.";
		$this->GTdefault = "Default";
		$this->GTsetprimaryage = "Under Age";
		$this->GTsetsecondaryage = "Over Age";
		$this->GTsetrealtime = "Real Time Clock";

		/*$totalRowsTOC = $wpdb->num_rows * 4;
		$totalTOC = $totalRowsTOC + 8;
		$numTOCPages = $totalTOC / 240;
		$numTOCPages = ceil($numTOCPages);*/

		$this->pageCount = 1;

		$this->yVar = 33;
		$this->toPage = null;
		$this->TOCarray = null;
		$this->barcodeSpaceArray = null;
		$this->finalBarcodeTitle = null;
		$this->finalBarcodeImage = null;
		$this->subsectionTitles = null;
		$this->gridTiles = null;

		$this->letter = array(
			1 => "A1",
			2 => "A2",
			3 => "A3",
			4 => "A4",
			5 => "B1",
			6 => "B2",
			7 => "B3",
			8 => "B4",
			9 => "C1",
			10 => "C2",
			11 => "C3",
			12 => "C4",
			13 => "D1",
			14 => "D2",
			15 => "D3",
			16 => "D4",
			17 => "E1",
			18 => "E2",
			19 => "E3",
			20 => "E4",
		);

		$this->font = 'trade';
		$this->font2 = '2';
		$this->global = '';
		$this->xhd_spacing = 2;

		global $wpdb;
		$this->cover_pdf = $wpdb->get_var("SELECT pdf_cover as pdf_cover FROM wp_codeqr_products WHERE `product_id` = '{$_POST['product']}' LIMIT 1");
		$this->model_number = $wpdb->get_var("SELECT model_number as model_number FROM wp_codeqr_products WHERE `product_id` = '{$_POST['product']}' LIMIT 1");

		require(QRROOT_PATH . 'include/fpdf/tcpdf.php');
		require(QRROOT_PATH . 'include/fpdi/fpdi.php');
		require(QRROOT_PATH . 'include/fpdf/html2text.php');
		$this->pdf = new \FPDI();

		# WE NEED TO GET THE CATEGORIES FROM THE PROVIDED FEATURES FIRST
		$this->categories_a = $this->getUniqueCategories();


	}

	public function printpdfPOST(){
		
		set_time_limit(0);

		$this->pdf->SetAutoPageBreak(false);
		$this->pdf->SetPrintHeader(false);
		$this->pdf->SetPrintFooter(false);
		
		if( !is_null($this->cover_pdf) ){
			$destination =  rtrim(dirname(QRROOT_PATH), "/") . '/pdfs/uploaded/' . $this->cover_pdf;
			$this->pdf->setSourceFile($destination);
		}

		$this->tplIdx = $this->pdf->importPage(1);
		$this->tplIdx2 = $this->pdf->importPage(2);
		$this->tplIdx3 = $this->pdf->importPage(3);

		// Just adding the templates to the overall PDF
		// ----------------------------------------------------------------
		$this->addCoverPage();
		$this->addTOCPage();

		// This is the data that will be looped for the TOC and Grid
		// ----------------------------------------------------------------
		$this->setupData();

		// Render the pages with the data that we setup
		// ----------------------------------------------------------------
		$this->renderTOCPage();
		$this->renderFeatureGrid();

		$this->pdf->Output($this->model_number.'_Configuration-Guide.pdf', 'D');
		die;
	}

	private function addCoverPage(){

		$this->pdf->addPage();
		$this->pdf->useTemplate($this->tplIdx, 0, 0, 210, 300);

		//place current date under 'web generated' splash page
		$this->pdf->SetXY(138.51, 156.26);
		$this->pdf->SetTextColor(255,255,255);
		$this->pdf->SetFont($this->font);
		$this->pdf->SetFontSize(9);
		$this->pdf->Cell(60, 5, $this->GTbasedon, 0, 0, 'R');
		$this->pdf->SetXY(138.72, 160.5);
		$this->pdf->SetFont($this->font, 'B');
		$this->pdf->SetFontSize(12);
		$this->pdf->Cell(60, 5, $this->GTwebgenerated, 0, 0, 'R');
		$this->pdf->SetXY(138.6, 165);
		$this->pdf->SetFont($this->font);
		$this->pdf->SetFontSize(9);
		$this->pdf->Cell(60, 5, date("n-j-Y"), 0, 1, 'R');
	}

	private function addTOCPage(){

		$this->pdf->addPage();
		$this->pdf->useTemplate($this->tplIdx2, 0, 0, 210, 300);
		
		//set footer texts
		$this->pdf->SetXY(17, -7.35);
		$this->pdf->SetFont($this->font.$this->font2, 'I');
		$this->pdf->SetXY(80, -6);
		$this->pdf->SetFont($this->font);
		$this->pdf->SetFontSize(8);
		$this->pdf->SetXY(-17, -6);
		$this->pdf->SetTextColor(128,130,133);
		$this->pdf->Write(0, $this->pdf->PageNo());
		$this->pdf->SetTextColor(0,0,0);
	}

	private function setupData(){


		global $wpdb;

		# This creates the ARRAY's that will render the QR code table on the PDF.
		# ========================================================================
		
		$twentyCount = 1;
		$cat_count = 1;

		foreach( $_POST['cat'] as $cat => $features ){

			$gridTileArray = array(
				'type' => 'category',
				'title' => $cat,
				'page' => $this->pageCount,
			);

			//echo '<pre>'.print_r('twentyCount1 : ' .$twentyCount, 1).'</pre>';
			//echo '<pre>'.print_r($gridTileArray, 1).'</pre>';
			//echo '<pre>'.print_r('-------------------------------------------', 1).'</pre>';


			// new row, new category
			$this->gridTiles[] = $gridTileArray;
			if(++$twentyCount > 20) { $twentyCount = 1; $this->pageCount++; }

			// run thru the features
			foreach( $features as $n => $feature ){

				// krs I moved this adding of the category up before adding the feature since if already added the last feaure then no need to add a new row
				if(in_array($twentyCount, [1, 5, 9, 13, 17])){ 
					
					//echo '<pre>'.print_r('<strong>twentyCount at limit</strong> : ' .$twentyCount, 1).'</pre>';
					//echo '<pre>'.print_r('<strong>modulus</strong> : ' .($twentyCount % 4), 1).'</pre>';
					//echo '<pre>'.print_r('-------------------------------------------', 1).'</pre>';

					// echo '<pre>'.print_r('twentyCount3 : ' .$twentyCount, 1).'</pre>';
					$gridTileArray = array(
						'type' => 'category',
						'title' => $cat,
						'page' => $this->pageCount,
					);

					// echo '<pre>'.print_r($gridTileArray, 1).'</pre>';
					// echo '<pre>'.print_r('-------------------------------------------', 1).'</pre>';

					$this->gridTiles[] = $gridTileArray;
					if(++$twentyCount > 20) { $twentyCount = 1; $this->pageCount++; }

				}
				
				// let's get the feature details
				$bca = $wpdb->get_row(" SELECT * FROM `{$wpdb->prefix}codeqr_features` WHERE id = {$feature} ", ARRAY_A);

				$short_name 	= stripslashes($bca['feature_name']);
				$image_file 	= $bca['feature_image'];
				
				$gridTileArray = array(
					'type' => 'feature',
					'title' => $short_name,
					'image' => $image_file,
					'page' => $this->pageCount,
					'tile' => $this->letter[$twentyCount],
				);


				//echo '<pre>'.print_r('twentyCount2 : ' .$twentyCount, 1).'</pre>';
				//echo '<pre>'.print_r($gridTileArray, 1).'</pre>';
				//echo '<pre>'.print_r('-------------------------------------------', 1).'</pre>';


				$this->gridTiles[] = $gridTileArray;
				if(++$twentyCount > 20) { $twentyCount = 1; $this->pageCount++; }

			}

			$this->fillBlankTiles($twentyCount);

			$cat_count++;
		}
	}

	private function fillBlankTiles(&$twentyCount){

		//echo '<pre>'.print_r('<strong>Need to Fill blank Tiles</strong>', 1).'</pre>';
		//echo '<pre>'.print_r('-------------------------------------------', 1).'</pre>';

		while(in_array($twentyCount, [3, 4, 7, 8, 11, 12, 15, 16, 19, 20])){

			$gridTileArray = array(
				'type' => 'blank',
			);

			//echo '<pre>'.print_r('twentyCount : ' .$twentyCount, 1).'</pre>';
			//echo '<pre>'.print_r($gridTileArray, 1).'</pre>';
			//echo '<pre>'.print_r('-------------------------------------------', 1).'</pre>';

			$this->gridTiles[] = $gridTileArray;

			if(++$twentyCount > 20) { $twentyCount = 1; $this->pageCount++; }

		}
	}

	private function fillBlankRows(&$twentyCount){
	}

	private function renderTOCPage(){

		$x = 0;
		$fourCount = 0;
		$twentyCount = 0;
		$last_title = '';

		$num_toc_pages = $this->calcNumTOCPages();

		foreach($this->gridTiles as $n => $tile){

			if( $tile['type'] == 'blank' ) continue;

			// Add a new Template page if
			// TOC exceeds page length
			// ------------------------------------------------------------
			if($this->yVar > MAX_PAGE_LEN_Y) {						
				$this->pdf->addPage();
				$this->pdf->useTemplate($this->tplIdx2, 0, 0, 210, 300);
				

				//set footer texts
				$this->pdf->SetXY(17, -7.35);
				$this->pdf->SetFont($this->font.$this->font2, 'I');
				$this->pdf->SetXY(80, -6);
				$this->pdf->SetFont($this->font);
				$this->pdf->SetFontSize(8);
				$this->pdf->SetXY(-17, -6);
				$this->pdf->SetTextColor(128,130,133);
				$this->pdf->Write(0, $this->pdf->PageNo());
				$this->pdf->SetTextColor(0,0,0);
				$this->yVar = 33;			

				$this->pdf->SetFont($this->font.$this->font2);
				$this->pdf->SetFontSize(8);
			}

			$page_num = strval(intval($tile['page']) + $num_toc_pages);

			if( $tile['type'] == 'category' ){

				// if you only want the category titles to pring once each in the TOC
				if(strcmp($last_title, $tile['title']) === 0){
					continue;
				}else{
					$last_title = $tile['title'];
				}

				$this->yVar = $this->yVar + 4;

				$this->pdf->SetXY(20, $this->yVar);
				$this->pdf->SetFont($this->font, 'b');
				$this->pdf->SetFontSize(9);
				$this->pdf->Write(0, $tile['title']);
				
				// The dotted line in the table of contents.
				do {$this->pdf->Write(0, '.'); $xVar = $this->pdf->GetX(); } while ($xVar < 150);
				$this->pdf->SetXY(149.5, $this->pdf->GetY());
				
				// This is the TOC line.
				$this->pdf->Cell(15, 2, $page_num, 0, 0, 'L');
				$this->yVar = $this->yVar + 4;

				$this->pdf->SetFont($this->font.$this->font2, '');
				$this->pdf->SetFontSize(8);


			}
			elseif( $tile['type'] == 'feature'){

				$this->pdf->SetFont($this->font.$this->font2);
				$this->pdf->SetFontSize(8);
				$this->pdf->SetXY(20, $this->yVar);
				$this->pdf->Write(0,$tile['title']);

				// This draws the line in the table of contents.
				do {$this->pdf->Write(0, '.'); $xVar = $this->pdf->GetX(); } while ($xVar < 150);

				//writing subsection page counts
				$this->pdf->SetXY(149.9, $this->pdf->GetY());
				
				// This is the TOC line. MAH - inserts page location, which is wrong currently 10/23/2019
				$this->pdf->Cell(15, 2,  $page_num . ' ('.$tile['tile'].')', 0, 0, 'L');

				$this->yVar = $this->yVar + 4;

			}

		}

		/*foreach ( $_POST['cat'] as $cat => $features ) {

			$this->yVar = $this->yVar + 4;

			# THIS IS THE LINE FOR CATEGORY
			# --------------------------------------------------------------------
			
			
			# COUNTERS
			# --------------------------------------------------------------------
			
			$x++;
			$fourCount++;
			$twentyCount++;

			global $wpdb;

			foreach( $features as $key => $feat ){

				if($fourCount == 4) { $fourCount = 1; }
				if($this->yVar != 33) { $this->yVar = $this->yVar + 4; }

				$bca = $wpdb->get_row("
					SELECT *
					FROM `{$wpdb->prefix}codeqr_features`
					WHERE id = {$feat}
				", ARRAY_A);


				if( $bca ){

					# ADD NEW TOC PAGE
					# --------------------------------------------------------
										
					

					# END TABLE OF CONTENTS HEADERS, FOOTER, AND PAGE NUMBER
					# ---------------------------------------------------------
					
					$product = $_POST['product'];
					$feature_name = stripslashes($bca['feature_name']);
					$short_name = stripslashes($bca['feature_name']);
					
					# THIS IS THE LINE OF FEATURES FOR THAT CATEGORY
					# ---------------------------------------------------------
						
					// This is for the bold line of the TOC
						
					$this->yVar = $this->yVar + 4;
					$x++;
					$fourCount++;
					$twentyCount++;

					if( $twentyCount > 20 ){
						$twentyCount =1;
					}
				
				}
			}

		}*/
	}

	private function calcNumTOCPages(){

		$y = 0;
		$last_title = '';
		$num_pages = 0;

		foreach($this->gridTiles as $n => $tile){
			if($tile['type'] == 'blank'){
				continue;
			}else if($tile['type'] == 'category'){
				if(strcmp($last_title, $tile['title']) === 0){
					continue;
				}
				$y += 8;
			}else if($tile['type'] == 'feature'){
				$y += 4;
			}

			if($y > MAX_PAGE_LEN_Y) {						
				$num_pages++;
				$y = 0;
			}
		}

		return $num_pages;
	}

	private function featureGridHeader(){
		//First barcodes page
		$this->pdf->addPage();
		$this->pdf->useTemplate($this->tplIdx3, 0, 0, 210, 300);
		//set header/footer texts
		$this->pdf->SetXY(16, 18.2-($this->font2/4));
		$this->pdf->SetFont('trade', 'B');
		$this->pdf->SetTextColor(255,255,255);
		$this->pdf->SetFontSize(15);
		$this->pdf->SetFont($this->font, 'B');
		$this->pdf->SetXY(33.5+$this->xhd_spacing, 16.7+($this->font2/2));

		$this->pdf->SetXY(-110, 18.6);
		$this->pdf->SetFont($this->font, 'I');
		$this->pdf->SetFontSize(7);

		$this->pdf->SetTextColor(0, 0, 0);
		$this->pdf->SetFont($this->font.$this->font2);
		$this->pdf->SetFontSize(6.7);
		$this->pdf->SetXY(17, -7.35);
		$this->pdf->SetFont($this->font.$this->font2, 'I');

		$this->pdf->SetXY(80, -6);

		$this->pdf->SetFont($this->font);
		$this->pdf->SetFontSize(8);
		$this->pdf->SetXY(-17, -6);
		$this->pdf->SetTextColor(128,130,133);
		$this->pdf->Write(0, $this->pdf->PageNo());
		$this->pdf->SetXY(12.3, 25.8);
		$this->pdf->SetFillColor(128, 130, 133);


		$this->pdf->SetFont($this->font, 'B');
		$this->pdf->SetTextColor(128,130,133);
		$this->pdf->SetFontSize(10);
	}

	private function addNewGridTemplate(){
		$this->pdf->addPage();
		$this->pdf->useTemplate($this->tplIdx3, 0, 0, 210, 300);
		//set header/footer texts
		$this->pdf->SetXY(16, 18.2-($this->font2/4));
		$this->pdf->SetFont('trade', 'B');
		$this->pdf->SetTextColor(255,255,255);
		$this->pdf->SetFontSize(12);
		// krs - product was undefined - product is in _POST 
		$product = isset($_POST['product']) ? sanitize_text_field($_POST['product']) : '';
		$this->pdf->Cell(60, 5, $product, 0, 0);
		$this->pdf->SetFont($this->font, 'B');
		$this->pdf->SetXY(33.5+$this->xhd_spacing, 16.7+($this->font2/2));
		$this->pdf->Cell(60, 5, $this->GTconfigguide, 0, 0);
			
		$this->pdf->SetXY(-110, 18.6);
		$this->pdf->SetFont($this->font, 'I');
		$this->pdf->SetFontSize(7);
		$this->pdf->MultiCell(96, 5, $this->GTnotechanges, 0,'R');
		
		$this->pdf->SetTextColor(0, 0, 0);
		$this->pdf->SetFont($this->font.$this->font2);
		$this->pdf->SetFontSize(6.7);
		$this->pdf->SetXY(17, -7.35);
		$this->pdf->Cell(60, 5, $this->GTwebgenconfigguide, 0, 0);
		$this->pdf->SetFont($this->font.$this->font2, 'I');
		
		$this->pdf->SetXY(80, -6);
		
		$this->pdf->SetFont($this->font);
		$this->pdf->SetFontSize(8);
		$this->pdf->SetXY(-17, -6);
		$this->pdf->SetTextColor(128,130,133);
		$this->pdf->Write(0, $this->pdf->PageNo());
		$this->pdf->SetXY(12.3, 25.8);
		$this->pdf->SetFillColor(128, 130, 133);
				
		$this->pdf->SetFont($this->font, 'B');
		$this->pdf->SetFontSize(8);
		$this->pdf->SetTextColor(128,130,133);
		$this->pdf->SetXY(12.3, 25.8);
	}

	private function renderFeatureGrid(){
		
		$this->featureGridHeader();

		// Loop through all the QR codes
		// -----------------------------------------------------
		
		$category = null;

		$twentyCount = 0;

		//echo '<pre>'.print_r($this->gridTiles, 1).'</pre>';
		
		foreach( $this->gridTiles as $n => $tile ){

			//if( $n > 19 )continue;
			$twentyCount++;

			if( $twentyCount > 20 ){
				$this->addNewGridTemplate();
				$twentyCount = 1;
			}

			if     ( ($twentyCount) >= 0  && ($twentyCount) <= 4  ){ $y = 22.8;  }
			else if( ($twentyCount) >= 4  && ($twentyCount) <= 8  ){ $y = 70.8;  }
			else if( ($twentyCount) >= 8  && ($twentyCount) <= 12 ){ $y = 118.8; }
			else if( ($twentyCount) >= 12 && ($twentyCount) <= 16 ){ $y = 166.8; }
			else if( ($twentyCount) >= 16 && ($twentyCount) <= 20 ){ $y = 214.8; }

			//echo '<pre>'.print_r('twentyCount : ' . $twentyCount, 1).'</pre>';
			//echo '<pre>'.print_r($tile, 1).'</pre>';

			if( $tile['type'] == 'category' ){

				//echo '<pre>'.print_r($tile, 1).'</pre>';

				$x = 12.3;

				$this->pdf->SetXY($x, $y); 
				$this->pdf->Cell(46.2,48.2," ",0,0,'C', true); 
				$this->pdf->SetXY($x, $y + 15); 				
				$this->pdf->SetTextColor(255,255,255); 
				//left side category MAH
				$this->pdf->SetFontSize(12); 
				$this->pdf->MultiCell(46.2,15,$tile['title'],0,'C'); 
				
			} elseif( $title['type'] = 'feature' ){

				$x = $x + 46.2;
				$imgWidth2 = 12.1;
				$imgHeight2 = 8;
				
				
				// this is only if not blank tile
				$this->pdf->SetY($y);

				if( $tile['type'] != 'blank' ){

					$this->pdf->SetXY($x, $y + 3); 
					$this->pdf->Cell(
						46.2, // width
						60, // height
						$this->pdf->Image(rtrim(dirname(QRROOT_PATH), "/") . '/qr_codes/' .$tile['image'], $this->pdf->GetX()+$imgWidth2, $y+$imgHeight2, 22, 22),
						0, // border false
						0, // moves current position to the right
						'C' // align
					);

					$this->pdf->SetXY($this->pdf->GetX() - 46.2, $this->pdf->GetY() + 30); 
					$this->pdf->SetTextColor(128,130,133);
					//Function Name Size MAH
					$this->pdf->SetFontSize(8); 
					$this->pdf->MultiCell(46.2,15,$tile['title'],0,'C'); 

				} 
			}

		}		
	}

	private function getUniqueCategories(){

		return array_keys($_POST['cat']);
	}

	public function timestampGET(){

		$_REQUEST = array(
			'date'				=> 'X@'.$_GET['date'],
			'matrix_size' => 20,
			'matrix_type' => 'DATE',
			'fc' 					=> '000000',
			'bc' 					=> 'FFFFFF',
			'converted' 	=> 1,
			'filename'		=> date('Y-m-d_H-i-s'),
			'matrix_block_style' => 'square_blocks'
		);

		require_once dirname( QRROOT_PATH ).'/include/dm_code/index.php';
		die;
	}

	public function imageGET(){

		$_REQUEST = array(
			'data_text'   => $_REQUEST['code'],
			'matrix_size' => 10,
			'matrix_type' => 'TEXT',
			'fc' 					=> '000000',
			'bc' 					=> 'FFFFFF',
			'converted' 	=> 1,
			'matrix_block_style' => 'square_blocks'
		);

		require_once dirname( QRROOT_PATH ).'/include/dm_code/index.php';
		die;
	}

	public function configGET(){
		
		require_once dirname( QRROOT_PATH ).'/include/dm_code/index.php';
		die;
	}
}
