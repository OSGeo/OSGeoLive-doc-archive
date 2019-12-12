<?php

require_once(dirname(__FILE__)."/fpdi.php");

class mb_fpdi extends FPDI {

    function mb_fpdi($orientation='L',$unit='mm',$format='A4') {
        parent::__construct($orientation,$unit,$format);
        $this->SetAutoPageBreak(false,0);
    }	

    // ====================================
	// Image with png alpha extension
	// ====================================
    
	//Private properties
	var $tmpFiles = array();
	
	/*******************************************************************************
	*                                                                              *
	*                               Public methods                                 *
	*                                                                              *
	*******************************************************************************/
	function Image($file,$x,$y,$w=0,$h=0,$type='',$link='', $isMask=false, $maskImg=0, $align='', $angle=0)
	{
	    //Put an image on the page
	    if(!isset($this->images[$file]))
	    {
	        //First use of image, get info
	        if($type=='')
	        {
	            $pos=strrpos($file,'.');
	            if(!$pos)
	                $this->Error('Image file has no extension and no type was specified: '.$file);
	            $type=substr($file,$pos+1);
	        }
	        $type=strtolower($type);
	        $mqr=get_magic_quotes_runtime();
	        set_magic_quotes_runtime(0);
	        if($type=='jpg' || $type=='jpeg')
	            $info=$this->_parsejpg($file);
	        elseif($type=='png'){
	            $info=$this->_parsepng($file);
	            if ($info=='alpha') return $this->ImagePngWithAlpha($file,$x,$y,$w,$h,$link);
	        }
	        else
	        {
	            //Allow for additional formats
	            $mtd='_parse'.$type;
	            if(!method_exists($this,$mtd))
	                $this->Error('Unsupported image type: '.$type);
	            $info=$this->$mtd($file);
	        }
	        set_magic_quotes_runtime($mqr);
	        
	        if ($isMask){
	      $info['cs']="DeviceGray"; // try to force grayscale (instead of indexed)
	    }
	        $info['i']=count($this->images)+1;
	        if ($maskImg>0) $info['masked'] = $maskImg;###
	        $this->images[$file]=$info;
	    }
	    else
	        $info=$this->images[$file];
	    //Automatic width and height calculation if needed
	    if($w===0 && $h===0)
	    {
	        //Put image at 72 dpi
	        $w=$info['w']/$this->k;
	        $h=$info['h']/$this->k;
	    }
	    if($w===0)
	        $w=$h*$info['w']/$info['h'];
	    if($h===0)
	        $h=$w*$info['h']/$info['w'];
	        
	    $x_rot = $x;
    	$y_rot = $y;    

	    switch ($align) {
			case 1:
		        break;
		    case 2:
		        $x=$x-$w/2;
		        break;
		    case 3:
		        $x=$x-$w;
		        break;
		    case 4:
		        $y=$y-$h/2;
		        break;
		    case 5:
		        $x=$x-$w/2;
		        $y=$y-$h/2;
		        break;
		    case 6:
		        $x=$x-$w;
		        $y=$y-$h/2;
		        break;
		    case 7:
		        $y=$y-$h;
		        break;
		    case 8:
		        $x=$x-$w/2;
		        $y=$y-$h;
		        break;
		    case 9:
		        $x=$x-$w;
		        $y=$y-$h;
		        break;
		    default:
		        break;
		}
		    	
	    if ($isMask) $x = $this->fwPt + 10; // embed hidden, ouside the canvas  

    	if($angle!=0){ 
      		$this->_out('q'); 
      		$this->Rotation($angle, $x_rot, $y_rot);
      		$this->_out(sprintf('q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q',$w*$this->k,$h*$this->k,$x*$this->k,($this->h-($y+$h))*$this->k,$info['i']));
      		$this->_out('Q'); 
    	}
    	else {  $this->_out(sprintf('q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q',$w*$this->k,$h*$this->k,$x*$this->k,($this->h-($y+$h))*$this->k,$info['i']));}
	    
	    #$this->_out(sprintf('q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q',$w*$this->k,$h*$this->k,$x*$this->k,($this->h-($y+$h))*$this->k,$info['i']));
	    if($link)
	        $this->Link($x,$y,$w,$h,$link);
	        
	    return $info['i'];
	}
	
	// needs GD 2.x extension
	// pixel-wise operation, not very fast
	function ImagePngWithAlpha($file,$x,$y,$w=0,$h=0,$link='')
	{
	    $tmp_alpha = tempnam('.', 'mska');
	    $this->tmpFiles[] = $tmp_alpha;
	    $tmp_plain = tempnam('.', 'mskp');
	    $this->tmpFiles[] = $tmp_plain;
	    
	    list($wpx, $hpx) = getimagesize($file);
	    $img = imagecreatefrompng($file);
	    $alpha_img = imagecreate( $wpx, $hpx );
	    
	    // generate gray scale pallete
	    for($c=0;$c<256;$c++) ImageColorAllocate($alpha_img, $c, $c, $c);
	    
	    // extract alpha channel
	    $xpx=0;
	    while ($xpx<$wpx){
	        $ypx = 0;
	        while ($ypx<$hpx){
	            $color_index = imagecolorat($img, $xpx, $ypx);
	            $col = imagecolorsforindex($img, $color_index);
	            imagesetpixel($alpha_img, $xpx, $ypx, $this->_gamma( (127-$col['alpha'])*255/127)  );
	        ++$ypx;
	        }
	        ++$xpx;
	    }
	
	    imagepng($alpha_img, $tmp_alpha);
	    imagedestroy($alpha_img);
	    
	    // extract image without alpha channel
	    $plain_img = imagecreatetruecolor ( $wpx, $hpx );
	    imagecopy ($plain_img, $img, 0, 0, 0, 0, $wpx, $hpx );
	    imagepng($plain_img, $tmp_plain);
	    imagedestroy($plain_img);
	    
	    //first embed mask image (w, h, x, will be ignored)
	    $maskImg = $this->Image($tmp_alpha, 0,0,0,-$h*10, 'PNG', '', true);
	    
	    //embed image, masked with previously embedded mask
	    $this->Image($tmp_plain,$x,$y,$w,$h,'PNG',$link, false, $maskImg);
	}
	
	function Close()
	{
	    parent::Close();
	    // clean up tmp files
	    foreach($this->tmpFiles as $tmp) @unlink($tmp);
	}
	
	/*******************************************************************************
	*                                                                              *
	*                               Private methods                                *
	*                                                                              *
	*******************************************************************************/
	function _putimages()
	{
	    $filter=($this->compress) ? '/Filter /FlateDecode ' : '';
	    reset($this->images);
	    while(list($file,$info)=each($this->images))
	    {
	        $this->_newobj();
	        $this->images[$file]['n']=$this->n;
	        $this->_out('<</Type /XObject');
	        $this->_out('/Subtype /Image');
	        $this->_out('/Width '.$info['w']);
	        $this->_out('/Height '.$info['h']);
	        
	        if (isset($info["masked"])) $this->_out('/SMask '.($this->n-1).' 0 R'); ###
	        
	        if($info['cs']=='Indexed')
	            $this->_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal'])/3-1).' '.($this->n+1).' 0 R]');
	        else
	        {
	            $this->_out('/ColorSpace /'.$info['cs']);
	            if($info['cs']=='DeviceCMYK')
	                $this->_out('/Decode [1 0 1 0 1 0 1 0]');
	        }
	        $this->_out('/BitsPerComponent '.$info['bpc']);
	        if(isset($info['f']))
	            $this->_out('/Filter /'.$info['f']);
	        if(isset($info['parms']))
	            $this->_out($info['parms']);
	        if(isset($info['trns']) && is_array($info['trns']))
	        {
	            $trns='';
	            for($i=0;$i<count($info['trns']);$i++)
	                $trns.=$info['trns'][$i].' '.$info['trns'][$i].' ';
	            $this->_out('/Mask ['.$trns.']');
	        }
	        $this->_out('/Length '.strlen($info['data']).'>>');
	        $this->_putstream($info['data']);
	        unset($this->images[$file]['data']);
	        $this->_out('endobj');
	        //Palette
	        if($info['cs']=='Indexed')
	        {
	            $this->_newobj();
	            $pal=($this->compress) ? gzcompress($info['pal']) : $info['pal'];
	            $this->_out('<<'.$filter.'/Length '.strlen($pal).'>>');
	            $this->_putstream($pal);
	            $this->_out('endobj');
	        }
	    }
	}
	
	// GD seems to use a different gamma, this method is used to correct it again
	function _gamma($v){
	    return pow ($v/255, 2.2) * 255;
	}
	
     function _parsegif($file) 
     { 
	//Function by Jerome Fenal
	require_once(BASECLASSES.'/fpdi/gif.php'); //GIF class in pure PHP from Yamasoft (formerly at http://www.yamasoft.com)

	$h=0;
	$w=0;
	$gif=new CGIF();

	if (!$gif->loadFile($file, 0))
		$this->Error("GIF parser: unable to open file $file");

	if($gif->m_img->m_gih->m_bLocalClr) {
		$nColors = $gif->m_img->m_gih->m_nTableSize;
		$pal = $gif->m_img->m_gih->m_colorTable->toString();
		if($bgColor != -1) {
			$bgColor = $gif->m_img->m_gih->m_colorTable->colorIndex($bgColor);
		}
		$colspace='Indexed';
	} elseif($gif->m_gfh->m_bGlobalClr) {
		$nColors = $gif->m_gfh->m_nTableSize;
		$pal = $gif->m_gfh->m_colorTable->toString();
		if($bgColor != -1) {
			$bgColor = $gif->m_gfh->m_colorTable->colorIndex($bgColor);
		}
		$colspace='Indexed';
	} else {
		$nColors = 0;
		$bgColor = -1;
		$colspace='DeviceGray';
		$pal='';
	}

	$trns='';
	if($gif->m_img->m_bTrans && ($nColors > 0)) {
		$trns=array($gif->m_img->m_nTrans);
	}

	$data=$gif->m_img->m_data;
	$w=$gif->m_gfh->m_nWidth;
	$h=$gif->m_gfh->m_nHeight;

	if($colspace=='Indexed' and empty($pal))
		$this->Error('Missing palette in '.$file);

	if ($this->compress) {
		$data=gzcompress($data);
		return array( 'w'=>$w, 'h'=>$h, 'cs'=>$colspace, 'bpc'=>8, 'f'=>'FlateDecode', 'pal'=>$pal, 'trns'=>$trns, 'data'=>$data);
	} else {
		return array( 'w'=>$w, 'h'=>$h, 'cs'=>$colspace, 'bpc'=>8, 'pal'=>$pal, 'trns'=>$trns, 'data'=>$data);
	} 
}

	// this method overwriing the original version is only needed to make the Image method support PNGs with alpha channels.
	// if you only use the ImagePngWithAlpha method for such PNGs, you can remove it from this script.
	function _parsepng($file)
	{
	    //Extract info from a PNG file
	    $f=fopen($file,'rb');
	    if(!$f)
	        $this->Error('Can\'t open image file: '.$file);
	    //Check signature
	    if(fread($f,8)!=chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10))
	        $this->Error('Not a PNG file: '.$file);
	    //Read header chunk
	    fread($f,4);
	    if(fread($f,4)!='IHDR')
	        $this->Error('Incorrect PNG file: '.$file);
	    $w=$this->_readint($f);
	    $h=$this->_readint($f);
	    $bpc=ord(fread($f,1));
	    if($bpc>8)
	        $this->Error('16-bit depth not supported: '.$file);
	    $ct=ord(fread($f,1));
	    if($ct==0)
	        $colspace='DeviceGray';
	    elseif($ct==2)
	        $colspace='DeviceRGB';
	    elseif($ct==3)
	        $colspace='Indexed';
	    else {
	        fclose($f);      // the only changes are
	        return 'alpha';  // made in those 2 lines
	    }
	    if(ord(fread($f,1))!=0)
	        $this->Error('Unknown compression method: '.$file);
	    if(ord(fread($f,1))!=0)
	        $this->Error('Unknown filter method: '.$file);
	    if(ord(fread($f,1))!=0)
	        $this->Error('Interlacing not supported: '.$file);
	    fread($f,4);
	    $parms='/DecodeParms <</Predictor 15 /Colors '.($ct==2 ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w.'>>';
	    //Scan chunks looking for palette, transparency and image data
	    $pal='';
	    $trns='';
	    $data='';
	    do
	    {
	        $n=$this->_readint($f);
	        $type=fread($f,4);
	        if($type=='PLTE')
	        {
	            //Read palette
	            $pal=fread($f,$n);
	            fread($f,4);
	        }
	        elseif($type=='tRNS')
	        {
	            //Read transparency info
	            $t=fread($f,$n);
	            if($ct==0)
	                $trns=array(ord(substr($t,1,1)));
	            elseif($ct==2)
	                $trns=array(ord(substr($t,1,1)),ord(substr($t,3,1)),ord(substr($t,5,1)));
	            else
	            {
	                $pos=strpos($t,chr(0));
	                if($pos!==false)
	                    $trns=array($pos);
	            }
	            fread($f,4);
	        }
	        elseif($type=='IDAT')
	        {
	            //Read image data block
	            $data.=fread($f,$n);
	            fread($f,4);
	        }
	        elseif($type=='IEND')
	            break;
	        else
	            fread($f,$n+4);
	    }
	    while($n);
	    if($colspace=='Indexed' && empty($pal))
	        $this->Error('Missing palette in '.$file);
	    fclose($f);
	    return array('w'=>$w,'h'=>$h,'cs'=>$colspace,'bpc'=>$bpc,'f'=>'FlateDecode','parms'=>$parms,'pal'=>$pal,'trns'=>$trns,'data'=>$data);
	}

	var $angle=0;
	
	function Rotate($angle, $x=-1, $y=-1)
	{
	    if($x==-1)
	        $x=$this->x;
	    if($y==-1)
	        $y=$this->y;
	    if($this->angle!=0)
	        $this->_out('Q');
	    $this->angle=$angle;
	    if($angle!=0)
	    {
	        $angle*=M_PI/180;
	        $c=cos($angle);
	        $s=sin($angle);
	        $cx=$x*$this->k;
	        $cy=($this->h-$y)*$this->k;
	        $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
	    }
	}
	
	function _endpage()
	{
	    if($this->angle!=0)
	    {
	        $this->angle=0;
	        $this->_out('Q');
	    }
	    parent::_endpage();
	}
    
	function RotatedText($x, $y, $txt, $angle)
	{
	    //Text rotated around its origin
	    $this->Rotate($angle, $x, $y);
	    $this->Text($x, $y, $txt);
	    $this->Rotate(0);
	}

// Draws a polygon
    // Parameters:
    // - p: Points. Array with values x0, y0, x1, y1, ..., x(np-1), y(np - 1)
    // - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
    // - line_style: Line style. Array with one of this index
    //   . all: Line style of all lines. Array like for SetLineStyle
    //   . 0..np-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    function Polygon($p, $style = '', $line_style = null, $fill_color = null) {
        $np = count($p) / 2;
        if (!(false === strpos($style, 'F')) && $fill_color) {
            #list($r, $g, $b) = $fill_color;
            
            $this->SetFillColor($fill_color->r, $fill_color->g, $fill_color->b);
        }
        switch ($style) {
            case 'F':
                $line_style = null;
                $op = 'f';
                break;
            case 'FD': case 'DF':
                $op = 'B';
                break;
            default:
                $op = 'S';
                break;
        }
        $draw = true;
        if ($line_style)
            if (isset($line_style['all']))
                $this->SetLineStyle($line_style['all']);
            else { // 0 .. (np - 1), op = {B, S}
                $draw = false;
                if ('B' == $op) {
                    $op = 'f';
                    $this->_Point($p[0], $p[1]);
                    for ($i = 2; $i < ($np * 2); $i = $i + 2)
                        $this->_Line($p[$i], $p[$i + 1]);
                    $this->_Line($p[0], $p[1]);
                    $this->_out($op);
                }
                $p[$np * 2] = $p[0];
                $p[($np * 2) + 1] = $p[1];
                for ($i = 0; $i < $np; $i++)
                    if (!empty($line_style[$i]))
                        $this->Line($p[$i * 2], $p[($i * 2) + 1], $p[($i * 2) + 2], $p[($i * 2) + 3], $line_style[$i]);
            }

        if ($draw) {
            $this->_Point($p[0], $p[1]);
            for ($i = 2; $i < ($np * 2); $i = $i + 2)
                $this->_Line($p[$i], $p[$i + 1]);
            $this->_Line($p[0], $p[1]);
            $this->_out($op);
        }
    }
    
	/* PRIVATE METHODS */

    // Sets a draw point
    // Parameters:
    // - x, y: Point
    function _Point($x, $y) {
        $this->_out(sprintf('%.2f %.2f m', $x * $this->k, ($this->h - $y) * $this->k));
    }
    
// Draws a line from last draw point
    // Parameters:
    // - x, y: End point
    function _Line($x, $y) {
        $this->_out(sprintf('%.2f %.2f l', $x * $this->k, ($this->h - $y) * $this->k));
    }

    // Draws a Bézier curve from last draw point
    // Parameters:
    // - x1, y1: Control point 1
    // - x2, y2: Control point 2
    // - x3, y3: End point
    function _Curve($x1, $y1, $x2, $y2, $x3, $y3) {
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c', $x1 * $this->k, ($this->h - $y1) * $this->k, $x2 * $this->k, ($this->h - $y2) * $this->k, $x3 * $this->k, ($this->h - $y3) * $this->k));
    }
    
    // Draws a line
    // Parameters:
    // - x1, y1: Start point
    // - x2, y2: End point
    // - style: Line style. Array like for SetLineStyle
    function Line($x1, $y1, $x2, $y2, $style = null) {
        if ($style)
            $this->SetLineStyle($style);
        parent::Line($x1, $y1, $x2, $y2);
    }

    function ClippingText($x, $y, $txt, $outline=false)
    {
        $op=$outline ? 5 : 7;
        $this->_out(sprintf('q BT %.2f %.2f Td %d Tr (%s) Tj 0 Tr ET',
            $x*$this->k,
            ($this->h-$y)*$this->k,
            $op,
            $this->_escape($txt)));
    }

    function ClippingRect($x, $y, $w, $h, $outline=false)
    {
        $op=$outline ? 'S' : 'n';
        $this->_out(sprintf('q %.2f %.2f %.2f %.2f re W %s',
            $x*$this->k,
            ($this->h-$y)*$this->k,
            $w*$this->k, -$h*$this->k,
            $op));
    }

    function ClippingEllipse($x, $y, $rx, $ry, $outline=false)
    {
        $op=$outline ? 'S' : 'n';
        $lx=4/3*(M_SQRT2-1)*$rx;
        $ly=4/3*(M_SQRT2-1)*$ry;
        $k=$this->k;
        $h=$this->h;
        $this->_out(sprintf('q %.2f %.2f m %.2f %.2f %.2f %.2f %.2f %.2f c',
            ($x+$rx)*$k, ($h-$y)*$k,
            ($x+$rx)*$k, ($h-($y-$ly))*$k,
            ($x+$lx)*$k, ($h-($y-$ry))*$k,
            $x*$k, ($h-($y-$ry))*$k));
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c',
            ($x-$lx)*$k, ($h-($y-$ry))*$k,
            ($x-$rx)*$k, ($h-($y-$ly))*$k,
            ($x-$rx)*$k, ($h-$y)*$k));
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c',
            ($x-$rx)*$k, ($h-($y+$ly))*$k,
            ($x-$lx)*$k, ($h-($y+$ry))*$k,
            $x*$k, ($h-($y+$ry))*$k));
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c W %s',
            ($x+$lx)*$k, ($h-($y+$ry))*$k,
            ($x+$rx)*$k, ($h-($y+$ly))*$k,
            ($x+$rx)*$k, ($h-$y)*$k,
            $op));
    }

    function UnsetClipping()
    {
        $this->_out('Q');
    }

    function ClippedCell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='')
    {
        if($border || $fill || $this->y+$h>$this->PageBreakTrigger)
        {
            $this->Cell($w, $h, '', $border, 0, '', $fill);
            $this->x-=$w;
        }
        $this->ClippingRect($this->x, $this->y, $w, $h);
        $this->Cell($w, $h, $txt, '', $ln, $align, 0, $link);
        $this->UnsetClipping();
    }	

    function Rotation($grad, $x = '', $y = ''){
      if ($x === '') $x = $this->x;
      if ($y === '') $y = $this->y;
      $y = ($this->h-$y) * $this->k;
      $x *= $this->k;
      $tm[0] = cos(deg2rad($grad));
      $tm[1] = sin(deg2rad($grad));
      $tm[2] = -$tm[1];
      $tm[3] = $tm[0];
      $tm[4] = $x+$tm[1]*$y-$tm[0]*$x;
      $tm[5] = $y-$tm[0]*$y-$tm[1]*$x;
      $this->_out(sprintf('%.3f %.3f %.3f %.3f %.3f %.3f cm', $tm[0],$tm[1],$tm[2],$tm[3],$tm[4],$tm[5]));
      }
    
    // Sets line style
    // Parameters:
    // - style: Line style. Array with keys among the following:
    //   . width: Width of the line in user units
    //   . cap: Type of cap to put on the line (butt, round, square). The difference between 'square' and 'butt' is that 'square' projects a flat end past the end of the line.
    //   . join: miter, round or bevel
    //   . dash: Dash pattern. Is 0 (without dash) or array with series of length values, which are the lengths of the on and off dashes.
    //           For example: (2) represents 2 on, 2 off, 2 on , 2 off ...
    //                        (2, 1) is 2 on, 1 off, 2 on, 1 off.. etc
    //   . phase: Modifier of the dash pattern which is used to shift the point at which the pattern starts
    //   . color: Draw color. Array with components (red, green, blue)
    function SetLineStyle($style) {
        extract($style);
        if (isset($width)) {
            $width_prev = $this->LineWidth;
            $this->SetLineWidth($width);
            $this->LineWidth = $width_prev;
        }
        if (isset($cap)) {
            $ca = array('butt' => 0, 'round'=> 1, 'square' => 2);
            if (isset($ca[$cap]))
                $this->_out($ca[$cap] . ' J');
        }
        if (isset($join)) {
            $ja = array('miter' => 0, 'round' => 1, 'bevel' => 2);
            if (isset($ja[$join]))
                $this->_out($ja[$join] . ' j');
        }
        if (isset($dash)) {
            $dash_string = '';
            if ($dash) {
                if(ereg('^.+, ', $dash))
                    $tab = explode(', ', $dash);
                else
                    $tab = array($dash);
                $dash_string = '';
                foreach ($tab as $i => $v) {
                    if ($i > 0)
                        $dash_string .= ' ';
                    $dash_string .= sprintf('%.2f', $v);
                }
            }
            if (!isset($phase) || !$dash)
                $phase = 0;
            $this->_out(sprintf('[%s] %.2f d', $dash_string, $phase));
        }
        if (isset($color)) {
            list($r, $g, $b) = $color;
            $this->SetDrawColor($r, $g, $b);
        }
    }
}

?>
