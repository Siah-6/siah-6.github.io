function addQuestion() {
  var addNewQuestion = document.getElementById("questionInput").value;
  var newQuestionContainer = document.getElementById("questionsContainer");

  if (addNewQuestion != "") {
    newQuestionContainer.insertAdjacentHTML(
      "beforeend",
      `<div class="questionBlock">
        <div class="questionsTitle">` +
        addNewQuestion +
        `</div>
        <button onclick="removeAddedOption(this)" type="button" class="btnDeleteQuestion">Delete</button>
        <input type="hidden" name="guestsQuestions[]" value="` +
        addNewQuestion +
        `">
      </div>`
    );

    document.getElementById("questionInput").value = "";
  } else {
    alert("Please fill in all fields");
  }
}

function removeAddedOption(button) {
  button.parentElement.remove();
}

function addImage() {
  var insertImage = document.getElementById("uploadImage");
  var previewContainer = document.getElementById("imageIsUploaded");

  if (previewContainer.children.length >= 1) {
    alert("You can only upload 1 image.");
    return;
  }
  if (insertImage.files && insertImage.files[0]) {
    var file = insertImage.files[0];
    var reader = new FileReader();
    reader.onload = function (image) {
      previewContainer.innerHTML +=
        `<div class="col-3 mt-4 position-relative"> 
    <div class="card" style="width: 100px; height: 100px; overflow: hidden;">
        <img src="` +
        image.target.result +
        `" style="width: 100%; height: 100%; object-fit: cover;">
        
        <button type="button" onclick="removePhoto(this)" class="imgButtonDelete position-absolute" 
            style="top: 5px; right: 5px; background: none; border: none; padding: 0; z-index: 2;">
            <img src="../assets/img/btndelete.png" alt="Delete" style="width: 20px; height: 20px;">
        </button>
    </div>
</div>`;
    };

    reader.readAsDataURL(file);
  }
}

function removePhoto(button) {
  button.closest(".position-relative").remove();
}
