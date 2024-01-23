<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    public function showForm()
    {
        // Lade die gewünschte PDF
        $pdfPath = storage_path('app/gnvlangenfeld.pdf');

        // Extrahiere die ausfüllbaren Felder
        $fillableFields = $this->extractFillableFields($pdfPath);

        return view('pdf.form', compact('fillableFields'));
    }

    public function processPdf(Request $request)
    {
        // Validierung des Formulars (kannst du nach Bedarf anpassen)

        // Lade die ausgewählte PDF
        $pdfPath = storage_path('app/gnvlangenfeld.pdf');


        // Kopiere die PDF als Basis für die Bearbeitung
        $editedPdfPath = storage_path('app/public/edited_pdf.pdf');
        copy($pdfPath, $editedPdfPath);

        // Fülle die ausfüllbaren Felder mit den Formulardaten
        $fillableFields = $this->extractFillableFields($pdfPath);
        $this->fillPdfFields($editedPdfPath, $fillableFields, $request->all());

        // Weiterleitung oder Anzeige einer Bestätigung
        return redirect()->back()->with('success', 'PDF erfolgreich bearbeitet und gespeichert.');
    }

    private function extractFillableFields($pdfPath)
    {
        $pdf = PDF::loadFile($pdfPath);
        $fields = $pdf->getCanvas()->getForm($pdf)->getFields();

        $fillableFields = [];

        foreach ($fields as $fieldName => $field) {
            $fillableFields[] = $fieldName;
        }

        return $fillableFields;
    }

    private function fillPdfFields($pdfPath, $fillableFields, $formData)
    {
        $pdf = PDF::loadFile($pdfPath);

        foreach ($fillableFields as $fieldName) {
            if (isset($formData[$fieldName])) {
                $pdf->getCanvas()->getForm($pdf)->getFields()->get($fieldName)->setValue($formData[$fieldName]);
            }
        }

        $pdf->save($pdfPath);
    }
}
