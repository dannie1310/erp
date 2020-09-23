<?php
namespace App\Utils\PDF;

class PDF_MC_Table extends FPDF
{
    var $widths;
    var $aligns;
    var $fills;
    var $fonts;
    var $heights;
    var $rounds;
    var $radius;
    var $textcolors;
    var $styles;

    var $encrypted;          //whether document is protected
    var $Uvalue;             //U entry in pdf document
    var $Ovalue;             //O entry in pdf document
    var $Pvalue;             //P entry in pdf document
    var $enc_obj_id;         //encryption object id
    var $last_rc4_key;       //last RC4 key encrypted (cached for optimisation)
    var $last_rc4_key_c;     //last RC4 computed key



    var $angle=0;


    function __construct($orientation='P', $unit='cm', $size='A4')
    {
        parent::__construct($orientation, $unit, $size);

        $this->encrypted=false;
        $this->last_rc4_key='';
        $this->padding="\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08".
            "\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";
    }
    function SetFonts($fo)
    {
        $this->fonts=$fo;
    }
    function SetStyles($st)
    {
        $this->styles=$st;
    }
    function SetTextColors($tc)
    {
        $this->textcolors=$tc;
    }
    function SetRadius($rad)
    {
        $this->radius=$rad;

    }
    function SetFills($f)
    {
        $this->fills=$f;

    }
    function SetRounds($rou)
    {
        $this->rounds=$rou;

    }
    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths=$w;
    }

    function SetHeights($h)
    {
        //Set the array of column widths
        $this->heights=$h;
    }

    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns=$a;
    }

    function Row($data)
    {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h1=isset($this->heights[0]) ? $this->heights[0] : '5';
        $h=$h1*$nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];

            $f=$this->fills[$i];
            $round=isset($this->rounds[$i]) ? $this->rounds[$i] : '0';
            $tc=isset($this->textcolors[$i]) ? $this->textcolors[$i] : '0,0,0';
            $st=isset($this->styles[$i]) ? $this->styles[$i] : 'DF';
            $rad=isset($this->radius[$i]) ? $this->radius[$i] : 0;
            //$fo=$this->fonts[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
            $this->SetTextColor($tc);
            $this->SetFillColor($f);
            //$this->SetFont($fo); $pdf->RoundedRect(60, 30, 68, 46, 5, '13', 'DF');

            $this->RoundedRect($x,$y,$w,$h,$rad,$round,$st);
            //Print the text

            $this->MultiCell($w,$h1,$data[$i],0,$a);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }


    function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' || $style=='DF')
            $op='B';
        else
            $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));

        $xc = $x+$w-$r;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
        if (strpos($corners, '2')===false)
            $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k,($hp-$y)*$k ));
        else
            $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);

        $xc = $x+$w-$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
        if (strpos($corners, '3')===false)
            $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-($y+$h))*$k));
        else
            $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);

        $xc = $x+$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
        if (strpos($corners, '4')===false)
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-($y+$h))*$k));
        else
            $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);

        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
        if (strpos($corners, '1')===false)
        {
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$y)*$k ));
            $this->_out(sprintf('%.2F %.2F l',($x+$r)*$k,($hp-$y)*$k ));
        }
        else
            $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
            $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }

    function Rotate($angle,$x=-1,$y=-1)
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
            $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
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

    function SetProtection($permissions=array(), $user_pass='', $owner_pass=null)
    {
        $options = array('print' => 4, 'modify' => 8, 'copy' => 16, 'annot-forms' => 32 );
        $protection = 192;
        foreach($permissions as $permission){
            if (!isset($options[$permission]))
                $this->Error('Incorrect permission: '.$permission);
            $protection += $options[$permission];
        }
        if ($owner_pass === null)
            $owner_pass = uniqid(rand());
        $this->encrypted = true;
        $this->_generateencryptionkey($user_pass, $owner_pass, $protection);
    }

    /****************************************************************************
     *                                                                           *
     *                              Private methods                              *
     *                                                                           *
     ****************************************************************************/

    function _putstream($s)
    {
        if ($this->encrypted) {
            $s = $this->_RC4($this->_objectkey($this->n), $s);
        }
        parent::_putstream($s);
    }

    function _textstring($s)
    {
        if ($this->encrypted) {
            $s = $this->_RC4($this->_objectkey($this->n), $s);
        }
        return parent::_textstring($s);
    }

    /**
     * Compute key depending on object number where the encrypted data is stored
     */
    function _objectkey($n)
    {
        return substr($this->_md5_16($this->encryption_key.pack('VXxx',$n)),0,10);
    }

    /**
     * Escape special characters
     */
    function _escape($s)
    {
        $s=str_replace('\\','\\\\',$s);
        $s=str_replace(')','\\)',$s);
        $s=str_replace('(','\\(',$s);
        $s=str_replace("\r",'\\r',$s);
        return $s;
    }

    function _putresources()
    {
        parent::_putresources();
        if ($this->encrypted) {
            $this->_newobj();
            $this->enc_obj_id = $this->n;
            $this->_out('<<');
            $this->_putencryption();
            $this->_out('>>');
            $this->_out('endobj');
        }
    }

    function _putencryption()
    {
        $this->_out('/Filter /Standard');
        $this->_out('/V 1');
        $this->_out('/R 2');
        $this->_out('/O ('.$this->_escape($this->Ovalue).')');
        $this->_out('/U ('.$this->_escape($this->Uvalue).')');
        $this->_out('/P '.$this->Pvalue);
    }

    function _puttrailer()
    {
        parent::_puttrailer();
        if ($this->encrypted) {
            $this->_out('/Encrypt '.$this->enc_obj_id.' 0 R');
            $this->_out('/ID [()()]');
        }
    }

    /**
     * RC4 is the standard encryption algorithm used in PDF format
     */
    function _RC4($key, $text)
    {
        if ($this->last_rc4_key != $key) {
            $k = str_repeat($key, 256/strlen($key)+1);
            $rc4 = range(0,255);
            $j = 0;
            for ($i=0; $i<256; $i++){
                $t = $rc4[$i];
                $j = ($j + $t + ord($k{$i})) % 256;
                $rc4[$i] = $rc4[$j];
                $rc4[$j] = $t;
            }
            $this->last_rc4_key = $key;
            $this->last_rc4_key_c = $rc4;
        } else {
            $rc4 = $this->last_rc4_key_c;
        }

        $len = strlen($text);
        $a = 0;
        $b = 0;
        $out = '';
        for ($i=0; $i<$len; $i++){
            $a = ($a+1)%256;
            $t= $rc4[$a];
            $b = ($b+$t)%256;
            $rc4[$a] = $rc4[$b];
            $rc4[$b] = $t;
            $k = $rc4[($rc4[$a]+$rc4[$b])%256];
            $out.=chr(ord($text{$i}) ^ $k);
        }

        return $out;
    }

    /**
     * Get MD5 as binary string
     */
    function _md5_16($string)
    {
        return pack('H*',md5($string));
    }

    /**
     * Compute O value
     */
    function _Ovalue($user_pass, $owner_pass)
    {
        $tmp = $this->_md5_16($owner_pass);
        $owner_RC4_key = substr($tmp,0,5);
        return $this->_RC4($owner_RC4_key, $user_pass);
    }

    /**
     * Compute U value
     */
    function _Uvalue()
    {
        return $this->_RC4($this->encryption_key, $this->padding);
    }

    /**
     * Compute encryption key
     */
    function _generateencryptionkey($user_pass, $owner_pass, $protection)
    {
        // Pad passwords
        $user_pass = substr($user_pass.$this->padding,0,32);
        $owner_pass = substr($owner_pass.$this->padding,0,32);
        // Compute O value
        $this->Ovalue = $this->_Ovalue($user_pass,$owner_pass);
        // Compute encyption key
        $tmp = $this->_md5_16($user_pass.$this->Ovalue.chr($protection)."\xFF\xFF\xFF");
        $this->encryption_key = substr($tmp,0,5);
        // Compute U value
        $this->Uvalue = $this->_Uvalue();
        // Compute P value
        $this->Pvalue = -(($protection^255)+1);
    }
}
?>
