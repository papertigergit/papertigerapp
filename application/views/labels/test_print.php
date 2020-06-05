<?php
// Print a table
/**
		 * Prints a cell (rectangular area) with optional borders, background color and html text string.
		 * The upper-left corner of the cell corresponds to the current position. After the call, the current position moves to the right or to the next line.<br />
		 * If automatic page breaking is enabled and the cell goes beyond the limit, a page break is done before outputting.
		 * @param float $w Cell width. If 0, the cell extends up to the right margin.
		 * @param float $h Cell minimum height. The cell extends automatically if needed.
		 * @param float $x upper-left corner X coordinate
		 * @param float $y upper-left corner Y coordinate
		 * @param string $html html text to print. Default value: empty string.
		 * @param mixed $border Indicates if borders must be drawn around the cell. The value can be either a number:<ul><li>0: no border (default)</li><li>1: frame</li></ul>or a string containing some or all of the following characters (in any order):<ul><li>L: left</li><li>T: top</li><li>R: right</li><li>B: bottom</li></ul>
		 * @param int $ln Indicates where the current position should go after the call. Possible values are:<ul><li>0: to the right (or left for RTL language)</li><li>1: to the beginning of the next line</li><li>2: below</li></ul>
	Putting 1 is equivalent to putting 0 and calling Ln() just after. Default value: 0.
		 * @param int $fill Indicates if the cell background must be painted (1) or transparent (0). Default value: 0.
		 * @param boolean $reseth if true reset the last cell height (default true).
		 * @param string $align Allows to center or align the text. Possible values are:<ul><li>L : left align</li><li>C : center</li><li>R : right align</li><li>'' : empty string : left for LTR or right for RTL</li></ul>
		 * @param boolean $autopadding if true, uses internal padding and automatically adjust it to account for line width.
		 * @access public
		 * @uses MultiCell()
		 * @see Multicell(), writeHTML()
		 */


$this->load->library('pdf');
$this->pdf->SetMargins('6', '15', '6');
// add a page
$this->pdf->AddPage();
$count=0;
	for($i=$range_start; $i<=$range_end; $i++)
$html = '<h2>HTML TABLE:</h2>
<table border="1" cellspacing="2">
    <tr>
        <th width="2in">'.<?php echo $location_name; echo $i?>.' </th>
    </tr>
   
</table>';

// output the HTML content
$html111='Ankit Parekh';
$this->pdf->writeHTML($html, true, false, true, false, '');
//$pdf->writeHTMLCell(5,20,100,100,'Ankit Parekh',1,1,0,'C');
//$this->pdf->writeHTMLCell(100, 10, '', '', $html111,'L', 1, 0, true, 'L', true);
//$this->pdf->writeHTMLCell(50, 10, '200', '200', $html111,1,1,0,true,'L', true);
?>