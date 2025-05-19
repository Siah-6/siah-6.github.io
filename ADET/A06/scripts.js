let total = 0;

document.addEventListener('DOMContentLoaded', () => {
  setupTabs();
  initializeReceipt();
  
  const checkoutBtn = document.getElementById('checkout-btn');
  if (checkoutBtn) {
    checkoutBtn.addEventListener('click', handleCheckout);
    console.log('Checkout button event listener attached');
  }
});

function setupTabs() {
  const tabs = document.querySelectorAll('.tab');
  
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      
      const category = tab.dataset.category;
      loadProductsByCategory(category);
    });
  });
  
  document.querySelector('.tab[data-category="hot"]').click();
}

function initializeReceipt() {
  fetch('api/receipts.php', {
    method: 'POST'
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Could not create receipt');
    }
    return response.json();
  })
  .then(data => {
    console.log('Created new receipt:', data);
    updateReceiptUI();
  })
  .catch(error => {
    console.error('Failed to create receipt:', error);
    showError('Could not connect to the server. Please try again later.');
  });
}

function loadProductsByCategory(category) {
  const productsContainer = document.querySelector('.products');
  
  productsContainer.innerHTML = '<div class="loading">Loading products...</div>';
  
  fetch(`api/products.php?category=${category}`)
    .then(response => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then(products => {
      console.log(`Products received:`, products);
      
      if (!products || products.length === 0) {
        productsContainer.innerHTML = '<div class="empty-state">No products found in this category</div>';
        return;
      }
      
      productsContainer.innerHTML = '';
      
      products.forEach(product => {
        console.log(`Creating card for product:`, product);
        const card = createProductCard(product);
        productsContainer.appendChild(card);
      });
    })
    .catch(error => {
      console.error(`Error loading products for ${category}:`, error);
      productsContainer.innerHTML = `
        <div class="error-state">
          Could not load products. Error: ${error.message}
          <button id="retry-btn">Retry</button>
        </div>
      `;
      
      document.getElementById('retry-btn').addEventListener('click', () => {
        loadProductsByCategory(category);
      });
    });
}

function createProductCard(product) {
  const card = document.createElement('div');
  card.className = 'product-card';
  card.dataset.id = product.id;
  card.dataset.category = product.category;
  card.dataset.name = product.name;
  card.dataset.price = product.price;
  
  card.innerHTML = `
    <img src="${product.image_url}" alt="${product.name}" />
    <h3>${product.name}</h3>
    <p>${product.description}</p>
    <p class="price">₱${product.price}</p>
  `;
  
  card.addEventListener('click', () => addToCart(product));
  
  return card;
}

// Add a product to the cart
function addToCart(product) {
  const receiptId = document.querySelector('.receipt').dataset.receiptId;
  
  if (!receiptId) {
    showError('Could not find active receipt. Please refresh the page.');
    return;
  }
  
  fetch(`api/receipts.php?id=${receiptId}&action=add_item`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      productId: product.id,
      quantity: 1
    })
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Could not add item to cart');
    }
    return response.json();
  })
  .then(data => {
    console.log('Item added to cart:', data);
    
    updateReceiptUI();
    
    showToast(`Added ${product.name} to your order!`);
  })
  .catch(error => {
    console.error('Error adding to cart:', error);
    showError('Could not add item to cart. Please try again.');
  });
}

function updateReceiptUI() {
  fetch('index.php?action=get_cart')
    .then(response => response.json())
    .then(data => {
      const receiptList = document.getElementById('receipt-items');
      const totalDisplay = document.getElementById('total');
      const { cartItems, receiptTotal, receiptId } = data;
      
      document.querySelector('.receipt').dataset.receiptId = receiptId;
      
      receiptList.innerHTML = '';
      
      cartItems.forEach(item => {
        const li = document.createElement('li');
        const itemTotal = item.product.price * item.quantity;
        
        li.innerHTML = `${item.product.name} ${item.quantity > 1 ? `(x${item.quantity})` : ''} - ₱${itemTotal}`;
        receiptList.appendChild(li);
      });
      
      totalDisplay.textContent = receiptTotal;
      total = receiptTotal;
      
      const checkoutBtn = document.querySelector('#checkout-btn') || createCheckoutButton();
      
      if (cartItems.length > 0) {
        checkoutBtn.removeAttribute('disabled');
      } else {
        checkoutBtn.setAttribute('disabled', 'true');
      }
    })
    .catch(error => {
      console.error('Error updating receipt:', error);
    });
}

function createCheckoutButton() {
  const receiptSection = document.querySelector('.receipt');
  
  let checkoutBtn = document.querySelector('#checkout-btn');
  
  if (!checkoutBtn) {
    checkoutBtn = document.createElement('button');
    checkoutBtn.id = 'checkout-btn';
    checkoutBtn.className = 'checkout-btn';
    checkoutBtn.textContent = 'Checkout';
    
    checkoutBtn.addEventListener('click', handleCheckout);
    
    receiptSection.appendChild(checkoutBtn);
  }
  
  return checkoutBtn;
}

function handleCheckout() {
  const receiptId = document.querySelector('.receipt').dataset.receiptId;
  
  if (!receiptId) {
    showError('Could not find active receipt. Please refresh the page.');
    return;
  }
  
  fetch(`api/receipts.php?id=${receiptId}&action=complete`, {
    method: 'POST'
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Could not complete checkout');
    }
    return response.json();
  })
  .then(data => {
    console.log('Checkout complete:', data);
    
    showToast(`Order complete! Total: ₱${total}. Thank you for your purchase!`);
    
    initializeReceipt();
  })
  .catch(error => {
    console.error('Checkout error:', error);
    showError('Could not complete your order. Please try again.');
  });
}

function showError(message) {
  const errorDiv = document.createElement('div');
  errorDiv.className = 'error-message';
  errorDiv.textContent = message;
  
  document.body.appendChild(errorDiv);
  
  setTimeout(() => {
    errorDiv.classList.add('show');
  }, 10);
  
  setTimeout(() => {
    errorDiv.classList.remove('show');
    setTimeout(() => {
      document.body.removeChild(errorDiv);
    }, 300);
  }, 3000);
}

function showToast(message) {
  const toast = document.createElement('div');
  toast.className = 'toast-message';
  toast.textContent = message;
  
  document.body.appendChild(toast);
  
  setTimeout(() => {
    toast.classList.add('show');
  }, 10);
  
  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => {
      document.body.removeChild(toast);
    }, 300);
  }, 3000);
}