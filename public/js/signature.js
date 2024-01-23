// signature.js
var signatures = {
    'owner': null,
    'order': null,
    'advisor': null
};

function clearSignature(canvasId) {
    var signaturePad = getSignaturePad(canvasId);
    signaturePad.clear();
}

function saveSignature(canvasId) {
    var signaturePad = getSignaturePad(canvasId);
    var signatureData = signaturePad.toDataURL();
    signatures[canvasId] = signatureData;
}

function getSignaturePad(canvasId) {
    switch (canvasId) {
        case 'signatureCanvasOwner':
            return signaturePadOwner;
        case 'signatureCanvasOrder':
            return signaturePadOrder;
        case 'signatureCanvasAdvisor':
            return signaturePadAdvisor;
        default:
            return null;
    }
}

function submitFormWithSignatures() {
    // Hier können Sie die gesammelten Unterschriften (signatures) dem Formular hinzufügen
    // und dann das Formular absenden
    saveFormData(); // Stellen Sie sicher, dass diese Funktion Ihre Formulardaten aktualisiert

    // Fügen Sie die Unterschriften als versteckte Felder zum Formular hinzu
    for (var key in signatures) {
        if (signatures.hasOwnProperty(key)) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'signatures[' + key + ']';
            input.value = signatures[key];
            document.getElementById('formOnPage2').appendChild(input);
        }
    }

    // Formular absenden
    document.getElementById('formOnPage2').submit();
}
