@extends('layouts.app')

@section('content') 
<style>
    .bg-purple {
    background-color: #8a2be2;
}
</style>
<div class="row justify-content-md-center">
    <div class="col-sm-2 mb-3 mb-md-0">
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
                            <input value="gf15024m" id="gf15024m" class="form-check-input" name="gfpaket" type="radio"> <label for="gf30">Tarif wählen</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-2 mb-3 mb-md-0">
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
                            <input value="gf15012m" id="gf15012m" class="form-check-input" name="gfpaket" type="radio"> <label for="gf100">Tarif wählen</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-2 mb-3 mb-md-0">
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
                            <input value="gf300" id="gf300" class="form-check-input" name="gfpaket" type="radio"> <label for="gf300">Tarif wählen</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-2 mb-3 mb-md-0">
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
                            <input value="gf600" id="gf600" class="form-check-input" name="gfpaket" type="radio"> <label for="gf500">Tarif wählen</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-2 mb-3 mb-md-0">
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
                            <input value="gf1000" id="gf1000" class="form-check-input" name="gfpaket" type="radio"> <label for="gf1000">Tarif wählen</label>
                        </div>
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
                            <input value="waipustick" id="waipustick" class="form-check-input" type="checkbox" > <label for="waipustick">Waipu.tv 4K Stick 59,99€</label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="mb-3 error-placeholder">
                        <div class="form-check">
                            <input value="eigenesgeraet" id="eigenesgeraet" class="form-check-input" type="checkbox"> <label for="eigenesgeraet">Eigenes Endgerät</label>
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
                            <input value="cabletv" id="cabletv" class="form-check-input" type="checkbox"> <label for="gfboxonetime">Kabelfernsehen: 10,00€ mtl.</label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="mb-3 error-placeholder">
                        <div class="form-check">
                            <input value="waipucomfort" id="waipucomfort" class="form-check-input"  type="checkbox"> <label for="gfboxrent">Waipu.tv Comfort: 7,49€ mtl.</label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="mb-3 error-placeholder">
                        <div class="form-check">
                            <input value="waipuplus" id="waipuplus" class="form-check-input"  type="checkbox"> <label for="gfboxrent">Waipu.tv Perfect Plus: 12,99€ mtl.</label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="mb-3 error-placeholder">
                        <div class="form-check">
                            <input value="1stflat" id="1stflat" class="form-check-input"  type="checkbox"> <label for="gfboxrent">1. Rufn. inkl. Festnetz-Flatrate 2023: 5,90€ mtl.</label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="mb-3 error-placeholder">
                        <div class="form-check">
                            <input value="2stflat" id="2stflat" class="form-check-input"  type="checkbox"> <label for="gfboxrent">2. Rufn. inkl. Festnetz-Flatrate 2023: 2,90€ mtl.</label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="mb-3 error-placeholder">
                        <div class="form-check">
                            <input value="staticip" id="staticip" class="form-check-input"  type="checkbox"> <label for="gfboxrent">Feste IP-Adresse: 11,00€ mtl.</label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="mb-3 error-placeholder">
                        <div class="form-check">
                            <input value="postde" id="postde" class="form-check-input"  type="checkbox"> <label for="gfboxrent">Rechnung auf Papier: 3,00€ mtl.</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection