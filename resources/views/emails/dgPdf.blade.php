<!DOCTYPE html>
<html>
<head>
    <title>Vertragszusammenfassung</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        h1 {
            color: #0056b3;
        }
        p {
            margin-bottom: 20px;
        }
        .signature {
            margin-top: 30px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <h1>Guten Tag, {{$name}} </h1>
    <p>anbei erhalten Sie die Vertragszusammenfassung für den Auftrag, den Sie erstellt haben. Die detaillierte PDF-Dokumentation finden Sie im Anhang dieser E-Mail.</p>
    <p>Falls Sie weitere Fragen haben oder Unterstützung benötigen, stehen wir Ihnen gerne zur Verfügung.</p>
    <p>Mit freundlichen Grüßen,<br>{{ $user}}</p>
</body>
</html>
