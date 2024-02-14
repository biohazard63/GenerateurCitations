let fragments = [];

function generateQuote() {
  let quote = "";
  for (let i = 0; i < 3; i++) {
    let randomIndex = Math.floor(Math.random() * fragments.length);
    quote += fragments[randomIndex].citation + " ";
  }
  return quote;
}

document.getElementById("generate").addEventListener("click", function() {
  document.getElementById("quote").textContent = generateQuote();
});

document.getElementById("number").addEventListener("change", function() {
  let number = document.getElementById("number").value;
  let quotes = "";
  for (let i = 0; i < number; i++) {
    quotes += generateQuote() + "\n";
  }
  document.getElementById("quotes").textContent = quotes;
});

fetch('citation.JSON')
  .then(response => response.json())
  .then(data => fragments = data.citations);