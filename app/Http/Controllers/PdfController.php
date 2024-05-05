<?php

    namespace App\Http\Controllers;
    
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\File;
    use setasign\Fpdi\Fpdi;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Mail\Message;
    use Illuminate\Support\Facades\Storage;
    use App\Models\CompletedContract;
    use Carbon\Carbon;


    
    
    class PdfController extends Controller
    {
        public function fillPdf(Request $request)
    {
        $formData = $request->input('formData');
        $username           = Auth::user()->name;
        $anredeFrau = $formData['fields.Anrede_Frau'] === 'X' ? 'X' : '';
        $anredeHerr = $formData['fields.Anrede_Herr'] === 'X' ? 'X' : '';
        $anredeDivers = $formData['fields.Anrede_Divers'] === 'X' ? 'X' : '';
        $eheleute = htmlspecialchars($formData['fields.Eheleute'] ?? '');
        $titel = htmlspecialchars($formData['fields.Titel'] ?? '');
        $firmaGemeinschaft = htmlspecialchars($formData['fields.Firma_Gemeinschaft'] ?? '');
        $vorname = htmlspecialchars($formData['fields.Vorname'] ?? '');
        $nachname = htmlspecialchars($formData['fields.Nachname'] ?? '');
        $gebu = $formData['gb'] ?? '';
        $strasse = htmlspecialchars($formData['fields.Strasse'] ?? '');
        $hausnummer = htmlspecialchars($formData['fields.Hausnummer'] ?? '');
        $plz = htmlspecialchars($formData['fields.PLZ'] ?? '');
        $ort = htmlspecialchars($formData['fields.Ort'] ?? '');
        $telefonFestnetz = htmlspecialchars($formData['fields.Telefon_Festnetz'] ?? '');
        $telefonMobil = htmlspecialchars($formData['fields.Telefon_mobil'] ?? '');
        $emailAdresse = filter_var($formData['fields.EMailAdresse'] ?? '', FILTER_VALIDATE_EMAIL);
        $kundennummer = htmlspecialchars($formData['fields.kundennummer'] ?? '');
        $abwcustom = $formData['kontoinhaber'] ?? '';
        $iban = $formData['iban'] ?? '';
        $bank = $formData['bank'] ?? '';
        $we = $formData['anzahlwe'] ?? '';
        $gk = $formData['anzahlgk'] ?? '';
        $ortDatum = 'Langenfeld ,' . date('d.m.Y');
        $waipustick = $formData['waipustick'] ?? 'Nein';
        $cabletv = $formData['cabletv'] ?? 'Nein';
        $waipucomfort = $formData['waipucomfort'] ?? 'Nein';
        $waipuplus = $formData['waipuplus'] ?? 'Nein';
        $firstflat = $formData['firstflat'] ?? 'Nein';
        $secondflat = $formData['secondflat'] ?? 'Nein';
        $staticip = $formData['staticip'] ?? 'Nein';
        $postde = $formData['postde'] ?? 'Nein';
        $additionalFields = $formData['fields'] ?? [];
        $anbieter = $formData['anbieter'] ?? '';
        $tel1 = $formData['tel1'] ?? '';
        $tel2 = $formData['tel2'] ?? '';
        $selectedstrom = $formData['strom'] ?? '';
        $selectedgas = $formData['gas'] ?? '';
        $checkboxStatus = $formData['fields.StandardCheckboxStatus'] ?? '';
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
            $anredeFrau1 = $formData['fields.Anrede_Frau1'] ?? '';
            $anredeHerr1 = $formData['fields.Anrede_Herr1'] ?? '';
            $anredeDivers1 = $formData['fields.Anrede_Divers1'] ?? '';
            $eheleute1 = htmlspecialchars($formData['fields.Eheleute1'] ?? '');
            $titel1 = htmlspecialchars($formData['fields.Titel1'] ?? '');
            $firmaGemeinschaft1 = htmlspecialchars($formData['fields.Firma_Gemeinschaft1'] ?? '');
            $vorname1 = htmlspecialchars($formData['fields.Vorname1'] ?? '');
            $nachname1 = htmlspecialchars($formData['fields.Nachname1'] ?? '');
            $strasse1 = htmlspecialchars($formData['fields.Strasse1'] ?? '');
            $hausnummer1 = htmlspecialchars($formData['fields.Hausnummer1'] ?? '');
            $plz1 = htmlspecialchars($formData['fields.PLZ1'] ?? '');
            $ort1 = htmlspecialchars($formData['fields.Ort1'] ?? '');
            $telefonFestnetz1 = htmlspecialchars($formData['fields.Telefon_Festnetz1'] ?? '');
            $telefonMobil1 = htmlspecialchars($formData['fields.Telefon_mobil1'] ?? '');
            $emailAdresse1 = filter_var($formData['fields.EMailAdresse1'] ?? '', FILTER_VALIDATE_EMAIL);
        }
        $adresse = $strasse . ' ' . $hausnummer . ', ' . $plz . ' ' . $ort;
        $customer = $vorname . ' ,' . $nachname;
        $combplzort = $plz . ' ' . $ort;
        $combstrha = $strasse . ' ' . $hausnummer;
        $combtel = $telefonMobil . '  ' . $telefonFestnetz;
        $fillPage = false;
        if (($selectedstrom === 'strom12' || $selectedstrom === 'strom24') || 
            ($selectedgas === 'gas12' || $selectedgas === 'gas24')) {
            $fillPage = true;
        }

        
          $ownerSignaturePath   = $this->savSignature($request);
          $orderSignaturePath   = $this->saSignature($request);
          $advisorSignaturePath = $this->sSignature($request); 

        $pdfPath = storage_path('gnvlangenfeld.pdf');
        $pdf = new Fpdi();
        $pdf->setSourceFile($pdfPath);
        
        for ($pageNumber = 1; $pageNumber <= $pdf->setSourceFile($pdfPath); $pageNumber++) {

            if ($pageNumber == 6 && !($fillPage)) {
                continue;
            }

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
                

                $selectedOption = $formData['gfpaket'] ?? '';
                $selecteddevice = $formData['fritzBox'] ?? '';
                
                
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
                    if (isset($formData[$key]) && $formData[$key] === 'Ja') {
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
                
                        $fieldName = "fields[$field" . "_$i]";
                        $fieldValue = $formData[$fieldName] ?? '';
                
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
            if ($pageNumber == 5) {
                $pdf->SetXY(150 , 48.5);
                $pdf->Write(0, utf8_decode($vorname));
                $pdf->SetXY(36, 48.5);
                $pdf->Write(0, utf8_decode($nachname));
                $pdf->SetXY(80, 59.5);
                $pdf->Write(0, utf8_decode($ort));
                $pdf->SetXY(31, 59.5);
                $pdf->Write(0, utf8_decode($plz));
                $pdf->SetXY(34, 54);
                $pdf->Write(0, utf8_decode($strasse));
                $pdf->SetXY(150 , 54);
                $pdf->Write(0, utf8_decode($hausnummer));
                $textWidth = $pdf->GetStringWidth($anbieter);
                while ($textWidth > $maxWidth && $fontSize > 2) {
                    $fontSize--;
                    $pdf->SetFont('Arial', '', $fontSize);
                    $textWidth = $pdf->GetStringWidth($anbieter);
                }
                $pdf->SetXY(165 , 28);
                $pdf->Write(0, utf8_decode($anbieter));
                $pdf->SetFont('Arial', '', 12);
                $pdf->SetXY(97 , 72);
                $pdf->Write(0, utf8_decode($tel1));
                $pdf->SetXY(97 , 78);
                $pdf->Write(0, utf8_decode($tel2));
                $pdf->SetXY(34, 127);
                $pdf->Write(0, utf8_decode($ortDatum));
                $pdf->SetXY(14, 23);
                $pdf->Write(0, 'X');
                $pdf->SetXY(14, 41);
                $pdf->Write(0, 'X');

                $pdf->Image($orderSignaturePath, 132, 105, 60, 20);
            }
            if ($pageNumber == 6) {
                $pdf->Image($orderSignaturePath, 10, 260, 60, 20);
                $pdf->Image($advisorSignaturePath, 135, 260, 60, 20);


                $checkboxCoordinates = [
                    'strom24' => ['x' => 24, 'y' => 92],
                    'strom12' => ['x' => 24, 'y' => 98],
                    'gas24' => ['x' => 24, 'y' => 104],
                    'gas12' => ['x' => 24, 'y' => 110],
                ];

                $selectedstromKey  = ''; 
                $selectedgasKey    = ''; 

                switch ($selectedstrom) {
                    case 'strom24':
                        $selectedstromKey = 'strom24';
                        break;
                    case 'strom12':
                        $selectedstromKey = 'strom12';
                        break;
                }
                switch ($selectedgas) {
                    case 'gas24':
                        $selectedgasKey = 'gas24';
                        break;
                    case 'gas12':
                        $selectedgasKey = 'gas12';
                        break;
                }

                foreach ($checkboxCoordinates as $key => $coords) {
                    $isChecked = false;
                    if (isset($$key) && $$key === 'on') {
                        $isChecked = true;
                    }
                    if ($key === $selectedstromKey  || $key === $selectedgasKey) {
                        $isChecked = true;
                    }
                    if ($isChecked) {
                        $pdf->SetXY($coords['x'], $coords['y']);
                        $pdf->Write(0, "X");
                    }
                }



                
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
            }
        }
        $outputPdfPath = storage_path('app/public/' . uniqid() . '.pdf');
        $pdf->Output($outputPdfPath, 'F');
        
        $outputname = "$vorname $nachname " . date('Y-m-d');
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
                   return response()->json(['success' => false, 'message' => 'Ein Fehler ist aufgetreten: ' . $e->getMessage()],500);
                }

        
            $pdfUrl = url('/storage/' . basename($outputPdfPath));; 
            if (file_exists($outputPdfPath)) {
                return response()->json([
                    'success' => true,
                    'url' => $pdfUrl  
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'PDF konnte nicht erstellt werden.'
                ]);
            }
    }
       
    public function fillPdfUgg(Request $request)
    {   
        $username           = Auth::user()->name;
        $anrede             = $request->input('anrede');
        $titel              = $request->input('titel');
        $vorname            = $request->input('vorname');
        $nachname           = $request->input('nachname');
        $gebu               = $request->input('geburtstag');
        $carbonDate         = Carbon::createFromFormat('Y-m-d', $gebu);
        $geburtstag         = $carbonDate->format('d.m.Y');
        $strasse            = $request->input('strasse');
        $hausnummer         = $request->input('hausnummer');
        $plz                = $request->input('plz');
        $ort                = $request->input('ort');
        $telefonnummer      = $request->input('telefonnummer');
        $handynummer        = $request->input('handynummer');
        $email              = $request->input('email');
        $lieferdatum_typ    = $request->input('lieferdatumTyp');
        $lieferdatum        = $request->input('lieferdatum');
        $anbieter           = $request->input('anbieter');
        $bank               = $request->input('bank');
        $iban               = $request->input('iban');
        $kennzahl           = $request->input('kennzahl');
        $firma              = $request->input('firma');
        $gfpaket            = $request->input('gfpaket');
        $hardware           = $request->input('hardwareOption');
        $festnetzoption     = $request->input('festnetzOption');
        $rufnummer_1        = $request->input('rufnummer_1');        
        $rufnummer_2        = $request->input('rufnummer_2');
        $rufnummer_3        = $request->input('rufnummer_3');
        $rufnummer_4        = $request->input('rufnummer_4');
        $rufnummer_5        = $request->input('rufnummer_5');
        $rufnummer_6        = $request->input('rufnummer_6');
        $rufnummer_7        = $request->input('rufnummer_7');
        $rufnummer_8        = $request->input('rufnummer_8');
        $rufnummer_9        = $request->input('rufnummer_9');
        $rufnummer_10       = $request->input('rufnummer_10');
        $we                 = $request->input('we');

        $ortDatum           = $ort . ' ,' . date('d.m.Y');
        $customer = $vorname . ' ,' . $nachname;
        $combplzort = $plz . ' ' . $ort;
        $combstrha = $strasse . ' ' . $hausnummer;

        $signaturePathData = $this->saveSignature($request);
        $pdfPath = storage_path('uggauftrag.pdf');
        $pdf = new Fpdi();
 
        
        try {
            $pdf->setSourceFile($pdfPath);
            $numPages = $pdf->setSourceFile($pdfPath);
            $maxWidth = 30;
            $fontSize = 12; 
        
            // Durchgehen aller Seiten
            for ($pageNo = 1; $pageNo <= $numPages; $pageNo++) {
                $templateIndex = $pdf->importPage($pageNo);
                $pdf->AddPage();
                $pdf->useTemplate($templateIndex);
        
                // Setzen Sie den Schrifttyp, die Größe und fügen Sie die Daten ein
                $pdf->SetFont('Arial');
                $pdf->SetFontSize(10);
                
                if ($pageNo == 1) {  
                    if($anrede == 'Herr'){
                        $pdf->SetXY(27, 108);
                        $pdf->Write(0, 'X'); 
                    }
                    if($anrede == 'Frau'){
                        $pdf->SetXY(41, 108);
                        $pdf->Write(0, 'X'); 
                    }

                    $pdf->SetXY(80 , 57);
                    $pdf->Write(0, utf8_decode($kennzahl));
                    $pdf->SetXY(42 , 102);
                    $pdf->Write(0, utf8_decode($firma));
                    $pdf->SetXY(42 , 114.4);
                    $pdf->Write(0, utf8_decode($titel));
                    $pdf->SetXY(80 , 108);
                    $pdf->Write(0, utf8_decode($geburtstag)); 
                    $pdf->SetXY(42 , 126.7);
                    $pdf->Write(0, utf8_decode($vorname));
                    $pdf->SetXY(42, 120.5);
                    $pdf->Write(0, utf8_decode($nachname));
                    $pdf->SetXY(135, 57.5);
                    $pdf->Write(0, utf8_decode($ort));
                    $pdf->SetXY(135, 52);
                    $pdf->Write(0, utf8_decode($plz));
                    $pdf->SetXY(135, 46.5);
                    $pdf->Write(0, utf8_decode($strasse));
                    $pdf->SetXY(186 , 46.5);
                    $pdf->Write(0, utf8_decode($hausnummer));
                    $pdf->SetXY(158 , 65);
                    $pdf->Write(0, utf8_decode($telefonnummer));
                    $pdf->SetXY(158 , 72);
                    $pdf->Write(0, utf8_decode($handynummer));
                    $pdf->SetXY(135, 78.5);
                    $pdf->Write(0, utf8_decode($email));
                    $pdf->SetXY(135, 203);
                    $pdf->Write(0, utf8_decode($customer));
                    $pdf->SetXY(135, 208.5);
                    $pdf->Write(0, utf8_decode($iban));
                    $pdf->SetXY(135, 221);
                    $pdf->Write(0, utf8_decode($bank));

                }

                if ($pageNo == 2) {
                    if($gfpaket == 'gf100'){
                        $pdf->SetXY(17, 47);
                        $pdf->Write(0, 'X'); 
                    }
                    if($gfpaket == 'gf250'){
                        $pdf->SetXY(17, 134);
                        $pdf->Write(0, 'X'); 
                    }
                }

                if ($pageNo == 3) {
                    if($gfpaket == 'gf500'){
                        $pdf->SetXY(17, 46.5);
                        $pdf->Write(0, 'X'); 
                    }
                    if($gfpaket == 'gf1000'){
                        $pdf->SetXY(17, 122.5);
                        $pdf->Write(0, 'X'); 
                    }
                    
                }

                if ($pageNo == 4) {
                    if($hardware == 'o2homebox'){
                        $pdf->SetXY(17, 46.5);
                        $pdf->Write(0, 'X'); 
                    }
                    if($hardware == 'fritzbox'){
                        $pdf->SetXY(17, 58.5);
                        $pdf->Write(0, 'X'); 
                    }
                    if($festnetzoption == 'isdn'){
                        $pdf->SetXY(17, 82);
                        $pdf->Write(0, 'X'); 
                    }

                }

                if ($pageNo == 5) {
                    
                }
                
                if ($pageNo == 6) {

                     if($lieferdatum_typ == 'schnellstmöglich'){
                         $pdf->SetXY(108, 48);
                         $pdf->Write(0, utf8_decode('Nächstmöglicher Zeitpunkt'));
                     }else {
                        $pdf->SetXY(108, 48);
                         $pdf->Write(0, utf8_decode($lieferdatum));
                     }
                    $pdf->SetXY(115, 165.5);
                    $pdf->Write(0, utf8_decode($ort));
                    $pdf->SetXY(85, 165.5);
                    $pdf->Write(0, utf8_decode($plz));
                    $pdf->SetXY(85, 158);
                    $pdf->Write(0, utf8_decode($strasse));
                    $pdf->SetXY(174 , 158);
                    $pdf->Write(0, utf8_decode($hausnummer));
                    
                }

                if ($pageNo == 7) {

                }

                if ($pageNo == 8) {
                    $pdf->SetXY(15, 243);
                    $pdf->Write(0, utf8_decode($ortDatum));

    
                    $pdf->Image($signaturePathData, 55, 240, 40, 10);

                    $pdf->SetXY(105, 243);
                    $pdf->Write(0, utf8_decode($ortDatum));

    
                    $pdf->Image($signaturePathData, 145, 240, 40, 10);
                    
                }
        
                if ($pageNo == 10) {
                    $pdf->SetXY(152 , 70);
                    $pdf->Write(0, utf8_decode($vorname));
                    $pdf->SetXY(36, 70);
                    $pdf->Write(0, utf8_decode($nachname));
                    $pdf->SetXY(68, 82);
                    $pdf->Write(0, utf8_decode($ort));
                    $pdf->SetXY(36, 82);
                    $pdf->Write(0, utf8_decode($plz));
                    $pdf->SetXY(36, 76);
                    $pdf->Write(0, utf8_decode($strasse));
                    $pdf->SetXY(152 , 76);
                    $pdf->Write(0, utf8_decode($hausnummer));
                    $textWidth = $pdf->GetStringWidth($anbieter);
                    while ($textWidth > $maxWidth && $fontSize > 2) {
                        $fontSize--;
                        $pdf->SetFont('Arial', '', $fontSize);
                        $textWidth = $pdf->GetStringWidth($anbieter);
                    }
                    $pdf->SetXY(160 , 48);
                    $pdf->Write(0, utf8_decode($anbieter));
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->SetXY(73 , 99);
                    $pdf->Write(0, utf8_decode($rufnummer_1));
                    $pdf->SetXY(73 , 105);
                    $pdf->Write(0, utf8_decode($rufnummer_2));
                    $pdf->SetXY(73 , 110.5);
                    $pdf->Write(0, utf8_decode($rufnummer_3));
                    $pdf->SetXY(73 , 116);
                    $pdf->Write(0, utf8_decode($rufnummer_4));
                    $pdf->SetXY(73 , 121.5);
                    $pdf->Write(0, utf8_decode($rufnummer_5));
                    $pdf->SetXY(139 , 99);
                    $pdf->Write(0, utf8_decode($rufnummer_6));
                    $pdf->SetXY(139 , 105);
                    $pdf->Write(0, utf8_decode($rufnummer_7));
                    $pdf->SetXY(139 , 110.5);
                    $pdf->Write(0, utf8_decode($rufnummer_8));
                    $pdf->SetXY(139 , 116);
                    $pdf->Write(0, utf8_decode($rufnummer_9));
                    $pdf->SetXY(139 , 121.5);
                    $pdf->Write(0, utf8_decode($rufnummer_10));
                    $pdf->SetXY(28, 148);
                    $pdf->Write(0, utf8_decode($ortDatum));
                    $pdf->SetXY(18, 64);
                    $pdf->Write(0, 'X');
                    $pdf->SetXY(18, 90);
                    $pdf->Write(0, 'X');
    
                    $pdf->Image($signaturePathData, 138, 140, 40, 10);
                }

                if ($pageNo == 11) {
                    $pdf->SetXY(50 , 105);
                    $pdf->Write(0, utf8_decode($customer));
                    $pdf->SetXY(50, 113);
                    $pdf->Write(0, utf8_decode($combstrha));
                    $pdf->SetXY(70, 121);
                    $pdf->Write(0, utf8_decode($ort));
                    $pdf->SetXY(50, 121);
                    $pdf->Write(0, utf8_decode($plz));
                    $pdf->SetXY(50, 129);
                    $pdf->Write(0, utf8_decode($email));
                    $pdf->SetXY(50, 138);
                    $pdf->Write(0, utf8_decode($bank));
                    $pdf->SetXY(50, 145.5);
                    $pdf->Write(0, utf8_decode($iban));
                    $pdf->SetXY(50, 157);
                    $pdf->Write(0, utf8_decode($ortDatum));

                    $pdf->Image($signaturePathData, 90, 160, 40, 10);
                }

                if ($pageNo == 12) {
                    $pdf->SetFontSize(12);

                    if($anrede == 'Herr'){
                        $pdf->SetXY(39, 83);
                        $pdf->Write(0, 'X'); 
                    }
                    if($anrede == 'Frau'){
                        $pdf->SetXY(14, 83);
                        $pdf->Write(0, 'X'); 
                    }

                    $pdf->SetXY(16 , 97);
                    $pdf->Write(0, utf8_decode($customer));
                    $pdf->SetXY(16, 128.7);
                    $pdf->Write(0, utf8_decode($telefonnummer));
                    $pdf->SetXY(105, 128.7);
                    $pdf->Write(0, utf8_decode($handynummer));
                    $pdf->SetXY(16, 144.7);
                    $pdf->Write(0, utf8_decode($email));
                    $pdf->SetXY(144, 144.7);
                    $pdf->Write(0, utf8_decode($geburtstag));
                    $pdf->SetXY(16, 160.5);
                    $pdf->Write(0, utf8_decode($combstrha));
                    $pdf->SetXY(16, 176.5);
                    $pdf->Write(0, utf8_decode($combplzort));
                    $pdf->SetXY(16 , 192.7);
                    $pdf->Write(0, utf8_decode($ort));

                    if($we == '1'){
                        $pdf->SetXY(60, 255.5);
                        $pdf->Write(0, 'X'); 
                    }
                    if($we == '2'){
                        $pdf->SetXY(73, 255.5);
                        $pdf->Write(0, 'X'); 
                    }
                    if($we == '3'){
                        $pdf->SetXY(87, 255.5);
                        $pdf->Write(0, 'X'); 
                    }

                    
                }
                
                if ($pageNo == 13) {
                    $pdf->SetFontSize(12);

                    $pdf->SetXY(18, 173);
                    $pdf->Write(0, utf8_decode($ortDatum));

    
                    $pdf->Image($signaturePathData, 120, 165, 40, 10);
                    
                }
            }
        
            $outputPath = storage_path('app/filled_ugg_' . uniqid() . '.pdf');
            $pdf->Output('F', $outputPath);
        
            unlink($signaturePathData);

              try {
                  Mail::send('emails.sendPdf', ['name' => $username], function (Message $message) use ($outputPath, $username, $customer) {
                    $message->to('c.mehmann@rhein-ruhr-vertrieb.de')
                         ->subject('Neuer Auftrag von: ' . $username)
                             ->attach($outputPath, [
                                'as' => $customer . '.pdf',
                                'mime' => 'application/pdf',
                              ]);
                  });
              } catch (\Exception $e) {
                 return response()->json(['success' => false, 'message' => 'Ein Fehler ist aufgetreten: ' . $e->getMessage()],500);
              }

            return response()->download($outputPath, 'filled_ugg.pdf', [
                'Content-Type' => 'application/pdf'
            ])->deleteFileAfterSend(true);            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

        public function showForm($pdfUrl = null)
        {
            return view('pdf.form')->with('pdfUrl', $pdfUrl);
        }
        public function uggform()
        {
            return view('pdf.uggform');
        }

        public function showProduct()
        {
            return view('pdf.productswl');
        }

        public function deletePdf(Request $request)
        {
            $url = $request->input('filePath');
            $path = parse_url($url, PHP_URL_PATH);
            $basePath = '/storage/'; // Basispfad, angepasst an deine Struktur
            $filePath = substr($path, strlen($basePath)); // Pfad ohne Basis-URL
    
            // Pfad zur Datei auf dem Server
            $fullPath = storage_path('app/public/' . $filePath);
    
            // Datei löschen, wenn sie existiert
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
                return response()->json(['success' => true, 'message' => 'Datei erfolgreich gelöscht']);
            } else {
                return response()->json(['success' => false, 'message' => 'Datei nicht gefunden']);
            }
        }

        function saveSignature(Request $request)
        {
            $base64Image = $request->input('unterschrift'); // oder der entsprechende Schlüssel Ihrer Data-URL
            // Extrahieren Sie den eigentlichen Base64-String aus der Data-URL
            @list($type, $fileData) = explode(';', $base64Image);
            @list(, $fileData) = explode(',', $fileData);
        
            if ($fileData != "") {
                $decodedImageData = base64_decode($fileData);
        
                $fileName = 'signature_' . uniqid() . '.png';
                $filePath = 'signatures/' . $fileName;
        
                Storage::disk('local')->put($filePath, $decodedImageData);
        
                
                return storage_path('app/' . $filePath);
            }
        
            return null;
        }

        function savSignature(Request $request)
        {
            $base64Image = $request->input('owner_signature'); // oder der entsprechende Schlüssel Ihrer Data-URL
            // Extrahieren Sie den eigentlichen Base64-String aus der Data-URL
            @list($type, $fileData) = explode(';', $base64Image);
            @list(, $fileData) = explode(',', $fileData);
        
            if ($fileData != "") {
                $decodedImageData = base64_decode($fileData);
        
                $fileName = 'signature_' . uniqid() . '.png';
                $filePath = 'signatures/' . $fileName;
        
                Storage::disk('local')->put($filePath, $decodedImageData);
        
                
                return storage_path('app/' . $filePath);
            }
        
            return null;
        }
        function saSignature(Request $request)
        {
            $base64Image = $request->input('order_signature'); // oder der entsprechende Schlüssel Ihrer Data-URL
            // Extrahieren Sie den eigentlichen Base64-String aus der Data-URL
            @list($type, $fileData) = explode(';', $base64Image);
            @list(, $fileData) = explode(',', $fileData);
        
            if ($fileData != "") {
                $decodedImageData = base64_decode($fileData);
        
                $fileName = 'signature_' . uniqid() . '.png';
                $filePath = 'signatures/' . $fileName;
        
                Storage::disk('local')->put($filePath, $decodedImageData);
        
                
                return storage_path('app/' . $filePath);
            }
        
            return null;
        }
        function sSignature(Request $request)
        {
            $base64Image = $request->input('advisor_signature'); // oder der entsprechende Schlüssel Ihrer Data-URL
            // Extrahieren Sie den eigentlichen Base64-String aus der Data-URL
            @list($type, $fileData) = explode(';', $base64Image);
            @list(, $fileData) = explode(',', $fileData);
        
            if ($fileData != "") {
                $decodedImageData = base64_decode($fileData);
        
                $fileName = 'signature_' . uniqid() . '.png';
                $filePath = 'signatures/' . $fileName;
        
                Storage::disk('local')->put($filePath, $decodedImageData);
        
                
                return storage_path('app/' . $filePath);
            }
        
            return null;
        }
                public function createContract(Request $request)
        {
            if (!Auth::check()) {
                return redirect()->back()->withErrors(['msg' => 'Nicht angemeldet']);
            }
            $vorname            = $request->input('fields_Vorname');
            $nachname           = $request->input('fields_Nachname');
            $plz                = $request->input('fields_PLZ');
            $ort                = $request->input('fields_Ort');
            $str                = $request->input('fields_Strasse');
            $ha                 = $request->input('fields_Hausnummer');
            $straha             = $str . ' ' . $ha;
            $ort1               = $plz . ' ' . $ort;
            $kundenname         = $vorname . ' , ' . $nachname;
            $firstFlat = $request->has('firstflat') ? 1 : 0;
            $fritzBox = $request->input('fritzBox') === 'none' ? 0 : 1;
            $gfpaket = $request->input('gfpaket');


            $contract = new CompletedContract([
                'user_id' => Auth::id(),
                'ort' => $ort1,
                'adresse' => $straha,
                'status' => "Erstellt",
                'notiz' => "",
                'kundenname' => $kundenname,
                'gfpaket' => $gfpaket,
                'firstflat' => $firstFlat,
                'fritzbox' => $fritzBox,
            ]);

            $contract->save();
            return response()->json(['success' => true, 'message' => 'Vertrag erstellt.']);
        }
                public function showAllContracts()
        {
            if (!Auth::user()->hasRole('Admin')) {
                return redirect()->route('home')->withErrors('Unauthorised');
            }

            $contracts = CompletedContract::with('user')
            ->latest()
            ->paginate(15);
            return view('admin.contracts', compact('contracts'));
        }

        public function updateContract(Request $request, $id)
        {
            $contract = CompletedContract::findOrFail($id);
            $contract->status = $request->status;
            if ($request->has('note') && $request->note !== null) {
                $contract->notiz = $request->note;
            }
            $contract->save();

            return response()->json(['message' => 'Auftrag erfolgreich aktualisiert']);
        }

        public function deleteContract($contractId)
        {
            $contract = CompletedContract::findOrFail($contractId);
            if (Auth::user()->hasRole('Admin')) {


                $contract->delete();

                return response()->json(['message' => 'Auftrag gelöscht']);
            }

            return back()->with('error', 'Nur Administratoren dürfen diese Aktion ausführen.');
        }

    }
   
