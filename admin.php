<?php

// DB-Verbindung
include 'db.php';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Admin - Palettenregister</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="p-6">
    <nav class="flex justify-between">
        <h1 class="text-2xl font-bold mb-4">Admin - Artikel bearbeiten/löschen</h1>
        <a href="index.html" class="font-bold text-blue-600">Startseite</a>
    </nav>
<input type="text" id="searchInput" placeholder="Artikelnummer oder Beschreibung suchen" class="border p-2 mb-4">
<div id="results"></div>

<div id="editFormContainer" class="hidden border p-4 mb-4">
  <h2 class="font-bold mb-2">Artikel bearbeiten</h2>
  <form id="editForm">
    <input type="hidden" id="editId">
    <div class="mb-2">
      <label>Artikelnummer:</label>
      <input type="text" id="editArtikel" class="border p-1 w-full">
    </div>
    <div class="mb-2">
      <label>Beschreibung:</label>
      <input type="text" id="editBeschreibung" class="border p-1 w-full">
    </div>
    <div class="mb-2">
        <label for="editPalette">Palette:</label>
        <select id="editPalette" name="editPalette" class="border p-1 w-full">
            <option value="">Pal. wählen</option>
            <option value="Normale Euro Pal">Normale Euro Pal</option>
            <option value="1A Euro Pal">1A Euro Pal</option>
            <option value="1A + Normale Euro Pal">1A + Normale Euro Pal</option>
            <option value="Einweg Pal">Einweg Pal</option>
            <option value="Blaue Euro Chep Pal">Blaue Euro Chep Pal</option>
        </select>
    </div>

    <div class="mb-2">
      <label>Kartons pro Palette:</label>
      <input type="number" id="editKPP" class="border p-1 w-full">
    </div>
    <div class="mb-2">
      <label>Packhöhe:</label>
      <input type="number" id="editPackhoehe" class="border p-1 w-full">
    </div>
    <div class="mb-2">
      <label>Kartons pro Lage:</label>
      <input type="number" id="editKartonsLage" class="border p-1 w-full">
    </div>
    <div class="mb-2">
      <label>Karton Art:</label>
      <input type="text" id="editKartonArt" class="border p-1 w-full">
    </div>
    <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded">Speichern</button>
    <button type="button" id="cancelEdit" class="bg-gray-300 px-4 py-1 rounded ml-2">Abbrechen</button>
  </form>
</div>


<script>
async function loadData() {
    const res = await fetch('api.php'); // ruft die gleiche API wie vorher auf
    return await res.json();
}

let data = [];

async function displayResults(query = '') {
    if (!data.length) data = await loadData();
    const resultsDiv = document.getElementById('results');
    resultsDiv.innerHTML = '';

    const filtered = data.filter(item => {
        const artikel = item.artikelNr ? item.artikelNr.toLowerCase() : '';
        const beschreibung = item.beschreibung ? item.beschreibung.toLowerCase() : '';
        return artikel.includes(query.toLowerCase()) || beschreibung.includes(query.toLowerCase());
    });

    filtered.forEach(item => {
        const div = document.createElement('div');
        div.className = "border p-2 pr-60 mb-2 flex justify-between items-center ";
        div.innerHTML = `
            <div>
                <strong>${item.artikelNr}</strong> - ${item.beschreibung} 
            </div>
            <div>
                <button onclick='editEntry(${item.id})' class="bg-yellow-500 text-white px-2 py-1 rounded mr-2">Bearbeiten</button>
                <button onclick='deleteEntry(${item.id})' class="bg-red-600 text-white px-2 py-1 rounded">Löschen</button>
            </div>
        `;
        resultsDiv.appendChild(div);
    });
}

document.getElementById('searchInput').addEventListener('input', e => {
    displayResults(e.target.value);
});

// Bearbeiten
function editEntry(id) {
    const entry = data.find(d => d.id == id);

    // Formular anzeigen
    const container = document.getElementById('editFormContainer');
    container.classList.remove('hidden');

    // Werte setzen
    document.getElementById('editId').value = entry.id;
    document.getElementById('editArtikel').value = entry.artikelNr;
    document.getElementById('editBeschreibung').value = entry.beschreibung;
    document.getElementById('editPalette').value = entry.palette;
    document.getElementById('editKPP').value = entry.kpp;
    document.getElementById('editPackhoehe').value = entry.packhoehe;
    document.getElementById('editKartonsLage').value = entry.kartonsLage;
    document.getElementById('editKartonArt').value = entry.kartonArt;
}


   document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const id = document.getElementById('editId').value;
    const artikel = document.getElementById('editArtikel').value;
    const beschreibung = document.getElementById('editBeschreibung').value;
    const palette = document.getElementById('editPalette').value;
    const kpp = parseInt(document.getElementById('editKPP').value);
    const packhoehe = parseInt(document.getElementById('editPackhoehe').value);
    const kartonsLage = parseInt(document.getElementById('editKartonsLage').value);
    const kartonArt = document.getElementById('editKartonArt').value;

    fetch('update.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({id, artikel, beschreibung, palette, kpp, packhoehe, kartonsLage, kartonArt})
    }).then(() => {
        alert('Eintrag aktualisiert!');
        data = [];
        displayResults(document.getElementById('searchInput').value);
        document.getElementById('editFormContainer').classList.add('hidden');
    });
});

// Abbrechen-Button
document.getElementById('cancelEdit').addEventListener('click', () => {
    document.getElementById('editFormContainer').classList.add('hidden');
});


// Löschen
function deleteEntry(id) {
    console.log("Zu löschende ID:", id); // <<< hier prüfen
    if(confirm("Eintrag wirklich löschen?")) {
        fetch('delete.php', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({id})
        }).then(response => response.json())
          .then(data => {
              console.log(data); // <<< Antwort von PHP prüfen
              if(data.success) {
                  alert("Eintrag gelöscht!");
                  data = [];
                  displayResults(document.getElementById('searchInput').value);
              } else {
                  alert("Fehler: " + data.error);
              }
          });
    }
}

// initial laden
displayResults();
</script>
</body>
</html>
