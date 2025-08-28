function openMenu() {
  const openButton = document.getElementById("openButton");
  const closeButton = document.getElementById("closeButton");
  const navbar = document.getElementById("navbar");

  navbar.classList.remove("hidden");

  openButton.classList.remove("block");
  openButton.classList.add("hidden");

  closeButton.classList.remove("hidden");
  closeButton.classList.add("block");
}

function closeMenu() {
  const openButton = document.getElementById("openButton");
  const closeButton = document.getElementById("closeButton");
  const navbar = document.getElementById("navbar");

  navbar.classList.add("hidden");

  openButton.classList.remove("hidden");
  openButton.classList.add("block");

  closeButton.classList.remove("block");
  closeButton.classList.add("hidden");
}

function goBack() {
  window.history.back();
}
