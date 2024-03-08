<?php

    namespace App\Http\Controllers;
    
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\File;
    use setasign\Fpdi\Fpdi;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Mail\Message;

    
    
    class PdfController extends Controller
    {
        public function fillPdf(Request $request)
    {

        $username           = Auth::user()->name;
        $anredeFrau         = $request->input('fields_Anrede_Frau') === 'X' ? 'X' : '';
        $anredeHerr         = $request->input('fields_Anrede_Herr') === 'X' ? 'X' : '';
        $anredeDivers       = $request->input('fields_Anrede_Divers') === 'X' ? 'X' : '';                  
        $eheleute           = $request->input('fields_Eheleute');
        $titel              = $request->input('fields_Titel');
        $firmaGemeinschaft  = $request->input('fields_Firma_Gemeinschaft');
        $vorname            = $request->input('fields_Vorname');
        $nachname           = $request->input('fields_Nachname');
        $gebu               = $request->input('gb');
        $strasse            = $request->input('fields_Strasse');
        $hausnummer         = $request->input('fields_Hausnummer');
        $plz                = $request->input('fields_PLZ');
        $ort                = $request->input('fields_Ort');
        $telefonFestnetz    = $request->input('fields_Telefon_Festnetz');
        $telefonMobil       = $request->input('fields_Telefon_mobil');
        $emailAdresse       = $request->input('fields_EMailAdresse');
        $kundennummer       = $request->input('fields_kundennummer');
        $abwcustom          = $request->input('kontoinhaber');
        $iban               = $request->input('iban');
        $bank               = $request->input('bank');
        $we                 = $request->input('anzahlwe');
        $gk                 = $request->input('anzahlgk');
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
        $adresse = $strasse . ' ' . $hausnummer . ', ' . $plz . ' ' . $ort;
        $customer = $vorname . ' ,' . $nachname;
        $combplzort = $plz . ' ' . $ort;
        $combstrha = $strasse . ' ' . $hausnummer;
        $combtel = $telefonMobil . '  ' . $telefonFestnetz;


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
    
        $signatureDir = storage_path('app/signatures');
        if (!File::exists($signatureDir)) {
            File::makeDirectory($signatureDir, 0755, true, true);
        }
        $directory = storage_path('app/pdf');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $ownerSignaturePath = $signatureDir . '/' . uniqid() . '_owner_signature.png';
        $orderSignaturePath = $signatureDir . '/' . uniqid() . '_order_signature.png';
        $advisorSignaturePath = $signatureDir . '/' . uniqid() . '_advisor_signature.png';
    
        base64ToImage($ownerSignatureBase64, $ownerSignaturePath);
        base64ToImage($orderSignatureBase64, $orderSignaturePath);
        base64ToImage($advisorSignatureBase64, $advisorSignaturePath);


        $pdfPath = storage_path('gnvlangenfeld.pdf');
        $pdf = new Fpdi();
        $pdf->setSourceFile($pdfPath);
        
        
        for ($pageNumber = 1; $pageNumber <= $pdf->setSourceFile($pdfPath); $pageNumber++) {
            $template = $pdf->importPage($pageNumber);
            $pdf->AddPage();
            $pdf->useTemplate($template);
            $maxWidth = 30;
            $fontSize = 12; 
        
            $pdf->SetFont('Arial', '', 12);
            $pdf->SetTextColor(0, 0, 0);
        
            if ($pageNumber == 1) {
                $pdf->Image($orderSignaturePath, 10, 260, 60, 20);
                $pdf->Image($advisorSignaturePath, 135, 260, 60, 20);
                
                $pdf->SetXY(89.5 , 50.5);
                $pdf->Write(0, utf8_decode($vorname));
                $pdf->SetXY(27, 50.5);
                $pdf->Write(0, utf8_decode($nachname));
                $pdf->SetXY(45, 61.8);
                $pdf->Write(0, utf8_decode($adresse));
                $textWidth = $pdf->GetStringWidth($kundennummer);
                while ($textWidth > $maxWidth && $fontSize > 2) {
                    $fontSize--;
                    $pdf->SetFont('Arial', '', $fontSize);
                    $textWidth = $pdf->GetStringWidth($kundennummer);
                }
                $pdf->SetXY(168 , 50.5);
                $pdf->Write(0, utf8_decode($kundennummer));
                $pdf->SetFont('Arial', '', 12);
                $pdf->SetXY(135 , 73);
                $pdf->Write(0, utf8_decode($emailAdresse));
                $pdf->SetXY(50, 70);
                $pdf->Write(0,  $telefonFestnetz);
                $pdf->SetXY(50, 75);
                $pdf->Write(0, $telefonMobil);
                $pdf->SetXY(32, 263);
                $pdf->Write(0, utf8_decode($ortDatum));
                $pdf->SetXY(49, 219);
                $pdf->Write(0, utf8_decode($customer));
                $pdf->SetXY(95, 230);
                $pdf->Write(0, utf8_decode($username));
                $pdf->SetXY(120, 250.5);
                $pdf->Write(0, utf8_decode($customer));
                $pdf->SetXY(24, 200);
                $pdf->Write(0, 'X');

                $checkboxCoordinates = [
                    'waipustick' => ['x' => 24, 'y' => 188],
                    'cabletv' => ['x' => 24, 'y' => 128],
                    'waipucomfort' => ['x' => 24, 'y' => 134],
                    'waipuplus' => ['x' => 24, 'y' => 140],
                    'firstflat' => ['x' => 24, 'y' => 146],
                    'secondflat' => ['x' => 24, 'y' => 152],
                    'staticip' => ['x' => 24, 'y' => 158],
                    'postde' => ['x' => 24, 'y' => 164],
                    'cbbasic' => ['x' => 24, 'y' => 92],
                    'cbbasi12' => ['x' => 24, 'y' => 98],
                    'cbclassic' => ['x' => 24, 'y' => 104],
                    'cbperformance' => ['x' => 24, 'y' => 110],
                    'cbexpert' => ['x' => 24, 'y' => 116],
                    'cbgfboxonetime' => ['x' => 24, 'y' => 176],
                    'cbgfboxrent' => ['x' => 24, 'y' => 182],
                ];
                

                $selectedOption = $request->input('gfpaket'); 
                $selecteddevice = $request->input('fritzBox'); 
                
                $selectedOptionKey = ''; 
                $selectedDeviceKey = ''; 
                switch ($selectedOption) {
                    case 'gf15024m':
                        $selectedOptionKey = 'cbbasic';
                        break;
                    case 'gf15012m':
                        $selectedOptionKey = 'cbbasi12';
                        break;
                    case 'gf300':
                        $selectedOptionKey = 'cbclassic';
                        break;
                    case 'gf600':
                        $selectedOptionKey = 'cbperformance';
                        break;
                    case 'gf1000':
                        $selectedOptionKey = 'cbexpert';
                        break;         
                }
                switch ($selecteddevice) {
                    case 'gfboxonetime':
                        $selectedDeviceKey = 'cbgfboxonetime';
                        break;
                    case 'gfboxrent':
                        $selectedDeviceKey = 'cbgfboxrent';
                        break;
                }
                
                foreach ($checkboxCoordinates as $key => $coords) {
                    $isChecked = false;
                    if (isset($$key) && $$key === 'on') {
                        $isChecked = true;
                    }
                    if ($key === $selectedOptionKey || $key === $selectedDeviceKey) {
                        $isChecked = true;
                    }
                    if ($isChecked) {
                        $pdf->SetXY($coords['x'], $coords['y']);
                        $pdf->Write(0, "X");
                    }
                }



            } elseif ($pageNumber == 2) {
                $pdf->SetXY(21 , 55);
                $pdf->Write(0, utf8_decode($anredeFrau1));                
                $pdf->SetXY(34 , 55);
                $pdf->Write(0, utf8_decode($anredeHerr1));               
                $pdf->SetXY(45 , 55);
                $pdf->Write(0, utf8_decode($anredeDivers1));                
                $pdf->SetXY(57 , 55);
                $pdf->Write(0, utf8_decode($eheleute1));                
                $pdf->SetXY(80 , 55);
                $pdf->Write(0, utf8_decode($titel1));
                $pdf->SetXY(110 , 55);
                $pdf->Write(0, utf8_decode($firmaGemeinschaft1));
                $pdf->SetXY(22 , 63);
                $pdf->Write(0, utf8_decode($vorname1));
                $pdf->SetXY(22, 71.2);
                $pdf->Write(0, utf8_decode($nachname1));
                $pdf->SetXY(135, 71.2);
                $pdf->Write(0, utf8_decode($ort1));
                $pdf->SetXY(110, 71.2);
                $pdf->Write(0, utf8_decode($plz1));
                $pdf->SetXY(110, 63);
                $pdf->Write(0, utf8_decode($strasse1));
                $pdf->SetXY(170 , 63);
                $pdf->Write(0, utf8_decode($hausnummer1));
                $pdf->SetXY(22 , 87.6);
                $pdf->Write(0, utf8_decode($emailAdresse1));
                $pdf->SetXY(22, 79.4);
                $pdf->Write(0,  $telefonFestnetz1);
                $pdf->SetXY(110, 79.4);
                $pdf->Write(0, $telefonMobil1);
                $pdf->SetXY(135, 117);
                $pdf->Write(0, utf8_decode($ort1));
                $pdf->SetXY(110, 117);
                $pdf->Write(0, utf8_decode($plz1));
                $pdf->SetXY(22, 117);
                $pdf->Write(0, utf8_decode($strasse1));
                $pdf->SetXY(80 , 117);
                $pdf->Write(0, utf8_decode($hausnummer1));
                $pdf->SetXY(22, 125);
                $pdf->Write(0, utf8_decode($we));
                $pdf->SetXY(110 , 125);
                $pdf->Write(0, utf8_decode($gk));

                $baseCoordinates = [
                    'Strasse' => ['x' => 22, 'y' => 117],
                    'Hausnummer' => ['x' => 80, 'y' => 117],
                    'PLZ' => ['x' => 110, 'y' => 117],
                    'Ort' => ['x' => 135, 'y' => 117],
                    'anzahlwe' => ['x' => 22, 'y' => 125],
                    'anzahlgk' => ['x' => 110, 'y' => 125],
                ];
                                
                $xIncrement = 0;
                $yIncrement = 27.5;
                
                $maxUnits = 6;
                
                for ($i = 1; $i <= $maxUnits; $i++) {
                    $adjustment = ($i) * $yIncrement;
                
                    foreach ($baseCoordinates as $field => $coords) {
                        $currentX = $coords['x'] + ($xIncrement * ($i));
                        $currentY = $coords['y'] + $adjustment;
                
                        $fieldName = $field . '_' . $i;
                        $fieldValue = $request->input("fields.$fieldName");
                
                        if ($fieldValue !== null) {
                            $pdf->SetXY($currentX, $currentY);
                            $pdf->Write(0, utf8_decode($fieldValue));
                        }
                    }
                }
                

            } elseif ($pageNumber == 3) {
                
                $pdf->Image($ownerSignaturePath, 30, 242, 60, 20);
                
                $pdf->SetXY(32, 243);
                $pdf->Write(0, utf8_decode($ortDatum));
            }

            if ($pageNumber == 4) {

                $pdf->Image($orderSignaturePath, 10, 260, 60, 20);
                $pdf->Image($orderSignaturePath, 135, 260, 60, 20);
                $pdf->SetXY(100 , 82);
                $pdf->Write(0, utf8_decode($kundennummer));
                $pdf->SetXY(90, 172);
                $pdf->Write(0, utf8_decode($customer));
                $pdf->SetXY(90, 180.3);
                $pdf->Write(0, utf8_decode($combstrha));
                $pdf->SetXY(90, 188.6);
                $pdf->Write(0, utf8_decode($gebu));
                $pdf->SetXY(90, 196.9);
                $pdf->Write(0, utf8_decode($combplzort));
                $pdf->SetXY(90, 205.4);
                $pdf->Write(0, utf8_decode($abwcustom));
                $pdf->SetXY(90, 218);
                $pdf->Write(0, utf8_decode($combtel));
                $pdf->SetXY(90, 226.3);
                $pdf->Write(0, utf8_decode($iban));
                $pdf->SetXY(90, 234.6);
                $pdf->Write(0, utf8_decode($bank));
                $pdf->SetXY(32, 252);
                $pdf->Write(0, utf8_decode($ortDatum));
                

            }
        }
        $outputPdfPath = storage_path('app/' . uniqid() . '.pdf');
        $pdf->Output($outputPdfPath, 'F');
        
        $outputname = "$vorname $nachname " . date('Y-m-d');
        // Aufräumen
        // unlink($tempPdfPath); // Wenn nötig
        // unlink($FDFfile); // Wenn nötig
        unlink($orderSignaturePath);
        unlink($ownerSignaturePath);
        unlink($advisorSignaturePath);



         try {
             Mail::send('emails.sendPdf', ['name' => $username], function (Message $message) use ($outputPdfPath, $username, $customer) {
                $message->to('c.mehmann@rhein-ruhr-vertrieb.de')
                     ->subject('Neuer Auftrag von: ' . $username)
                         ->attach($outputPdfPath, [
                             'as' => $customer . '.pdf',
                            'mime' => 'application/pdf',
                         ]);
             });
         } catch (\Exception $e) {
             return response()->json(['success' => false, 'message' => 'Ein Fehler ist aufgetreten: ' . $e->getMessage()], 500);
         }

        
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
   
