@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<script src="
https://cdn.jsdelivr.net/npm/alpinejs@3.13.8/dist/cdn.min.js
" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
<style>
            .bg-green {
    background-color: #00f673;
}
</style>

@section('content')
<div class="min-h-screen bg-gray-100 flex justify-center items-center">
    <div class="max-w-5xl w-full mx-auto mt-8">
        <div class="bg-white shadow-md rounded-lg p-5" x-data="{
            schritt: 1,
            vorname: '',
            nachname: '',
            strasse: '',
            hausnummer: '',
            plz: '',
            firma: '',
            ort: '',
            iban: '',
            bank: '',
            bic: '',
            anrede: '', 
            titel: '',
            geburtstag: '',
            handynummer: '',
            telefonnummer: '',
            email: '',
            kennzahl: '',
            festnetzOption: 'analog', 
            hardwareOption: 'no_router', 
            lieferdatumTyp: 'schnellstmöglich',
            lieferdatum: '',
            anbieter: '',
            we: '1',
            rufnummern: [],
            rufnummer_1: '',
            rufnummer_2: '',
            rufnummer_3: '',
            rufnummer_4: '',
            rufnummer_5: '',
            rufnummer_6: '',
            rufnummer_7: '',
            rufnummer_8: '',
            rufnummer_9: '',
            rufnummer_10: '',
            gfpaket: 'gf250',
            init() {
                console.log(`Initialer Schritt: ${this.schritt}`);
                this.$watch('schritt', (value) => console.log(`Schritt geändert zu: ${value}`));
                this.$watch('selectedProduct', (newValue) => {
                    console.log(`Das ausgewählte Produkt hat sich geändert: ${newValue}`);
                    this.updateProductDetails();
                });
            },
            hardwareOptions: {
                'o2homebox': { name: 'AVM FRITZ!Box 7530AX o2', monthlyFee: 4.99, shippingCost: 9.99 },
                'fritzbox': { name: 'AVM FRITZ!Box 7590AX o2', monthlyFee: 6.99, shippingCost: 9.99 },
                'no_router': { name: 'Kein Router benötigt', monthlyFee: 0.00, shippingCost: 0.00 }
            },
            festnetzOptions: {
                'analog': { name: 'Analog Option', description: '1 Leitung mit 1 Rufnummer', price: 0.00 },
                'isdn': { name: 'ISDN Komfort', description: '2 Telefonleitungen und bis zu 10 Rufnummern', price: 2.99 }
            },
            products: {
                'gf100': { name: 'o2 Home M 100/40', price: 29.99, regularPrice: 39.99, connectionFee: 0 },
                'gf250': { name: 'o2 Home L 250/125', price: 19.99, regularPrice: 44.99, connectionFee: 0 },
                'gf500': { name: 'o2 Home XL 500/250', price: 19.99, regularPrice: 59.99, connectionFee: 0 },
                'gf1000': { name: 'o2 Home XXL 1000/500', price: 69.99, regularPrice: 69.99, connectionFee: 0 }
            },
            calculateMonthlyCostsFirstYear() {
                return (this.products[this.gfpaket].price +
                        this.hardwareOptions[this.hardwareOption].monthlyFee +
                        this.festnetzOptions[this.festnetzOption].price).toFixed(2);
            },
            calculateMonthlyCostsSecondYear() {
                return (this.products[this.gfpaket].regularPrice +
                        this.hardwareOptions[this.hardwareOption].monthlyFee +
                        this.festnetzOptions[this.festnetzOption].price).toFixed(2);
            },
            calculateOneTimeCosts() {
                return (this.products[this.gfpaket].connectionFee +
                        this.hardwareOptions[this.hardwareOption].shippingCost).toFixed(2);
            },
            selectedProduct: 'gf250',
            sendDataToController() {
                const signatureCanvas = document.getElementById('signatureCanvas');
                const signatureDataUrl = signatureCanvas.toDataURL();
                const data = {
                    vorname: this.vorname,
                    nachname: this.nachname,
                    strasse: this.strasse,
                    hausnummer: this.hausnummer,
                    plz: this.plz,
                    ort: this.ort,
                    iban: this.iban,
                    bank: this.bank,
                    bic: this.bic,
                    firma: this.firma,
                    kennzahl: this.kennzahl,
                    we: this.we,
                    unterschrift: signatureDataUrl,
                    anrede: this.anrede, 
                    titel: this.titel,
                    geburtstag: this.geburtstag,
                    handynummer: this.handynummer,
                    telefonnummer: this.telefonnummer,
                    email: this.email,
                    festnetzOption: this.festnetzOption, 
                    hardwareOption: this.hardwareOption, 
                    lieferdatumTyp: this.lieferdatumTyp,
                    lieferdatum: this.lieferdatum,
                    anbieter: this.anbieter,
                    rufnummer_1: this.rufnummer_1,
                    rufnummer_2: this.rufnummer_2,
                    rufnummer_3: this.rufnummer_3,
                    rufnummer_4: this.rufnummer_4,
                    rufnummer_5: this.rufnummer_5,
                    rufnummer_6: this.rufnummer_6,
                    rufnummer_7: this.rufnummer_7,
                    rufnummer_8: this.rufnummer_8,
                    rufnummer_9: this.rufnummer_9,
                    rufnummer_10: this.rufnummer_10,
                    gfpaket: this.gfpaket,
                };

                fetch('{{ route('pdf.fillugg') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/pdf',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')   
                    },
                    body: JSON.stringify(data),
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Netzwerkantwort war nicht ok , Status: ${response.status}');
                    }
                    return response.blob();
                })
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'filled_ugg.pdf';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    alert('Auftrag erstellt, das Fenster Schließt automatisch');
                    setTimeout(() => {
                        window.close();  // Versucht das Fenster zu schließen
                    }, 1000);
                
                })
                .catch((error) => {
                    console.error('Fehler beim Senden der Daten:', error);
                });
            }
        }">
        <div class="border-b-2 border-gray-200 mb-4">
            <nav class="flex space-x-4 border-b">
                <a href="#1" @click="schritt = 1" :class="{'text-blue-600 border-blue-600': schritt === 1}" class="pb-2" style="text-decoration : none">Produktauswahl</a>
                <a href="#2" @click="schritt = 2" :class="{'text-blue-600 border-blue-600': schritt === 2}" class="pb-2" style="text-decoration : none">Kontaktdaten</a>
                <a href="#3" @click="schritt = 3" :class="{'text-blue-600 border-blue-600': schritt === 3}" class="pb-2" style="text-decoration : none">Anbieterwechsel</a>
                <a href="#4" @click="schritt = 4" :class="{'text-blue-600 border-blue-600': schritt === 4}" class="pb-2" style="text-decoration : none">Kontoinformationen</a>
                <a href="#5" @click="schritt = 5" :class="{'text-blue-600 border-blue-600': schritt === 5}" class="pb-2" style="text-decoration : none">Unterschrift</a>
            </nav>
        </div>
        <div x-show="schritt === 1">
            <h2 class="text-lg font-semibold mb-4">Produktauswahl</h2>
            <div class="row justify-content-md-center">
                <div class="col-md-4 mb-3">
                    <div class="card text-start h-100 border">
                        <div class="card-body">
                            <div class="mb-4">
                                <h3>o2 Home M 100/40</h3>
                            </div>
                            <hr>
                            <div class="bg-light">
                                <ul class="list-unstyled">
                                    <li class="p-1">Download mit 100Mbit/s</li>
                                    <li class="p-1">Upload mit 40 Mbit/s</li>
                                </ul>
                            </div>
                            <div class="">
                                <ul class="list-unstyled">
                                    <li class="p-1">Laufzeit: 24Monate</li>
                                    <li class="p-1">Aktionspreis 1-6Monat: 29,99€ </li>
                                    <li class="p-1">Danach 39,99€ (o2 Kunden -5€)</li>
                                    <strong><li class="p-2">Anschlussgebühr 0€!</li></strong>
                                </ul>
                            </div>
                            <hr>
                            <div class="bg-green bg-gradient p-1 text-white">
                                <div class="error-placeholder">
                                    <div class="form-check">
                                        <input x-model="gfpaket" value="gf100" id="gf100" class="form-check-input" name="gfpaket" type="radio" > <label>Tarif wählen</label>
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
                                <h3>o2 Home L 250/125</h3>
                            </div>
                            <hr>
                            <div class="bg-light">
                                <ul class="list-unstyled">
                                    <li class="p-1">Download mit 250 Mbit/s</li>
                                    <li class="p-1">Upload mit 125 Mbit/s</li>
                                </ul>
                            </div>
                            <div class="">
                                <ul class="list-unstyled">
                                    <li class="p-1">Laufzeit: 24Monate</li>
                                    <li class="p-1">Aktionspreis 1-6Monat: 19,99€ </li>
                                    <li class="p-1">Danach 44,99€ (o2 Kunden -5€)</li>
                                    <strong><li class="p-2">Anschlussgebühr 0€!</li></strong>
                                </ul>
                            </div>
                            <hr>
                            <div class="bg-green bg-gradient p-1 text-white">
                                <div class="error-placeholder">
                                    <div class="form-check">
                                        <input x-model="gfpaket"  value="gf250" id="gf250" class="form-check-input" name="gfpaket" type="radio" checked> <label>Tarif wählen</label>
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
                                <h3>o2 Home XL 500/250</h3>
                            </div>
                            <hr>
                            <div class="bg-light">
                                <ul class="list-unstyled">
                                    <li class="p-1">Download mit 500 Mbit/s</li>
                                    <li class="p-1">Upload mit 250 Mbit/s</li>
                                </ul>
                            </div>
                            <div class="">
                                <ul class="list-unstyled">
                                    <li class="p-1">Laufzeit: 24Monate</li>
                                    <li class="p-1">Aktionspreis 1-6Monat: 19,99€ </li>
                                    <li class="p-1">Danach 59,99€ (o2 Kunden -5€)</li>
                                    <strong><li class="p-2">Anschlussgebühr 0€!</li></strong>
                                </ul>
                            </div>
                            <hr>
                            <div class="bg-green bg-gradient p-1 text-white">
                                <div class="error-placeholder">
                                    <div class="form-check">
                                        <input x-model="gfpaket" value="gf500" id="gf500" class="form-check-input" name="gfpaket" type="radio"> <label>Tarif wählen</label>
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
                                <h3>o2 Home XXL 1000/500</h3>
                            </div>
                            <hr>
                            <div class="bg-light">
                                <ul class="list-unstyled">
                                    <li class="p-1">Download mit 1 Gbit/s</li>
                                    <li class="p-1">Upload mit 500 Mbit/s</li>
                                </ul>
                            </div>
                            <div class="">
                                <ul class="list-unstyled">
                                    <li class="p-1">Laufzeit: 24Monate</li>
                                    <li class="p-1">Dauerhaft 69,99€ (o2 Kunden -5€)</li>
                                    <strong><li class="p-2">Anschlussgebühr 0€!</li></strong>
                                </ul>
                            </div>
                            <hr>
                            <div class="error-placeholder">
                                <div class="bg-green bg-gradient p-1 text-white">
                                    <div class="form-check">
                                        <input x-model="gfpaket" value="gf1000" id="gf1000" class="form-check-input" name="gfpaket" type="radio"> <label>Tarif wählen</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card text-start h-100 border">
                    <div class="card-body">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold mb-4">Festnetz Optionen</h3>
                            <div class="flex flex-col gap-4">
                                <div class="flex items-center">
                                    <input x-model="festnetzOption" value="analog" id="analog" class="form-radio h-6 w-6 text-blue-500 radio-custom" name="festnetz" type="radio">
                                    <label for="analog" class="ml-2 text-base">Analog Option - 1 Leitung mit 1 Rufnummer - 0,00 €</label>
                                </div>
                                <div class="flex items-center">
                                    <input x-model="festnetzOption" value="isdn" id="isdn" class="form-radio h-6 w-6 text-blue-500 radio-custom" name="festnetz" type="radio">
                                    <label for="isdn" class="ml-2 text-base">ISDN Komfort - 2 Telefonleitungen und bis zu 10 Rufnummern - 2,99 €</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card text-start h-100 border">
                    <div class="card-body">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold mb-4">Hardware</h3>
                            <div class="flex flex-col gap-4">
                                <div class="flex items-center">
                                    <input x-model="hardwareOption" value="o2homebox" id="o2homebox" class="form-radio h-6 w-6 text-blue-500 radio-custom" name="hardware" type="radio">
                                    <label for="o2homebox" class="ml-2 text-base">AVM FRITZ!Box 7530AX o2 - monatlich 4,99 € - Versandkosten 9,99 €</label>
                                </div>
                                <div class="flex items-center">
                                    <input x-model="hardwareOption" value="fritzbox" id="fritzbox" class="form-radio h-6 w-6 text-blue-500 radio-custom" name="hardware" type="radio">
                                    <label for="fritzbox" class="ml-2 text-base">AVM FRITZ!Box 7590AX o2 - monatlich 6,99 € - Versandkosten 9,99 €</label>
                                </div>
                                <div class="flex items-center">
                                    <input x-model="hardwareOption" value="no_router" id="no_router" class="form-radio h-6 w-6 text-blue-500 radio-custom" name="hardware" type="radio">
                                    <label for="no_router" class="ml-2 text-base">Ich benötige keinen Router - 0,00 €</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div class="flex justify-end">
                <button class="py-2 px-4 bg-blue-500 text-white rounded hover:bg-blue-700" @click="schritt = 2">Weiter</button>
            </div>
    </div>
        
            <div x-show="schritt === 2" class="space-y-4">
                <h2 class="text-xl font-semibold text-gray-900">Kontaktdaten</h2>
            
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="anrede" class="block text-sm font-medium text-gray-700">Anrede:</label>
                        <select x-model="anrede" name="anrede" id="anrede" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Bitte wählen...</option>
                            <option value="Frau">Frau</option>
                            <option value="Herr">Herr</option>
                            <option value="Divers">Divers</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="titel" class="block text-sm font-medium text-gray-700">Titel:</label>
                        <select x-model="titel" name="titel" id="titel" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Bitte wählen...</option>
                            <option value="Dr.">Dr.</option>
                            <option value="Prof.">Prof.</option>
                            <option value="Prof. Dr.">Prof. Dr.</option>
                        </select>
                    </div>
                </div>
            
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="vorname" class="block text-sm font-medium text-gray-700">Vorname:</label>
                        <input type="text" x-model="vorname" name="vorname" id="vorname" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"  placeholder="Max">
                    </div>
            
                    <div>
                        <label for="nachname" class="block text-sm font-medium text-gray-700">Nachname:</label>
                        <input type="text" x-model="nachname" name="nachname" id="nachname" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Mustermann">
                    </div>
                </div>
            
                <div>
                    <label for="geburtstag" class="block text-sm font-medium text-gray-700">Geburtsdatum:</label>
                    <input type="date" x-model="geburtstag" name="geburtstag" id="geburtstag" required class="mt-1 block border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="handynummer" class="block text-sm font-medium text-gray-700">Handynummer:</label>
                        <input type="tel" x-model="handynummer" name="handynummer" id="handynummer" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="0151....">                    
                        <div id="handynummer-feedback" class="text-red-500 text-sm mt-1" style="display: none;">Bitte geben Sie eine gültige Handynummer ein.</div>
                    </div>
            
                    <div>
                        <label for="telefonnummer" class="block text-sm font-medium text-gray-700">Telefonnummer:</label>
                        <input type="tel" x-model="telefonnummer" name="telefonnummer" id="telefonnummer" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="01234...">            
                        <div id="telefonnummer-feedback" class="text-red-500 text-sm mt-1 min-h-[20px]" style="display: none">Bitte korrekte Nummer eingeben.</div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">E-Mail:</label>
                        <input type="email" x-model="email" name="email" id="email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="beispiel@domain.de">
                        <div id="email-feedback" class="text-red-500 text-sm mt-1" style="display: none;">Bitte geben Sie eine gültige E-Mail-Adresse ein.</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="strasse" class="block text-sm font-medium text-gray-700">Straße:</label>
                        <input type="text" x-model="strasse" name="strasse" id="strasse" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Musterstraße">
                    </div>
            
                    <div>
                        <label for="hausnummer" class="block text-sm font-medium text-gray-700">Hausnummer:</label>
                        <input type="text" x-model="hausnummer" name="hausnummer" id="hausnummer" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="12 A">
                    </div>
                </div>
            
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="plz" class="block text-sm font-medium text-gray-700">PLZ:</label>
                        <input type="text" x-model="plz" name="plz" id="plz" pattern="^[0-9]{5}$" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="12345">
                    </div>
            
                    <div class="col-span-2">
                        <label for="ort" class="block text-sm font-medium text-gray-700">Ort:</label>
                        <input type="text" x-model="ort" name="ort" id="ort" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Musterstadt">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="kennzahl" class="block text-sm font-medium text-gray-700">Sicherheitskennzahl:</label>
                        <input type="text" x-model="kennzahl" name="kennzahl" id="kennzahl" pattern="^[0-9]{4}$" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="XXXX">
                    </div>
                    <div>
                        <label for="we" class="block text-sm font-medium text-gray-700">Anzahl Wohneinheiten:</label>
                        <input type="text" x-model="we" name="we" id="we" pattern="^[0-9]{2}$" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="1-X">
                    </div>
                </div>
                
            
                <div class="flex justify-between">
                    <button class="py-2 px-4 bg-gray-500 text-white rounded hover:bg-gray-600" @click="schritt = 1">Zurück</button>
                    <button class="py-2 px-4 bg-blue-500 text-white rounded hover:bg-blue-600" @click="schritt = 3">Weiter</button>
                </div>
            </div>
            
            <div x-show="schritt === 3" class="space-y-4">
                <h2 class="text-lg font-semibold mb-4">Anbieterwechsel</h2>

                <fieldset class="mt-4">
                    <legend class="text-sm font-medium text-gray-700">Gewünschtes Lieferdatum:</legend>
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center mb-4">
                            <input type="radio" x-model="lieferdatumTyp" value="schnellstmöglich" checked class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                            <label class="ml-3 block text-sm font-medium text-gray-700">Schnellstmöglich</label>
                        </div>
                        <div class="flex items-center mb-4">
                            <input type="radio" x-model="lieferdatumTyp" value="waehlen" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                            <label class="ml-3 block text-sm font-medium text-gray-700">
                                Datum wählen:
                                <input type="date" x-model="lieferdatum" x-bind:disabled="lieferdatumTyp !== 'waehlen'" x-on:change="lieferdatumTyp = lieferdatum ? '' : 'waehlen'" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </label>
                        </div>
                    </div>
                </fieldset>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="anbieter" class="block text-sm font-medium text-gray-700">Anbieter:</label>
                    <input type="text" x-model="anbieter" name="anbieter" id="anbieter" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Anbietername">
                </div>
            
                <div>
                    <label for="rufnummern" class="block text-sm font-medium text-gray-700">Anzahl Rufnummern:</label>
                    <select name="rufnummern" id="rufnummern" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @for ($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div id="rufnummernFelderContainer" class="grid grid-cols-1 md:grid-cols-3 gap-4"></div>
            
            <div class="mt-8 mb-10">
                <h3 class="text-lg font-semibold mb-4">Vertragsinhaber</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="vorname" class="block text-sm font-medium text-gray-700">Vorname:</label>
                        <input type="text" x-model="vorname" id="vorname" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="nachname" class="block text-sm font-medium text-gray-700">Nachname:</label>
                        <input type="text" x-model="nachname" id="nachname" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="firma" class="block text-sm font-medium text-gray-700">Firma:</label>
                        <input type="text" x-model="firma" id="firma" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>
            
                <div class="flex justify-between">
                    <button class="py-2 px-4 bg-gray-500 text-white rounded hover:bg-blue-700" @click="schritt = 2">Zurück</button>
                    <button class="py-2 px-4 bg-blue-500 text-white rounded hover:bg-blue-700" @click="schritt = 4">Weiter</button>
                </div>
            </div>

            <div x-show="schritt === 4" class="space-y-4">
                <h2 class="text-lg font-semibold mb-4">Kontoinformationen</h2>
                <div class="mt-8 mb-10">
                    <h3 class="text-lg font-semibold mb-4">Kontoinhaber</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="vorname" class="block text-sm font-medium text-gray-700">Vorname:</label>
                            <input type="text" x-model="vorname" id="vorname" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="nachname" class="block text-sm font-medium text-gray-700">Nachname:</label>
                            <input type="text" x-model="nachname" id="nachname" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="strasse" class="block text-sm font-medium text-gray-700">Straße:</label>
                            <input type="text" x-model="strasse" id="strasse" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="hausnummer" class="block text-sm font-medium text-gray-700">Hausnummer:</label>
                            <input type="text" x-model="hausnummer" id="hausnummer" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="plz" class="block text-sm font-medium text-gray-700">PLZ:</label>
                            <input type="text" x-model="plz" id="plz" pattern="^[0-9]{5}$" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="ort" class="block text-sm font-medium text-gray-700">Ort:</label>
                            <input type="text" x-model="ort" id="ort" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
                <h3 class="text-lg font-semibold mt-15">Bankverbindung</h3>
                <div class="mt-20">
                    <div>
                        <label for="iban" class="block text-sm font-medium text-gray-700">IBAN:</label>
                        <input type="text" x-model="iban" id="iban" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="bank" class="block text-sm font-medium text-gray-700">Bankinstitut:</label>
                        <input type="text" x-model="bank" id="bank" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="bic" class="block text-sm font-medium text-gray-700">BIC:</label>
                        <input type="text" x-model="bic" id="bic" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="flex justify-between">
                    <button class="py-2 px-4 bg-gray-500 text-white rounded hover:bg-blue-700" @click="schritt = 3">Zurück</button>
                    <button class="py-2 px-4 bg-blue-500 text-white rounded hover:bg-blue-700" @click="schritt = 5">Weiter</button>
                </div>
            </div>
            

            <div x-show="schritt === 5">
                <div class="mb-4">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Ausgewähltes Produkt -->
                        <div class="bg-gray-100 p-4 rounded shadow">
                            <h3 class="text-lg font-semibold">Ausgewähltes Produkt</h3>
                            <p><strong>Produkt:</strong> <span x-text="products[gfpaket].name"></span></p>
                            <p><strong>Aktionspreis (Monate 1-12):</strong> <span x-text="products[gfpaket].price + ' €'"></span></p>
                            <p><strong>Regulärer Preis (danach):</strong> <span x-text="products[gfpaket].regularPrice + ' €'"></span></p>
                            <p><strong>Anschlussgebühr:</strong> <span x-text="products[gfpaket].connectionFee + ' €'"></span></p>
                        </div>
                
                        <!-- Ausgewählte Hardware -->
                        <div class="bg-gray-100 p-4 rounded shadow">
                            <h3 class="text-lg font-semibold">Ausgewählte Hardware</h3>
                            <p><strong>Hardware:</strong> <span x-text="hardwareOptions[hardwareOption].name"></span></p>
                            <p><strong>Monatliche Gebühr:</strong> <span x-text="hardwareOptions[hardwareOption].monthlyFee + ' €'"></span></p>
                            <p><strong>Versandkosten:</strong> <span x-text="hardwareOptions[hardwareOption].shippingCost + ' €'"></span></p>
                        </div>
                
                        <!-- Gewählte Festnetzoption -->
                        <div class="bg-gray-100 p-4 rounded shadow">
                            <h3 class="text-lg font-semibold">Gewählte Festnetzoption</h3>
                            <p><strong>Option:</strong> <span x-text="festnetzOptions[festnetzOption].name"></span></p>
                            <p><strong>Details:</strong> <span x-text="festnetzOptions[festnetzOption].description"></span></p>
                            <p><strong>Preis:</strong> <span x-text="festnetzOptions[festnetzOption].price + ' €'"></span></p>
                        </div>
                    </div>
                    <h2 class="text-lg font-semibold mb-2 mt-6">Kostenübersicht :</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-100 p-4 rounded shadow">
                            <h3 class="text-lg font-semibold">Monatliche Kosten <br>(Monate 1-6)</h3>
                            <p><span x-text="calculateMonthlyCostsFirstYear() + ' €'"></span> pro Monat</p>
                        </div>

                        <div class="bg-gray-100 p-4 rounded shadow">
                            <h3 class="text-lg font-semibold">Monatliche Kosten <br>(Ab dem 7. Monat)</h3>
                            <p><span x-text="calculateMonthlyCostsSecondYear() + ' €'"></span> pro Monat</p>
                        </div>

                        <div class="bg-gray-100 p-4 rounded shadow">
                            <h3 class="text-lg font-semibold">Einmalige Kosten</h3>
                            <p><span x-text="calculateOneTimeCosts() + ' €'"></span> gesamt</p>
                        </div>
                    </div>
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold">Kontaktinformationen</h3>
                            <div class="bg-gray-100 p-4 rounded shadow">
                                <p><strong>Vorname:</strong> <span x-text="vorname"></span></p>
                                <p><strong>Nachname:</strong> <span x-text="nachname"></span></p>
                                <p><strong>Adresse:</strong> <span x-text="strasse + ' ' + hausnummer + ', ' + plz + ' ' + ort"></span></p>
                                <p><strong>E-Mail:</strong> <span x-text="email"></span></p>
                                <p><strong>Telefonnummer:</strong> <span x-text="telefonnummer"></span></p>
                            </div>
                        </div>
                </div>
                <div class="mb-4 mt-6 row">
                    <p>Unterschrift des Kunden für die Beauftragung des Glasfaser Produkts</p>
                </div>
                <div class="flex flex-col justify-center items-center w-full" style="height: 300px;">
                    <div class="w-3/4 border border-gray-300" style="height: 150px;">
                        <canvas id="signatureCanvas" width="699" height="150"></canvas>
                    </div>
                    <div class="text-center">
                    <button class="py-2 px-4 bg-gray-500 text-white rounded hover:bg-blue-700 mt-2" onclick="clearSignature()">Unterschrift löschen</button>
                    </div>
                </div>
                            
            
                <!-- Schaltflächen für Navigation -->
                <div class="flex justify-between">
                    <button class="py-2 px-4 bg-gray-500 text-white rounded hover:bg-blue-700" @click="schritt = 4">Zurück</button>
                    <button class="py-2 px-4 bg-blue-500 text-white rounded hover:bg-blue-700" @click="sendDataToController">Absenden</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    
    function createRufnummernFelder(anzahl) {
        const container = document.getElementById('rufnummernFelderContainer');
        container.innerHTML = ''; 

        for (let i = 1; i <= anzahl; i++) {
            const input = document.createElement('input');
            input.setAttribute('type', 'text');
            input.setAttribute('x-model', `rufnummer_${i}`);
            input.setAttribute('name', `rufnummer_${i}`);
            input.setAttribute('id', `rufnummer_${i}`);
            input.setAttribute('placeholder', `Rufnummer ${i}`);
            input.classList.add('mt-1', 'block', 'w-full', 'border-gray-300', 'rounded-md', 'shadow-sm', 'focus:ring-blue-500', 'focus:border-blue-500');

            container.appendChild(input);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        createRufnummernFelder(1);

        document.getElementById('rufnummern').addEventListener('change', function() {
            const anzahl = parseInt(this.value, 10) || 1;
            createRufnummernFelder(anzahl);
        });
    });



    document.addEventListener('DOMContentLoaded', function () {
    const waehlenRadio = document.getElementById('lieferdatum_waehlen');
    const datumInput = document.getElementById('lieferdatum');
    const schnellRadio = document.getElementById('lieferdatum_schnellstmoeglich');

    
    waehlenRadio.addEventListener('change', function() {
        if (this.checked) {
            datumInput.disabled = false;
        }
    });

   
    schnellRadio.addEventListener('change', function() {
        if (this.checked) {
            datumInput.disabled = true;
            datumInput.value = ''; 
            waehlenRadio.checked = false; 
        }
    });

    
    datumInput.addEventListener('change', function() {
        if (this.value) {
            schnellRadio.checked = false; 
        }
    });
});


    document.getElementById('telefonnummer').addEventListener('input', function(e) {
        const value = e.target.value;
        const feedbackElement = document.getElementById('telefonnummer-feedback');
        const pattern = /^0[0-9]{4,15}$/;
    
        if (pattern.test(value)) {
            feedbackElement.style.display = 'none';
            e.target.classList.remove('border-red-500');
            e.target.classList.add('border-green-500');
        } else {
            feedbackElement.style.display = 'block';
            feedbackElement.textContent = 'Bitte prüfen!.';
            e.target.classList.remove('border-green-500');
            e.target.classList.add('border-red-500');
        }
    });

        document.getElementById('email').addEventListener('input', function(e) {
        const feedbackElement = document.getElementById('email-feedback');
        if (e.target.validity.typeMismatch) {
            feedbackElement.style.display = 'block';
            feedbackElement.textContent = 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
            e.target.classList.add('border-red-500');
            e.target.classList.remove('border-green-500');
        } else {
            feedbackElement.style.display = 'none';
            e.target.classList.remove('border-red-500');
            e.target.classList.add('border-green-500');
        }
    });

    document.getElementById('handynummer').addEventListener('input', function(e) {
        const value = e.target.value;
        const feedbackElement = document.getElementById('handynummer-feedback');
        const pattern = /^0[0-9]{10,11}$/;

        if (pattern.test(value)) {
            feedbackElement.style.display = 'none';
            e.target.classList.remove('border-red-500');
            e.target.classList.add('border-green-500');
        } else {
            feedbackElement.style.display = 'block';
            feedbackElement.textContent = 'Bitte geben Sie eine gültige Handynummer ein.';
            e.target.classList.add('border-red-500');
            e.target.classList.remove('border-green-500');
        }
    });

    window.addEventListener('DOMContentLoaded', () => {
        const canvas = document.getElementById('signatureCanvas');
        const signaturePad = new SignaturePad(canvas);
        signaturePad.penColor = 'black';

    });

    function clearSignature() {
        var canvas = document.getElementById('signatureCanvas');
        var ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    function saveSignature() {
        var canvas = document.getElementById('signatureCanvas');
        var dataURL = canvas.toDataURL();

    }

</script>
@endsection
