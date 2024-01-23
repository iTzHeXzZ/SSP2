<!-- resources/views/pdf/ausgefuelltes-formular.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ausgefülltes Formular</title>
</head>
<body>
    <!-- Hier die HTML-Struktur für die ausgefüllten Daten einfügen -->
    <p>Name: {{ $formularData['name'] }}</p>
    <p>Email: {{ $formularData['email'] }}</p>
    <!-- Weitere Felder entsprechend einfügen -->

    <!-- ... -->
</body>
</html>
