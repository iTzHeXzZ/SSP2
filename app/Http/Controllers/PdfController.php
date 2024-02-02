<?php

    namespace App\Http\Controllers;
    
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\File;
    use setasign\Fpdi\Fpdi;
    
    
    class PdfController extends Controller
    {
        public function fillPdf(Request $request)
    {
        function rotateImage($filePath, $rotationAngle)
        {
            $source = imagecreatefrompng($filePath) or die('Error opening file ' . $filePath);
            imagealphablending($source, false);
            imagesavealpha($source, true);
        
            $rotation = imagerotate($source, $rotationAngle, imageColorAllocateAlpha($source, 0, 0, 0, 127));
            imagealphablending($rotation, false);
            imagesavealpha($rotation, true);
        
            imagepng($rotation, $filePath);
        
            imagedestroy($source);
            imagedestroy($rotation);
        }

        $username           = Auth::user()->name;
        $anredeFrau         = $request->input('fields_Anrede_Frau') === 'X' ? 'X' : '';
        $anredeHerr         = $request->input('fields_Anrede_Herr') === 'X' ? 'X' : '';
        $anredeDivers       = $request->input('fields_Anrede_Divers') === 'X' ? 'X' : '';                  
        $eheleute           = $request->input('fields_Eheleute');
        $titel              = $request->input('fields_Titel');
        $firmaGemeinschaft  = $request->input('fields_Firma_Gemeinschaft');
        $vorname            = $request->input('fields_Vorname');
        $nachname           = $request->input('fields_Nachname');
        $strasse            = $request->input('fields_Strasse');
        $hausnummer         = $request->input('fields_Hausnummer');
        $plz                = $request->input('fields_PLZ');
        $ort                = $request->input('fields_Ort');
        $telefonFestnetz    = $request->input('fields_Telefon_Festnetz');
        $telefonMobil       = $request->input('fields_Telefon_mobil');
        $emailAdresse       = $request->input('fields_EMailAdresse');
        $kundennummer       = $request->input('fields_kundennummer');
        $ortDatum           = 'Langenfeld ,' . date('d.m.Y');
        $waipustick         = $request->input('waipustick');
        $cabletv            = $request->input('cabletv');
        $waipucomfort       = $request->input('waipucomfort');
        $waipuplus          = $request->input('waipuplus');
        $firstflat          = $request->input('firstflat');
        $secondflat         = $request->input('secondflat');
        $staticip           = $request->input('staticip');
        $postde             = $request->input('postde');
        $additionalFields = $request->input('fields');
        $checkboxStatus = $request->input('fields.StandardCheckboxStatus');
        if ($checkboxStatus == '1') {
            $anredeFrau1         =  $anredeFrau; 
            $anredeHerr1         =  $anredeHerr;
            $anredeDivers1       =  $anredeDivers;
            $eheleute1           =  $eheleute; 
            $titel1              =  $titel;
            $firmaGemeinschaft1  =  $firmaGemeinschaft;
            $vorname1            =  $vorname;
            $nachname1           =  $nachname;
            $strasse1            =  $strasse;
            $hausnummer1         =  $hausnummer;
            $plz1                =  $plz;
            $ort1                =  $ort;
            $telefonFestnetz1    =  $telefonFestnetz;
            $telefonMobil1       =  $telefonMobil;
            $emailAdresse1       =  $emailAdresse;
        } else {
            $anredeFrau1         =  $request->input('fields.Anrede_Frau1');
            $anredeHerr1         =  $request->input('fields.Anrede_Herr1');
            $anredeDivers1       =  $request->input('fields.Anrede_Divers1');
            $eheleute1           =  $request->input('fields.Eheleute1');
            $titel1              =  $request->input('fields.Titel1');
            $firmaGemeinschaft1  =  $request->input('fields.Firma_Gemeinschaft1');
            $vorname1            =  $request->input('fields.Vorname1');
            $nachname1           =  $request->input('fields.Nachname1');
            $strasse1            =  $request->input('fields.Strasse1');
            $hausnummer1         =  $request->input('fields.Hausnummer1');
            $plz1                =  $request->input('fields.PLZ1');
            $ort1                =  $request->input('fields.Ort1');
            $telefonFestnetz1    =  $request->input('fields.Telefon_Festnetz1');
            $telefonMobil1       =  $request->input('fields.Telefon_mobil1');
            $emailAdresse1       =  $request->input('fields.EMailAdresse1');
        }
        $selectedOption = $request->input('gfpaket'); 
        $checkboxValue = '';
        $checkboxDevice= '';
            
            if ($selectedOption === 'gf15024m') {
                $checkboxValue = 'cbbasic';
            } elseif ($selectedOption === 'gf15012m') {
                $checkboxValue = 'cbbasi12';
            } elseif ($selectedOption === 'gf300') {
                $checkboxValue = 'cbclassic';
            } elseif ($selectedOption === 'gf600') {
                $checkboxValue = 'cbperformance';
            } elseif ($selectedOption === 'gf1000') {
                $checkboxValue = 'cbexpert';
            }
 
        $selecteddevice = $request->input('fritzBox');
        if ($selecteddevice === 'gfboxonetime') {
            $checkboxDevice = 'cbgfboxonetime';
        } elseif ($selecteddevice === 'gfboxrent') {
            $checkboxDevice = 'cbgfboxrent';
        }
        $ownerSignatureBase64 = $request->input('owner_signature');
        $orderSignatureBase64 = $request->input('order_signature');
        $advisorSignatureBase64 = $request->input('advisor_signature');

        function base64ToImage($base64_string, $output_file) {
            $file = fopen($output_file, "wb");
        
            $data = explode(',', $base64_string);
        
            if (count($data) > 1) {
                fwrite($file, base64_decode($data[1]));
                fclose($file);
        
                return $output_file;
            } else {
                fclose($file);
                return null;
            }
        }
    
        $signatureDir = storage_path('app/public/signatures');
        if (!File::exists($signatureDir)) {
            File::makeDirectory($signatureDir, 0755, true, true);
        }
        
        $ownerSignaturePath = $signatureDir . '/' . uniqid() . '_owner_signature.png';
        $orderSignaturePath = $signatureDir . '/' . uniqid() . '_order_signature.png';
        $advisorSignaturePath = $signatureDir . '/' . uniqid() . '_advisor_signature.png';
    
        base64ToImage($ownerSignatureBase64, $ownerSignaturePath);
        base64ToImage($orderSignatureBase64, $orderSignaturePath);
        base64ToImage($advisorSignatureBase64, $advisorSignaturePath);

        


        $fdf_header = <<<FDF
        %FDF-1.2
        1 0 obj
        <<
        /FDF << /Fields [
        FDF;
        
        // FDF footer section
        $fdf_footer = <<<FDF
] >> >>
2 0 obj
<<
/Length 4744
/Filter [/ASCII85Decode /FlateDecode]
>>
stream
... (hier kommt der FDF-Content hin)
FDF;
        // FDF content section
        $fdf_content  = "<</T($checkboxValue)/V/Ja/AS/Ja>>";
        $fdf_content .= "<</T($checkboxDevice)/V/Ja/AS/Ja>>";
        $fdf_content .= "<</T(Frau)/V(" . mb_convert_encoding($anredeFrau1, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(Mann)/V(" . mb_convert_encoding($anredeHerr1, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(Divers)/V(" . mb_convert_encoding($anredeDivers1, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(Eheleute)/V(" . mb_convert_encoding($eheleute1, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(Titel)/V(" . mb_convert_encoding($titel1, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(Firma Gemeinschaft)/V(" . mb_convert_encoding($firmaGemeinschaft1, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(Vorname)/V(" . mb_convert_encoding($vorname, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(Nachname)/V(" . mb_convert_encoding($nachname, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(Strasse)/V(" . mb_convert_encoding($strasse, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(Hausnummer)/V(" . mb_convert_encoding($hausnummer, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(PLZ)/V(" . mb_convert_encoding($plz, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(Ort)/V(" . mb_convert_encoding($ort, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(Telefon Festnetz)/V(" . mb_convert_encoding($telefonFestnetz, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(Telefon mobil)/V(" . mb_convert_encoding($telefonMobil, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(EMailAdresse)/V(" . mb_convert_encoding($emailAdresse, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(vorName)/V(" . mb_convert_encoding($vorname1, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(nachName)/V(" . mb_convert_encoding($nachname1, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(Strassen)/V(" . mb_convert_encoding($strasse1, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(hausNummer)/V(" . mb_convert_encoding($hausnummer1, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(plz)/V(" . mb_convert_encoding($plz1, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(ort)/V(" . mb_convert_encoding($ort1, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(Telefon Festnetz1)/V(" . mb_convert_encoding($telefonFestnetz1, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(Telefon mobil1)/V(" . mb_convert_encoding($telefonMobil1, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(email)/V(" . mb_convert_encoding($emailAdresse1, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(Kundennummer)/V(" . mb_convert_encoding($kundennummer, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(Ort  Datum)/V(" . mb_convert_encoding($ortDatum, 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(Adresse)/V(" . mb_convert_encoding("$strasse $hausnummer, $plz $ort", 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(customer)/V(" . mb_convert_encoding("$vorname,$nachname ", 'ISO-8859-1', 'UTF-8') . ")>>";
        $fdf_content .= "<</T(cbwaipustick)/V" . (($waipustick === 'on') ? '/Ja' : '/Off') . ">>";
        $fdf_content .= "<</T(cbcabletv)/V" . (($cabletv === 'on') ? '/Ja' : '/Off') . ">>";
        $fdf_content .= "<</T(cbwaipucomfort)/V" . (($waipucomfort === 'on') ? '/Ja' : '/Off') . ">>";
        $fdf_content .= "<</T(cbwaipuplus)/V" . (($waipuplus === 'on') ? '/Ja' : '/Off') . ">>";
        $fdf_content .= "<</T(cb1flat)/V" . (($firstflat === 'on') ? '/Ja' : '/Off') . ">>";
        $fdf_content .= "<</T(cb2flat)/V" . (($secondflat=== 'on') ? '/Ja' : '/Off') . ">>";
        $fdf_content .= "<</T(cbstaticip)/V" . (($staticip === 'on') ? '/Ja' : '/Off') . ">>";
        $fdf_content .= "<</T(cbpostde)/V" . (($postde === 'on') ? '/Ja' : '/Off') . ">>";
        $fdf_content .= "<</T(user)/V(" . utf8_decode($username) . ")>>";
        $additionalFieldsContent = '';
        if ($additionalFields && is_array($additionalFields)) {
            foreach ($additionalFields as $fieldName => $fieldValue) {
                $escapedFieldName = mb_convert_encoding($fieldName, 'ISO-8859-1', 'UTF-8');
                $escapedFieldValue = mb_convert_encoding($fieldValue, 'ISO-8859-1', 'UTF-8');
                $additionalFieldsContent .= "<</T($escapedFieldName)/V($escapedFieldValue)>>";
            }
        }
        $fdf_content .= $additionalFieldsContent;
        
                

        
        $content = $fdf_header . $fdf_content . $fdf_footer;


        $pdfPath = public_path('gnvlangenfeld.pdf');
        $outputTextPath = public_path(uniqid() . '_text_content.txt');
        // Temporäre FDF-Datei im public-Ordner erstellen
        $FDFfile = storage_path(uniqid() . '.fdf');
        file_put_contents($FDFfile, "%FDF-1.2\n1 0 obj\n<<\n/FDF << /Fields [$fdf_content] >> >>\nendobj\ntrailer\n<</Root 1 0 R>>\n%%EOF");

        // Temporäre PDF-Datei im public-Ordner erstellen
        $tempPdfPath = storage_path(uniqid() . '.pdf');
        // PDF-Formular ausfüllen
        $command = "pdftk \"$pdfPath\" fill_form \"$FDFfile\" output \"$tempPdfPath\" flatten ";
        exec($command);
                
        unlink($FDFfile);

        $pdf = new Fpdi();
        $pdf->setSourceFile($tempPdfPath);
        rotateImage($orderSignaturePath, 0);
        rotateImage($advisorSignaturePath, 0);
        rotateImage($ownerSignaturePath, 0);
        
        dd($pdfPath,$tempPdfPath);

                

        // Durchlaufe alle Seiten der vorhandenen PDF und füge sie zur neuen PDF hinzu
        for ($pageNumber = 1; $pageNumber <= $pdf->setSourceFile($tempPdfPath); $pageNumber++) {
            $template = $pdf->importPage($pageNumber);
            $pdf->AddPage();
            $pdf->useTemplate($template);

            if ($pageNumber == 1) {
                $pdf->Image($orderSignaturePath, 10, 260, 60, 20);
                $pdf->Image($advisorSignaturePath, 135, 260, 60, 20);                

            } elseif ($pageNumber == 3) {
                $pdf->Image($ownerSignaturePath, 30, 240, 60, 20);
            }
        }
        $outputPdfPath = storage_path(uniqid() . '.pdf');
        $pdf->Output($outputPdfPath, 'F');
        $outputname = "$vorname $nachname " . date('Y-m-d');
        unlink($tempPdfPath);
        unlink($orderSignaturePath);
        unlink($ownerSignaturePath);
        unlink($advisorSignaturePath);

        return response()->download($outputPdfPath, $outputname)->deleteFileAfterSend(true);
    }
        

        public function showForm()
        {
            return view('pdf.form');
        }

        public function showProduct()
        {
            return view('pdf.productswl');
        }

    }
   
