<?php
//** Get Input from _REQUEST **
$textGiftDynamic = '                                                          ';
$textGiverDynamic = '                              ';
$displayBlankError = false;
if (isset($_REQUEST['gift']) && $_REQUEST['gift'] != '')
{
  $textGiftDynamic = $_REQUEST['gift'];
}
else
{
  $displayBlankError = true;
}
if (isset($_REQUEST['giver']) && $_REQUEST['giver'] != '')
{
  $textGiverDynamic = $_REQUEST['giver'];
}
else
{
  $displayBlankError = true;
}

if (isset($_REQUEST['display']) && $_REQUEST['display'] != '')
{
  if ($_REQUEST['display'] == 'download')
    $action = 'download';
  else
    $action = 'preview';
}
else
{
  $action = "preview";
}


//*****************
//** Definitions **
//*****************

$textTitle = 'In honor of your wedding';
$textGiftStatic = 'a gift of  ';
$textGiverStatic = 'has been purchased for you by  ';
$textFooter = 'Enjoy the adventure!';
$textInstructions1 = 'This certificate may be printed out to be included within a card.';
$textInstructions2 = 'Please cut along the light gray box.';
$textError1 = 'Error: Gift Description or Gift Giver has been left blank.';
$textError2 = 'For a non-blank PDF certificate, please return to the Honeymoon';
$textError3 = 'Registry checkout page to auto-magically generate your custom certificate.';

//** Page x and y boundries **
$wFullPage = 215.9; //8.5 inches in mm, the entire x-deminsion
$hFullPage = 125;

//** Page Margins **
//x and y points for each margin
$xMarginLeft = 12.7; //12.7 mm = 1/2 inch
$yMarginTop = 12.7;
$xMarginRight = $wFullPage - 12.7;
$yMarginBottom = $hFullPage - 12.7;
$wMargins = $xMarginRight - $xMarginLeft;

//** Text Margins **
$wBorderPicture = 20;
$xLeftTextMargin = $xMarginLeft + $wBorderPicture;
$wTextArea = $wMargins - 2 * $wBorderPicture;
$wprintable_BAD_REPLACE = $xMarginRight - $wBorderPicture - $xLeftTextMargin;
$hUnderlineOffset = 7.15;
$wSpace = 5;
$hTwoLinesOffset = 5.5;

//***************
//** PDF Build **
//***************

require_once("resources/includePath.inc");
require('fpdf17/fpdf.php');
$pdf = new FPDF(); //default to mm units
$pdf->AddFont('Porcelain','','Porcelain.php');
$pdf->AddFont('DemiTasse','','DemiTasse.php');
$pdf->AddPage('P', 'Letter');

//** Draw Page Outline Box **
$pdf->SetDrawColor(230, 230, 230);
$pdf->Rect($xMarginLeft, $yMarginTop, $xMarginRight - $xMarginLeft, $yMarginBottom - $yMarginTop);

//*************************
//** Write Page Elements **
//*************************


//** Images **
$imgScaleBorder = 97;
$imgScaleWatermark = 75;
$imgSpacingBorder = 1.5;
$pdf->Image('resources/certificates/border_L.png', $xMarginLeft + $imgSpacingBorder, $yMarginTop + $imgSpacingBorder, $imgScaleBorder);
$pdf->Image('resources/certificates/watermark_light.png', ($wTextArea - $imgScaleWatermark) / 2 + $xLeftTextMargin, $yMarginTop + 7, $imgScaleWatermark);
$pdf->Image('resources/certificates/border_R.png', $xMarginRight - $imgSpacingBorder - $imgScaleBorder, $yMarginBottom - $imgSpacingBorder - $imgScaleBorder, $imgScaleBorder);

//** Title **
$yLine = $yMarginTop + 16;
$pdf->SetFont('Porcelain','', 42);
$pdf->SetY($yLine);
$pdf->Cell(0, 10, $textTitle, 0, 1, 'C');


//** Body Lines **
$pdf->SetFont('DemiTasse');
$pdf->SetFontSize(14);
$pdf->SetDrawColor(0,0,0);



//** Line 1: Gifts **
$yLine = $yLine + 22;

$wStatic = $pdf->GetStringWidth($textGiftStatic);
// $wDynamic = $pdf->GetStringWidth($textGiftDynamic);
$wAvailForDynaimc = $wTextArea - $wStatic - 2 * $wSpace;

//Case 1: Fits on one line
$wDynamic = $pdf->GetStringWidth($textGiftDynamic);
if ($wDynamic <= $wAvailForDynaimc)
{
  // $pdf->SetXY(10,10);
  // $pdf->Cell(20,0, "DEBUG: case 1");

  $wSideSpacing = ($wTextArea - $wStatic - $wDynamic - 2 * $wSpace) / 2;
  
  $xStatic = $xLeftTextMargin + $wSideSpacing;
  $pdf->SetXY($xStatic, $yLine);
  $pdf->Cell($wStatic, 10, $textGiftStatic, 0, 0, 'L');

  $xDynamic = $xStatic + $wStatic + $wSpace;
  $pdf->SetXY($xDynamic, $yLine);
  $pdf->Cell($wDynamic, 10, $textGiftDynamic, 0, 0, 'L');
  
  $x1 = $xStatic + $wStatic;
  $x2 = $x1 + $wDynamic + 2 * $wSpace;
  $pdf->Line($x1, $yLine + $hUnderlineOffset, $x2, $yLine + $hUnderlineOffset);  
}
else
{
  //Case 2: Size reduced, fits on one line
  $pdf->SetFontSize(12);
  $wDynamic = $pdf->GetStringWidth($textGiftDynamic);
  if ($wDynamic <= $wAvailForDynaimc)
  {
    // $pdf->SetXY(10,10);
    // $pdf->Cell(20,0, "DEBUG: case 2");
    
    $wSideSpacing = ($wTextArea - $wStatic - $wDynamic - 2 * $wSpace) / 2;
    $xStatic = $xLeftTextMargin + $wSideSpacing;
    
    $xDynamic = $xStatic + $wStatic + $wSpace;
    $pdf->SetXY($xDynamic, $yLine);
    $pdf->Cell($wDynamic, 10, $textGiftDynamic, 0, 0, 'L');
    
    $pdf->SetXY($xStatic, $yLine);
    $pdf->SetFontSize(14);
    $pdf->Cell($wStatic, 10, $textGiftStatic, 0, 0, 'L');
    
    $x1 = $xStatic + $wStatic;
    $x2 = $x1 + $wDynamic + 2 * $wSpace;
    $pdf->Line($x1, $yLine + $hUnderlineOffset, $x2, $yLine + $hUnderlineOffset);
  }
  else
  {
    // $pdf->SetXY(10,10);
    // $pdf->Cell(20,0, "DEBUG: case 3 (default)");
    
    //Case 3: Fits on two lines, size regular
    $splitAt = determineLineSplitEvenly($textGiftDynamic);
    $line1 = substr($textGiftDynamic, 0, $splitAt);
    $line2 = substr($textGiftDynamic, $splitAt);

    $fontSizeDynaimc = 14; //starts at two above first desired size
    $wLarger = 0;
    do {
      $fontSizeDynaimc -= 2;
      $pdf->SetFontSize($fontSizeDynaimc);
      $wLine1 = $pdf->GetStringWidth($line1);
      $wLine2 = $pdf->GetStringWidth($line2);
      $wLarger = (($wLine1 >= $wLine2) ? $wLine1 : $wLine2);
    } while ($wLarger >= $wAvailForDynaimc);
    
    // print 2 lines
    $yLine = $yLine + $hTwoLinesOffset; //y offset for 2 lines
    $wSideSpacing = ($wTextArea - $wStatic - $wLarger - 2 * $wSpace) / 2;
    $xStatic = $xLeftTextMargin + $wSideSpacing;
    
    $xDynamic = $xStatic + $wStatic + $wSpace;
    $wLine1Margin = ($wLarger - $wLine1) / 2;
    $pdf->SetXY($xDynamic + $wLine1Margin, $yLine - $hTwoLinesOffset);
    $pdf->Cell($wLarger, 10, $line1, 0, 0, 'L');
    $wLine2Margin = ($wLarger - $wLine2) / 2;
    $pdf->SetXY($xDynamic + $wLine2Margin, $yLine);
    $pdf->Cell($wLarger, 10, $line2, 0, 0, 'L');
    
    $pdf->SetXY($xStatic, $yLine);
    $pdf->SetFontSize(14);
    $pdf->Cell($wStatic, 10, $textGiftStatic, 0, 0, 'L');
    
    $x1 = $xStatic + $wStatic;
    $x2 = $x1 + $wLarger + 2 * $wSpace;
    $pdf->Line($x1, $yLine + $hUnderlineOffset, $x2, $yLine + $hUnderlineOffset);
    
    $yLine = $yLine - $hTwoLinesOffset; //undo the offset for 2 lines
  }
}



//** Line 2: Gift Giver **
$yLine = $yLine + 18;
$pdf->SetFontSize(14);
$wStatic = $pdf->GetStringWidth($textGiverStatic);
$wDynamic = $pdf->GetStringWidth($textGiverDynamic);

$wSideSpacing = ($wTextArea - $wStatic - $wDynamic - 2 * $wSpace) / 2;

$xStatic = $xLeftTextMargin + $wSideSpacing;
$pdf->SetXY($xStatic, $yLine);
$pdf->Cell($wStatic, 10, $textGiverStatic, 0, 0, 'L');

$xDynamic = $xStatic + $wStatic + $wSpace;
$pdf->SetXY($xDynamic, $yLine);
$pdf->Cell($wDynamic, 10, $textGiverDynamic, 0, 0, 'L');

$x1 = $xStatic + $wStatic;
$x2 = $x1 + $wDynamic + 2 * $wSpace;
$pdf->Line($x1, $yLine + $hUnderlineOffset, $x2, $yLine + $hUnderlineOffset);  


//** Footer **
$yLine = $yLine + 18;
$pdf->SetFont('Porcelain','', 24);
$pdf->SetY($yLine);
$pdf->Cell(0, 10, $textFooter, 0, 1, 'C');


//** Instructions outside of Printable Range **
$yLine = $yMarginBottom + 30;
$pdf->SetFont('DemiTasse','', 14);
$pdf->SetTextColor(150, 150, 150);
$pdf->SetY($yLine);
$pdf->Cell(0, 10, $textInstructions1, 0, 1, 'C');
$pdf->Cell(0, 10, $textInstructions2, 0, 1, 'C');

//** Error if no Info Provided **
if ($displayBlankError)
{
  $yLine = $yLine + 50;
  $pdf->SetTextColor(128,0,0);
  $pdf->SetY($yLine);
  $pdf->Cell(0, 10, $textError1, 0, 1, 'C');
  $pdf->SetFont('DemiTasse','', 11);
  $pdf->Cell(0, 10, $textError2, 0, 1, 'C');
  $pdf->Cell(0, 10, $textError3, 0, 1, 'C');
}

//** Output to Broswer **
if ($action == "download")
  $pdf->Output('WeddingCertificate.pdf', 'D');
else
  $pdf->Output('WeddingCertificate.pdf', 'I'); //display in browser



// *************************************************************************
// *************************  Functions  ***********************************
// *************************************************************************

// $splitAt = determineLineSplitEvenly($str);
// echo substr($str, 0, $splitAt) . "<br />";
// echo substr($str, $splitAt) . "<br />";
function determineLineSplitEvenly($str)
{
  $split = floor(strlen($str) / 2);
  while (!ctype_space(substr($str, $split, 1)))
    ++$split;
  return $split;
}

?>