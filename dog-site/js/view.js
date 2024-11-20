document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);
  const breed = urlParams.get("breed");
  const breedTitle = document.getElementById("breedTitle");
  const breedImagesContainer = document.getElementById("breedImages");

  if (breed) {
      breedTitle.innerText = `Images of ${breed.charAt(0).toUpperCase() + breed.slice(1)}`;
      
      fetch(`https://dog.ceo/api/breed/${breed}/images`)
          .then(response => response.json())
          .then(data => {
              const images = data.message;
              images.forEach(imageUrl => {
                  const col = document.createElement("div");
                  col.className = "col-md-4 mb-3";

                  const img = document.createElement("img");
                  img.src = imageUrl;
                  img.className = "img-fluid rounded";

                  col.appendChild(img);
                  breedImagesContainer.appendChild(col);
              });
          })
          .catch(error => console.error("Error fetching breed images:", error));
  } else {
      breedTitle.innerText = "Breed not found";
  }
});
