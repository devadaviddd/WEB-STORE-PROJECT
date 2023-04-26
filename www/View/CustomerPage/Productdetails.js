const toast = document.querySelector('.toast');

// Disable the noti repeat the form
if (window.history.replaceState) {
  window.history.replaceState(null, null, window.location.href);
}
carBtn = document.querySelector("#cartBtn");
orderBtn = document.querySelector("#orderBtn");

carBtn.addEventListener("click", () => {
  const product = {
    productName: document.querySelector("#productName").innerHTML,

    price: Number(document.querySelector("#price").innerHTML),
  };
});

orderBtn.addEventListener("click", () => {
  const product = {
    productName: document.querySelector("#productName").innerHTML,
    price: Number(document.querySelector("#price").innerHTML),
  };
});

function successAdd() {
  toast.querySelector(".toast-body").innerHTML = "Add to cart successfully";
  turnOnToast();
}

// learn from https://www.w3schools.com/bootstrap5/tryit.asp?filename=trybs_toast&stacked=h
function turnOnToast() {
  var announceToast = new bootstrap.Toast(document.querySelector(".toast"));
  announceToast.show();
}