const tabs = document.querySelectorAll('.tab');
const cards = document.querySelectorAll('.product-card');
const receiptList = document.getElementById('receipt-items');
const totalDisplay = document.getElementById('total');
let total = 0;

tabs.forEach(tab => {
  tab.addEventListener('click', () => {
    const selectedCategory = tab.dataset.category;

    tabs.forEach(t => t.classList.remove('active'));
    tab.classList.add('active');

    cards.forEach(card => {
      card.style.display = card.dataset.category === selectedCategory ? 'block' : 'none';
    });
  });
});

cards.forEach(card => {
  card.addEventListener('click', () => {
    const name = card.dataset.name;
    const price = parseFloat(card.dataset.price);

    const li = document.createElement('li');
    li.textContent = `${name} - ₱${price}`;
    receiptList.appendChild(li);

    total += price;
    totalDisplay.textContent = total.toFixed(2);
  });
});

window.addEventListener('DOMContentLoaded', () => {
  document.querySelector('.tab[data-category="hot"]').click();
});
