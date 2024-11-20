document.addEventListener("DOMContentLoaded", function () {
  const breedsListContainer = document.getElementById("breedsList");
  const breedInput = document.getElementById("breedInput");
  const searchResults = document.getElementById("searchResults");

  if (breedsListContainer) {
      fetch("https://dog.ceo/api/breeds/list/all")
          .then(response => response.json())
          .then(data => {
              const breeds = data.message;
              for (let breed in breeds) {
                  const breedItem = document.createElement("a");
                  breedItem.href = `view.html?breed=${breed}`;
                  breedItem.className = "list-group-item list-group-item-action";
                  breedItem.innerText = breed;
                  breedsListContainer.appendChild(breedItem);
              }
          })
          .catch(error => console.error("Error fetching breed list:", error));
  }

  if (breedInput && searchResults) {
      breedInput.addEventListener("input", function () {
          const query = breedInput.value.toLowerCase();
          searchResults.innerHTML = ""; 

          if (query.length > 0) {
              fetch("https://dog.ceo/api/breeds/list/all")
                  .then(response => response.json())
                  .then(data => {
                      const breeds = data.message;
                      for (let breed in breeds) {
                          if (breed.toLowerCase().includes(query)) {
                              const resultItem = document.createElement("a");
                              resultItem.href = `view.html?breed=${breed}`;
                              resultItem.className = "list-group-item list-group-item-action";
                              resultItem.innerText = breed;
                              searchResults.appendChild(resultItem);
                          }
                      }
                  })
                  .catch(error => console.error("Error fetching breed list:", error));
          }
      });
  }
});
