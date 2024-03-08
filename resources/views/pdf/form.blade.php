@extends('layouts.app')

@section('content')
<style>
        .signature-container {
        margin-top: 20px;
        border: 2px solid #000; /* Schwarzer Rand */
        background-color: #fff; /* Weißer Hintergrund */
        padding: 10px; /* Innenabstand für den Container */
        margin-bottom: 20px; /* Platz zwischen den Signaturen */
    }

        .bg-purple {
    background-color: #8a2be2;
}
    #page2 {
        display: none;
    }

    form {
        max-width: 800px;
        margin: auto;
        padding: 20px;
        background-color: #f4f4f4;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    fieldset {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 15px;
    }

    legend {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    label {
        display: block;
        margin-bottom: 5px;
    }

    input[type="text"] {
        width: calc(100% - 12px);
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
    }

    input[type="checkbox"] {
        margin-right: 5px;
    }

    button {
        background-color: #4caf50;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
</style>
    <h1></h1>
<div id="page1">
    <form method="post" action="{{ route('pdf.fill') }}" enctype="multipart/form-data">
        @csrf


        <fieldset>
            <legend>Persönliche Informationen</legend>
            <label for="Frau">Frau</label>
            <input type="checkbox" name="fields[Anrede_Frau]" value="Frau" id="Frau">
            
            <label for="Mann">Herr</label>
            <input type="checkbox" name="fields[Anrede_Herr]" value="Herr" id="Mann">
            
            <label for="Divers">Divers</label>
            <input type="checkbox" name="fields[Anrede_Divers]" value="Divers" id="Divers">            
            

            <label for="Eheleute">Eheleute:</label>
            <input type="text" name="fields[Eheleute]" id="Eheleute" >

            <label for="Titel">Titel:</label>
            <input type="text" name="fields[Titel]" id="Titel" >

            <label for="Firma_Gemeinschaft">Firma / Gemeinschaft:</label>
            <input type="text" name="fields[Firma_Gemeinschaft]" id="Firma_Gemeinschaft" >

            <label for="Vorname">Vorname:</label>
            <input type="text" name="fields[Vorname]" id="Vorname" required>

            <label for="Nachname">Nachname:</label>
            <input type="text" name="fields[Nachname]" id="Nachname" required>
        </fieldset>

        <fieldset>
            <legend>Adresse</legend>
            <label for="Strasse">Straße:</label>
            <input type="text" name="fields[Strasse]" id="Strasse" required>

            <label for="Hausnummer">Hausnummer:</label>
            <input type="text" name="fields[Hausnummer]" id="Hausnummer" required>

            <label for="PLZ">PLZ:</label>
            <input type="text" name="fields[PLZ]" id="PLZ" required>

            <label for="Ort">Ort:</label>
            <input type="text" name="fields[Ort]" id="Ort" required>
        </fieldset>

        <fieldset>
            <legend>Kontaktinformationen</legend>
            <label for="Telefon_Festnetz">Telefon Festnetz:</label>
            <input type="text" name="fields[Telefon_Festnetz]" id="Telefon_Festnetz" >

            <label for="Telefon_mobil">Telefon mobil:</label>
            <input type="text" name="fields[Telefon_mobil]" id="Telefon_mobil" >

            <label for="EMailAdresse">E-Mail-Adresse:</label>
            <input type="text" name="fields[EMailAdresse]" id="EMailAdresse" >
        </fieldset>

        <fieldset>
            <h3>Sind Sie bereits Kunde?</h3>
            <label for="kundennummer">Wenn ja Ihre Kundennummer:</label>
            <input type="text" name="fields[kundennummer]" id="kundennummer" >
        </fieldset>
        <button type="button" onclick="return validateFormAndShowNextPage();">Weiter</button>
    </form>
</div>

<div id="page2">
    <form id="formOnPage2" enctype="multipart/form-data">
        @csrf

        <fieldset>
            <h3>Sind Sie Eigentümer?</h3>
            <input type="checkbox" id="standardCheckbox" checked>
            <label for="standardCheckbox">Ja, Vertragspartner auch Eigentümer</label>
        <div id="zusatzFelder" style="display: none;">  
            <fieldset>
                <legend>Persönliche Informationen</legend>
                <label for="Frau">Frau</label>
                <input type="checkbox" name="fields[Anrede_Frau1]" value="X" id="Frau">
                
                <label for="Mann">Herr</label>
                <input type="checkbox" name="fields[Anrede_Herr1]" value="X" id="Mann">
                
                <label for="Divers">Divers</label>
                <input type="checkbox" name="fields[Anrede_Divers1]" value="X" id="Divers">            
                
    
                <label for="Eheleute">Eheleute:</label>
                <input type="text" name="fields[Eheleute1]" id="Eheleute" >
    
                <label for="Titel">Titel:</label>
                <input type="text" name="fields[Titel1]" id="Titel" >
    
                <label for="Firma_Gemeinschaft">Firma / Gemeinschaft:</label>
                <input type="text" name="fields[Firma_Gemeinschaft1]" id="Firma_Gemeinschaft" >
    
                <label for="Vorname">Vorname:</label>
                <input type="text" name="fields[Vorname1]" id="Vorname" >
    
                <label for="Nachname">Nachname:</label>
                <input type="text" name="fields[Nachname1]" id="Nachname" >
            </fieldset>
    
            <fieldset>
                <legend>Adresse</legend>
                <label for="Strasse">Straße:</label>
                <input type="text" name="fields[Strasse1]" id="Strasse" >
    
                <label for="Hausnummer">Hausnummer:</label>
                <input type="text" name="fields[Hausnummer1]" id="Hausnummer" >
    
                <label for="PLZ">PLZ:</label>
                <input type="text" name="fields[PLZ1]" id="PLZ" >
    
                <label for="Ort">Ort:</label>
                <input type="text" name="fields[Ort1]" id="Ort" >
            </fieldset>
    
            <fieldset>
                <legend>Kontaktinformationen</legend>
                <label for="Telefon_Festnetz">Telefon Festnetz:</label>
                <input type="text" name="fields[Telefon_Festnetz1]" id="Telefon_Festnetz" >
    
                <label for="Telefon_mobil">Telefon mobil:</label>
                <input type="text" name="fields[Telefon_mobil1]" id="Telefon_mobil" >
    
                <label for="EMailAdresse">E-Mail-Adresse:</label>
                <input type="text" name="fields[EMailAdresse1]" id="EMailAdresse" >
            </fieldset>
        </div>
    </fieldset> 

    <fieldset>
        <h3>Gibt es zusätzliche Wohneinheiten?</h3>
        <label for="anzahlWohneinheiten">Anzahl der Wohneinheiten:</label>
        <select id="anzahlWohneinheiten" name="anzahlWohneinheiten" onchange="createAdditionalFields()">
            <option value="0">0</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
        </select>
    </fieldset>

    <fieldset>
        <div id="additionalFieldsContainer"></div>
    </fieldset>


<div class="row justify-content-md-center">
    <div class="col-md-4 mb-3">
        <div class="card text-start h-100 border">
            <div class="card-body">
                <div class="mb-4">
                    <h3>LFeld Basic 150/75</h5>
                </div>
                <hr>
                <div class="bg-light">
                    <ul class="list-unstyled">
                        <li class="p-1">Download bis zu 150Mbit/s</li>
                        <li class="p-1">Upload bis zu 75 Mbit/s</li>
                    </ul>
                </div>
                <div class="">
                    <ul class="list-unstyled">
                        <li class="p-1">Laufzeit: 24Monate</li>
                        <li class="p-1">Aktionspreis 1-6Monat: 24,90€ </li>
                        <li class="p-1">Danach 34,90€</li>
                        <strong><li class="p-2">Anschlussgebühr 0€!</li></strong>
                    </ul>
                </div>
                <hr>
                <div class="bg-purple bg-gradient p-1 text-white">
                    <div class="error-placeholder">
                        <div class="form-check">
                            <input value="gf15024m" id="gf15024m" class="form-check-input" name="gfpaket" type="radio"> <label for="gf150">Tarif wählen</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card text-left h-100 border">
            <div class="card-body">
                <div class="mb-4">
                    <h3>LFeld Basic 150/75 12M</h5>
                </div>
                <hr>
                <div class="bg-light">
                    <ul class="list-unstyled">
                        <li class="p-1">Download bis zu 150Mbit/s</li>
                        <li class="p-1">Upload bis zu 75 Mbit/s</li>
                    </ul>
                </div>
                <div class="">
                    <ul class="list-unstyled">
                        <li class="p-1">Laufzeit: 12Monate</li>
                        <li class="p-1">Dauerhaft: 34,90€</li>
                        <li class="p-1">Kein Aktionspreis</li>
                        <strong><li class="p-1">Anschlussgebühr 0€!</li></strong>   
                    </ul>
                </div>
                <hr>
                <div class="error-placeholder">
                    <div class="bg-purple bg-gradient p-1 text-white">
                        <div class="form-check">
                            <input value="gf15012m" id="gf15012m" class="form-check-input" name="gfpaket" type="radio"> <label>Tarif wählen</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card text-start h-100 border">
            <div class="card-body">
                <div class="mb-4">
                    <h3>LFeld Classic 300/150</h5>
                </div>
                <hr>
                <div class="bg-light">
                    <ul class="list-unstyled">
                        <li class="p-1">Download bis zu 300 Mbit/s</li>
                        <li class="p-1">Upload bis zu 150 Mbit/s</li>
                    </ul>
                </div>
                <div class="">
                    <ul class="list-unstyled">
                        <li class="p-1">Laufzeit: 24Monate</li>
                        <li class="p-1">Aktionspreis 1-6Monat: 34,90€ </li>
                        <li class="p-1">Danach 44,90€</li>
                        <strong><li class="p-1">Anschlussgebühr 0€!</li></strong>
                    </ul>
                </div>
                <hr>
                <div class="bg-purple bg-gradient p-1 text-white">
                    <div class="error-placeholder">
                        <div class="form-check">
                            <input value="gf300" id="gf300" class="form-check-input" name="gfpaket" type="radio"> <label>Tarif wählen</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card text-start h-100 border">
            <div class="card-body">
                <div class="mb-4">
                    <h3>LFeld Perform 600/300</h3>
                </div>
                <hr>
                <div class="bg-light">
                    <ul class="list-unstyled">
                        <li class="p-1">Download bis zu 600 Mbit/s</li>
                        <li class="p-1">Upload bis zu 300 Mbit/s</li>
                    </ul>
                </div>
                <div class="">
                    <ul class="list-unstyled">
                        <li class="p-1">Laufzeit: 24Monate</li>
                        <li class="p-1">Aktionspreis 1-6Monat: 59,90€ </li>
                        <li class="p-1">Danach 69,90€</li>
                        <strong><li class="p-1">Anschlussgebühr 0€!</li></strong>
                    </ul>
                </div>
                <hr>
                <div class="bg-purple bg-gradient p-1 text-white">
                    <div class="error-placeholder">
                        <div class="form-check">
                            <input value="gf600" id="gf600" class="form-check-input" name="gfpaket" type="radio"> <label>Tarif wählen</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card text-start h-100 border">
            <div class="card-body">
                <div class="mb-4">
                    <h3>LFeld Expert 1000/500</h5>
                </div>
                <hr>
                <div class="bg-light">
                    <ul class="list-unstyled">
                        <li class="p-1">Download bis zu 1 Gbit/s</li>
                        <li class="p-1">Upload bis zu 500 Mbit/s</li>
                    </ul>
                </div>
                <div class="">
                    <ul class="list-unstyled">
                        <li class="p-1">Laufzeit: 24Monate</li>
                        <li class="p-1">Aktionspreis 1-6Monat: 69,90€ </li>
                        <li class="p-1">Danach 89,90€</li>
                        <strong><li class="p-1">Anschlussgebühr 0€!</li></strong>
                    </ul>
                </div>
                <hr>
                <div class="error-placeholder">
                    <div class="bg-purple bg-gradient p-1 text-white">
                        <div class="form-check">
                            <input value="gf1000" id="gf1000" class="form-check-input" name="gfpaket" type="radio"> <label>Tarif wählen</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mt-3 justify-content-md-center">
    <div class="col-sm-6">
        <div class="card text-left h-100 border">
            <div class="card-body">
                <h4 class="bg-light p-1">Benötigen Sie ein zusätzliches Gerät?</h4>
                <hr>
                <div class="col">
                    <div class="mb-3 error-placeholder">
                        <div class="form-check">
                            <input value="gfboxonetime" id="gfboxonetime" class="form-check-input" name="fritzBox" type="radio"> <label for="gfboxonetime">Fritz!Box 5530 Kauf: 209,00€</label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="mb-3 error-placeholder">
                        <div class="form-check">
                            <input value="gfboxrent" id="gfboxrent" class="form-check-input" name="fritzBox" type="radio"> <label for="gfboxrent">Fritz!Box 5530 Miete: 6,00€ mtl.</label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="mb-3 error-placeholder">
                        <div class="form-check">
                            <input id="waipustick" class="form-check-input" type="checkbox" name="waipustick" > <label for="waipustick">Waipu.tv 4K Stick 59,99€</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mt-3 justify-content-md-center">
    <div class="col-sm-6">
        <div class="card text-left h-100 border">
            <div class="card-body">
                <h4 class="bg-light p-1">Welche Zusatzoptionen benötigen Sie?</h4>
                <hr>
                <div class="col">
                    <div class="mb-3 error-placeholder">
                        <div class="form-check">
                            <input name="cabletv" id="cabletv" class="form-check-input" type="checkbox"> <label for="cabletv">Kabelfernsehen: 10,00€ mtl.</label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="mb-3 error-placeholder">
                        <div class="form-check">
                            <input name="waipucomfort" id="waipucomfort" class="form-check-input"  type="checkbox"> <label for="waipucomfort">Waipu.tv Comfort: 7,49€ mtl.</label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="mb-3 error-placeholder">
                        <div class="form-check">
                            <input name="waipuplus" id="waipuplus" class="form-check-input"  type="checkbox"> <label for="waipuplus">Waipu.tv Perfect Plus: 12,99€ mtl.</label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="mb-3 error-placeholder">
                        <div class="form-check">
                            <input name="firstflat" id="firstflat" class="form-check-input"  type="checkbox"> <label for="firstflat">1. Rufn. inkl. Festnetz-Flatrate 2023: 5,90€ mtl.</label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="mb-3 error-placeholder">
                        <div class="form-check">
                            <input name="secondflat" id="secondflat" class="form-check-input"  type="checkbox"> <label for="secondflat">2. Rufn. inkl. Festnetz-Flatrate 2023: 2,90€ mtl.</label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="mb-3 error-placeholder">
                        <div class="form-check">
                            <input name="staticip" id="staticip" class="form-check-input"  type="checkbox"> <label for="staticip">Feste IP-Adresse: 11,00€ mtl.</label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="mb-3 error-placeholder">
                        <div class="form-check">
                            <input name="postde" id="postde" class="form-check-input"  type="checkbox"> <label for="postde">Rechnung auf Papier: 3,00€ mtl.</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="signature-container">
    <p>Unterschrift Grundstückseigentümer</p>
    <canvas id="signatureCanvasOwner" width="750" height="200"></canvas>
    <button class="clear-button" onclick="clearSignature('signatureCanvasOwner')">Unterschrift löschen</button>
    <p id="datetimeOwner"></p>
</div>

<!-- Signaturen für Auftrag -->
<div class="signature-container">
    <p>Unterschrift Auftrag</p>
    <canvas id="signatureCanvasOrder" width="750" height="200"></canvas>
    <button class="clear-button" onclick="clearSignature('signatureCanvasOrder')">Unterschrift löschen</button>
    <p id="datetimeOrder"></p>
</div>

<!-- Signaturen für Berater -->
<div class="signature-container">
    <p>Unterschrift Berater</p>
    <canvas id="signatureCanvasAdvisor" width="750" height="200"></canvas>
    <button class="clear-button" onclick="clearSignature('signatureCanvasAdvisor')">Unterschrift löschen</button>
    <p id="datetimeAdvisor"></p>
</div>
<input type="hidden" name="owner_signature" id="owner_signature">
<input type="hidden" name="order_signature" id="order_signature">
<input type="hidden" name="advisor_signature" id="advisor_signature">
    <input type="hidden" name="fields[StandardCheckboxStatus]" value="1" id="StandardCheckboxStatus">
    <button type="button" onclick="submitForm()">PDF ausfüllen und herunterladen</button>
</div>
</form>
</div>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
            var checkbox = document.getElementById('standardCheckbox');
            var zusatzFelderContainer = document.getElementById('zusatzFelder');

            checkbox.addEventListener('change', function() {
            zusatzFelderContainer.style.display = checkbox.checked ? 'none' : 'block';
            });

            function updateDateTime() {
                var now = new Date();
                var dateTimeString = now.toLocaleString();
                document.getElementById('datetimeOwner').innerText = dateTimeString;
                document.getElementById('datetimeOrder').innerText = dateTimeString;
                document.getElementById('datetimeAdvisor').innerText = dateTimeString;
            }

            
            setInterval(updateDateTime, 5000);



            var canvas1 = document.getElementById('signatureCanvasOwner');
            var signaturePad1 = new SignaturePad(canvas1);

            var canvas2 = document.getElementById('signatureCanvasOrder');
            var signaturePad2 = new SignaturePad(canvas2);

            var canvas3 = document.getElementById('signatureCanvasAdvisor');
            var signaturePad3 = new SignaturePad(canvas3);

            function clearSignature(canvasId) {
                var canvas = document.getElementById(canvasId);
                    var signaturePad = (canvasId === 'signatureCanvasOwner') ? signaturePad1 : 
                       (canvasId === 'signatureCanvasOrder') ? signaturePad2 : signaturePad3;
                signaturePad.clear();
                event.preventDefault();
            }


        var formData = {};
            function createAdditionalFields() {
                    console.log('createAdditionalFields function called');
                    var dropdown = document.getElementById('anzahlWohneinheiten');
                    var additionalFieldsContainer = document.getElementById('additionalFieldsContainer');

                    additionalFieldsContainer.innerHTML = '';

                    for (var i = 0; i < dropdown.value; i++) {
                        var fieldset = document.createElement('fieldset');
                        var legend = document.createElement('legend');
                        legend.innerText = 'Zusätzliche Wohneinheit ' + (i + 1);
                        fieldset.appendChild(legend);

                        
                        var labelStrasse = document.createElement('label');
                        labelStrasse.innerText = 'Straße:';
                        fieldset.appendChild(labelStrasse);

                        var inputStrasse = document.createElement('input');
                        inputStrasse.type = 'text';
                        inputStrasse.name = 'fields[Strasse_' + (i + 1) + ']';
                        fieldset.appendChild(inputStrasse);

                        
                        var labelHausnummer = document.createElement('label');
                        labelHausnummer.innerText = 'Hausnummer:';
                        fieldset.appendChild(labelHausnummer);

                        var inputHausnummer = document.createElement('input');
                        inputHausnummer.type = 'text';
                        inputHausnummer.name = 'fields[Hausnummer_' + (i + 1) + ']';
                        fieldset.appendChild(inputHausnummer);

                        
                        var labelPLZ = document.createElement('label');
                        labelPLZ.innerText = 'PLZ:';
                        fieldset.appendChild(labelPLZ);

                        var inputPLZ = document.createElement('input');
                        inputPLZ.type = 'text';
                        inputPLZ.name = 'fields[PLZ_' + (i + 1) + ']';
                        fieldset.appendChild(inputPLZ);

                        var labelOrt = document.createElement('label');
                        labelOrt.innerText = 'Ort:';
                        fieldset.appendChild(labelOrt);

                        var inputOrt = document.createElement('input');
                        inputOrt.type = 'text';
                        inputOrt.name = 'fields[Ort_' + (i + 1) + ']';
                        fieldset.appendChild(inputOrt);

                        var labelWe = document.createElement('label');
                        labelWe.innerText = 'Anzahl Wohneinheiten:';
                        fieldset.appendChild(labelWe);

                        var inputWe = document.createElement('input');
                        inputWe.type = 'text';
                        inputWe.name = 'fields[anzahlwe_' + (i + 1) + ']';
                        fieldset.appendChild(inputWe);

                        var labelGk = document.createElement('label');
                        labelGk.innerText = 'Anzahl Geschäftseinheiten:';
                        fieldset.appendChild(labelGk);

                        var inputGk = document.createElement('input');
                        inputGk.type = 'text';
                        inputGk.name = 'fields[anzahlgk_' + (i + 1) + ']';
                        fieldset.appendChild(inputGk);

                        
                        additionalFieldsContainer.appendChild(fieldset);
                    }
                }
    function saveFormData() {
        formData['fields.Anrede_Frau'] = document.getElementById('Frau').checked ? 'X' : '';
        formData['fields.Anrede_Herr'] = document.getElementById('Mann').checked ? 'X' : '';
        formData['fields.Anrede_Divers'] = document.getElementById('Divers').checked ? 'X' : '';
        formData['fields.Eheleute'] = document.getElementById('Eheleute').value;
        formData['fields.Titel'] = document.getElementById('Titel').value;
        formData['fields.Firma_Gemeinschaft'] = document.getElementById('Firma_Gemeinschaft').value;
        formData['fields.Vorname'] = document.getElementById('Vorname').value;
        formData['fields.Nachname'] = document.getElementById('Nachname').value;
        formData['fields.Strasse'] = document.getElementById('Strasse').value;
        formData['fields.Hausnummer'] = document.getElementById('Hausnummer').value;
        formData['fields.PLZ'] = document.getElementById('PLZ').value;
        formData['fields.Ort'] = document.getElementById('Ort').value;
        formData['fields.Telefon_Festnetz'] = document.getElementById('Telefon_Festnetz').value;
        formData['fields.Telefon_mobil'] = document.getElementById('Telefon_mobil').value;
        formData['fields.EMailAdresse'] = document.getElementById('EMailAdresse').value;
        formData['fields.kundennummer'] = document.getElementById('kundennummer').value;
        
        var additionalFieldsContainer = document.getElementById('additionalFieldsContainer');
        var additionalFieldInputs = additionalFieldsContainer.querySelectorAll('input[type="text"]');
    
        additionalFieldInputs.forEach(function (input) {
            var fieldName = input.name;
            var fieldValue = input.value;
            formData[fieldName] = fieldValue;
        });
    }



    function populateFormData() {
        document.getElementById('Frau').checked = formData['fields.Anrede_Frau'] === 'X';
        document.getElementById('Mann').checked = formData['fields.Anrede_Herr'] === 'X';
        document.getElementById('Divers').checked = formData['fields.Anrede_Divers'] === 'X';
        document.getElementById('Eheleute').value = formData['fields.Eheleute'] || '';
        document.getElementById('Titel').value = formData['fields.Titel'] || '';
        document.getElementById('Firma_Gemeinschaft').value = formData['fields.Firma_Gemeinschaft'] || '';
        document.getElementById('Vorname').value = formData['fields.Vorname'] || '';
        document.getElementById('Nachname').value = formData['fields.Nachname'] || '';
        document.getElementById('Strasse').value = formData['fields.Strasse'] || '';
        document.getElementById('Hausnummer').value = formData['fields.Hausnummer'] || '';
        document.getElementById('PLZ').value = formData['fields.PLZ'] || '';
        document.getElementById('Ort').value = formData['fields.Ort'] || '';
        document.getElementById('Telefon_Festnetz').value = formData['fields.Telefon_Festnetz'] || '';
        document.getElementById('Telefon_mobil').value = formData['fields.Telefon_mobil'] || '';
        document.getElementById('EMailAdresse').value = formData['fields.EMailAdresse'] || '';
        document.getElementById('kundennummer').value = formData['fields.kundennummer'] || 'Neukunde';

        var additionalFieldsContainer = document.getElementById('additionalFieldsContainer');
        var additionalFieldInputs = additionalFieldsContainer.querySelectorAll('input[type="text"]');

        additionalFieldInputs.forEach(function (input) {
            var fieldName = input.name;
            var fieldValue = formData[fieldName] || '';
            input.value = fieldValue;
        });

    console.log('Populated Form Data:', formData);
    }

    function submitForm() {

    var canvasOwner = document.getElementById('signatureCanvasOwner');
    var dataURLOwner = canvasOwner.toDataURL(); 
    document.getElementById('owner_signature').value = dataURLOwner;
    var canvasOrder = document.getElementById('signatureCanvasOrder');
    var dataURLOrder = canvasOrder.toDataURL();
    document.getElementById('order_signature').value = dataURLOrder;
    var canvasAdvisor = document.getElementById('signatureCanvasAdvisor');
    var dataURLAdvisor = canvasAdvisor.toDataURL();
    document.getElementById('advisor_signature').value = dataURLAdvisor;


 
    $.ajax({
        type: 'POST',
        url: '{{ route('pdf.fill') }}',
        data: $('#signatureForm').serialize(),
        success: function(response) {
        alert('E-Mail wurde erfolgreich gesendet.');
        window.close(); 
    },
    error: function(error) {
        alert('E-Mail wurde erfolgreich gesendet.');
        window.close();
    }
    });
    var form = document.getElementById('formOnPage2');
    var standardCheckbox = document.getElementById('standardCheckbox');
        var standardCheckboxStatus = standardCheckbox.checked ? '1' : '0';

        document.getElementById('StandardCheckboxStatus').value = standardCheckboxStatus;
    form.submit(); 
}



        function closePdfFormWindow() {
        window.close();
    }


    function validateFormAndShowNextPage(event) {
        document.getElementById('page1').style.display = 'none';
        var requiredFields = ['Vorname', 'Nachname', 'Strasse', 'Hausnummer', 'PLZ', 'Ort', 'EMailAdresse'];

         for (var i = 0; i < requiredFields.length; i++) {
             var fieldId = requiredFields[i];
             var fieldValue = document.getElementById(fieldId).value;

             if (fieldValue.trim() === '') {
                 alert('Bitte füllen Sie alle erforderlichen Felder aus.');
                 return false;
             }
         }

        
        saveFormData();
        populateFormData();


        var form = document.getElementById('formOnPage2');
    for (var key in formData) {
        if (formData.hasOwnProperty(key)) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = formData[key];
            form.appendChild(input);
        }
    }


        document.getElementById('formOnPage2').action = "{{ route('pdf.fill') }}";
        document.getElementById('formOnPage2').method = "post";
        document.getElementById('page2').style.display = 'block';

    }
    </script>
@endsection
