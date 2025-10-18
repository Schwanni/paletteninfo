async function search() {
  const query = document.getElementById("searchField").value;
  const response = await fetch(`/search?query=${encodeURIComponent(query)}`);
  const results = await response.json();
  const resultsDiv = document.getElementById("results");
  resultsDiv.innerHTML = "";
  results.forEach((item) => {
    const div = document.createElement("div");
    div.innerHTML = `<a href="#" onclick="showDetails(${item.id})">${item.artikelNr} - ${item.beschreibung}</a>`;
    resultsDiv.appendChild(div);
  });
}

async function showDetails(id) {
  const res = await fetch(`/details/${id}`);
  const entry = await res.json();
  alert(JSON.stringify(entry));
}

async function showDetails(id) {
  const res = await fetch(`/details/${id}`);
  const entry = await res.json();

  // Setze die Modal-Inhalte
  document.getElementById(
    "modalTitle"
  ).innerText = `Details für Artikel Nr. ${entry.artikelNr}`;
  document.getElementById("modalContent").innerText = `
    Beschreibung: ${entry.beschreibung}
    Palette: ${entry.palette}
    Kartons/Pal: ${entry.kartonsPal || "N/A"}
    Packhöhe: ${entry.packhoehe || "N/A"}
    Kartons pro Lage: ${entry.kartonsLage || "N/A"}
    Kartons: ${entry.kartons}
  `;

  // Zeige das Modal an
  document.getElementById("entryModal").classList.remove("hidden");

  // Schließe das Modal, wenn der Schließen-Button geklickt wird
  document.getElementById("closeModal").onclick = function () {
    document.getElementById("entryModal").classList.add("hidden");
  };
}
