const noContent = document.querySelector(".noContent");
const productsCard = document.querySelector(".productsCard");
const item = document.querySelector(".item");
const form = document.querySelector("#cart_items_form");
let deleteBtns = document.querySelectorAll(".delete-btn");
const checkout = document.querySelector(".checkoutCard");
const price = document.querySelector("#totalPrice");
let items = document.querySelectorAll(".item");
let tempPrice = 0;
window.onload = makeForm();
function getElementLocalStorage(product, item, key) {
  item.getElementsByTagName("p")[0].innerHTML = key;
  item.getElementsByTagName("img")[0].src = product["imageURL"];

  item.getElementsByTagName("b")[0].innerHTML = product["productName"];

  item.getElementsByTagName("input")[0].value = product["quantity"];
  item.getElementsByTagName("span")[1].innerHTML = product["description"];
  let totalPrice = Number(product["price"]);
  let sum = 0;

  for (let it = 0; it < Number(product["quantity"]); it++) {
    sum = sum + totalPrice;
    tempPrice = tempPrice + totalPrice;
    price.innerHTML = tempPrice + "$";
  }
  item.getElementsByClassName("total-price")[0].innerHTML = "$" + sum;
}

function makeForm() {
  for (let i = 0; i < localStorage.length; i++) {
    const key = localStorage.key(i);
    let product = JSON.parse(localStorage.getItem(key));
    let quantity = product["quantity"];
    let inputElement = document.createElement("INPUT");
    inputElement.setAttribute("type", "text");
    inputElement.setAttribute("value", quantity);
    inputElement.setAttribute("name", key);
    inputElement.setAttribute("hidden", true);
    form.append(inputElement);
  }
}

function pay() {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", window.location.href);
  xhr.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      // Typical action to be performed when the document is ready:
      localStorage.clear();
      document.querySelector("#announce").innerHTML =
        "Thank you! Your Order Has Been Recieved";
      console.log("done");
    } else {
      document.querySelector("#announce").innerHTML =
        "Reload A Page. Your Order Is Invalid";
    }

    noContent.style.display = "flex";
    while (productsCard.lastChild.id !== "noContent") {
      productsCard.removeChild(productsCard.lastChild);
    }
  };
  // or onerror, onabort
  if (localStorage.length > 0) {
    var formData = new FormData(form);
    xhr.send(formData);
  } else {
    document.querySelector("#announce").innerHTML =
      "Your cart is empty. Please add some item!";
  }
}

if (localStorage.length > 0) {
  for (let i = 0; i < localStorage.length; i++) {
    const key = localStorage.key(i);
    let product = JSON.parse(localStorage.getItem(key));
    if (i == 0) {
      // item.classList.add('new-box');
      getElementLocalStorage(product, item, key);
      // item.classList.remove('new-box');
      continue;
    }
    let clone = item.cloneNode(true);
    productsCard.append(clone);

    getElementLocalStorage(product, item, key);
  }
  deleteBtns = document.querySelectorAll(".delete-btn");
  items = document.querySelectorAll(".item");
  console.log(items);
} else {
  tempPrice = 0;
  price.innerHTML = tempPrice + "$";
  noContent.style.display = "flex";
  item.style.display = "none";
}

deleteBtns.forEach((btn, index) => {
  btn.addEventListener("click", () => {
    let id = items[index].getElementsByTagName("p")[0].innerHTML;
    let tempProduct = JSON.parse(localStorage.getItem(id));
    localStorage.removeItem(id);
    price.innerHTML = tempPrice - tempProduct["price"];
    tempPrice = tempPrice - tempProduct["price"] * tempProduct["quantity"];
    productsCard.removeChild(items[index]);
    price.innerHTML = tempPrice + "$";

    if (localStorage.length == 0) {
      noContent.style.display = "flex";
      noContent.style.flexDirection = "column";
      

      tempPrice = 0;
      price.innerHTML = tempPrice + "$";
    }
  });
});
