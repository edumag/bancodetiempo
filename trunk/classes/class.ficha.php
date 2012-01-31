<?php 

if (!isset($global))
{
    die(__FILE__." was included without inc.global.php being included first.  Include() that file first, then you can include ".__FILE__);
}

require_once ("class.member.php");
require_once ("File/PDF.php");
//require_once ("File_PDF/PDF.php");

class cFicha {
    public $member;
    public $pdf;
    public $font;
    public $font_size;
    public $font_spacing;
    public $margin;
    public $column;
       


    function __construct ($member=null) {
        if($member) {
        $this->member = $member;
        $this->column = 1;
        $this->margin = 15;
        $this->font = "Helvetica";
        $this->font_size = 8;
        $this->font_spacing = 5;
        $this->pdf = &File_PDF::factory("P", "mm", "A4");
        $this->pdf->open();
        $this->pdf->addPage("P");
        $this->pdf->setFont($this->font,"",$this->font_size);
        $this->pdf->setMargins($this->margin, $this->margin, "105");
        $this->pdf->setAutoPageBreak(true,"2");
        $this->pdf->setXY($this->margin,$this->margin);
        $this->pdf->SetDisplayMode("real","single");
        }
    }
 
    function GenerarFicha () {
        $this->ImagenTop();
        $this->LineasCorte(); 
        $this->Foto();
        $this->InfoContacto();
        $this->InfoComun();
        $this->pdf->Output("ficha.pdf",false);
    }

    function LineasCorte () {
    $this->pdf->setLineWidth(0.2);
    $this->pdf->Line(25.7,23.7,25.7,103.8);
    $this->pdf->Line(105.7,23.7,105.7,103.8);
    $this->pdf->Line(13.2,36.3,128.2,36.3);
    $this->pdf->Line(13.2,84.3,128.2,84.3);  
    }

     function ImagenTop () {
     $this->pdf->image('images/top.png', 23.6, 33.0, 94.2, 18.0); 
    }   
    
     function Foto () {
     $imagen = 'media/fotos/'.$this->member->person[0]->imagen.'.jpg';
     $this->pdf->image($imagen, 30.6, 54.2, 21.4, 17.2); 
    }  
    
    function InfoContacto() {
     $this->pdf->setXY( 48.0, 59.0); 
     $this->pdf->setFont($this->font,"",$this->font_size); 
     $fecha = new cDateTime($this->member->join_date);  
     $inscripcion='Data d\'inscripció    '.$fecha->ShortDate();
     $inscripcion=iconv('utf-8', 'windows-1252', $inscripcion);
     $this->pdf->Cell( 53.8,"", $inscripcion,"","", $align = 'R');
     
  /*   $this->pdf->setXY( 30.6, 77.0); 
     $this->pdf->setFont($this->font,"",$this->font_size);   
     $socio='Socio';
     $socio=iconv('utf-8', 'windows-1252', $socio);
     $this->pdf->Cell( 30.6,"", $socio,"","", $align = 'L');
   */  
     $this->pdf->setXY( 29.6, 75.0); 
     $this->pdf->setFont($this->font,"B",10);   
     $socio=$this->member->member_id;  
     $socio=iconv('utf-8', 'windows-1252', $socio);
     $this->pdf->Cell( 29.6,"", $socio,"","", $align = 'L');  
       
     if($this->member->person[0]->phone1_number !="")
        {   
        $this->pdf->setXY( 48.0, 64.0);
        $telefono='Telèfon 1 - ';
        $this->pdf->setFont($this->font,"I",$this->font_size);
        $telefono.=$this->member->person[0]->DisplayPhone("1");
        $telefono=iconv('utf-8', 'windows-1252', $telefono);
        $this->pdf->Cell( 53.8,"", $telefono,"","", $align = 'R');
        }  
     if($this->member->person[0]->phone2_number !="")
        {   
        $this->pdf->setXY( 48.0, 69.0);
        $telefono='Telèfon 2 - ';
        $this->pdf->setFont($this->font,"I",$this->font_size);
        $telefono.=$this->member->person[0]->DisplayPhone("2");
        $telefono=iconv('utf-8', 'windows-1252', $telefono);
        $this->pdf->Cell( 53.8,"", $telefono,"","", $align = 'R');  
        } 
     if($this->member->person[0]->email !="")
        {   
        $this->pdf->setXY( 48.0, 74.0);
        $this->pdf->setFont($this->font,"B",$this->font_size);
        $this->pdf->setFillColor('rgb',0.55, 0, 0);    
        $email=$this->member->person[0]->email;
        $telefono=iconv('utf-8', 'windows-1252', $email);
        $this->pdf->Cell( 53.8,"", $email,"","", $align = 'R');  
        }   
    }
    
    function InfoComun()
    {
     $this->pdf->setXY( 48.0, 53.0); 
     $this->pdf->setFont($this->font,"",8);
     $this->pdf->setFillColor('rgb',0.13, 0.19, 0.42);   
     $pie=SERVER_DOMAIN;
     $pie=iconv('utf-8', 'windows-1252', $pie);
     $this->pdf->Cell( 53.8,"", $pie,"","", $align = 'R');
     
     $this->pdf->setXY( 30.6, 80.5); 
     $this->pdf->setFont($this->font,"",5);   
     $pie=l('Si troba aquesta tarje, si us plau, avísi\'ns a ').EMAIL_ADMIN;
     $pie=iconv('utf-8', 'windows-1252', $pie);
     $this->pdf->Cell( 30.6,"", $pie,"","", $align = 'L');
    }
   
}
 ?>
