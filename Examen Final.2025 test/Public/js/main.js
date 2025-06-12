document.addEventListener('DOMContentLoaded', () => {
  // === Gestion du Thème (Light/Dark) ===
  const lightButton = document.getElementById('theme-light');
  const darkButton = document.getElementById('theme-dark');
  const body = document.body;
  
  const savedTheme = localStorage.getItem('theme') || 'light';
  body.classList.remove('light', 'dark');
  body.classList.add(savedTheme);
  
  lightButton.addEventListener('click', () => {
    body.classList.remove('dark');
    body.classList.add('light');
    localStorage.setItem('theme', 'light');
  });
  
  darkButton.addEventListener('click', () => {
    body.classList.remove('light');
    body.classList.add('dark');
    localStorage.setItem('theme', 'dark');
  });
  
  // === Menu Burger ===
  const menuToggle = document.querySelector('.menu-toggle');
  const menuList = document.querySelector('.menu-list');
  
  if (menuToggle && menuList) {
    menuToggle.addEventListener('click', () => {
      menuList.classList.toggle('open');
    });
  }
  
  // === Ajout au Panier  avec Confirmation Popup ===
  document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function(event) {
      event.preventDefault();
      const productId = this.getAttribute('data-product-id');
      const quantity = 1; 
      
      fetch(`index.php?route=addToCart&id=${productId}&quantity=${quantity}`)
        .then(response => response.text())
        .then(data => {
          showConfirmation("Produit ajouté au panier !");
        })
        .catch(error => console.error('Erreur AJAX (addToCart):', error));
    });
  });
  
  // === Mise à jour de la Quantité du Panier  ===
  document.querySelectorAll('.update-quantity-form').forEach(form => {
    form.addEventListener('submit', function(event) {
      event.preventDefault();  // Empêche le rechargement de page
      const productId = this.getAttribute('data-product-id');
      const quantity = this.querySelector('input[name="quantity"]').value;
      
      fetch(`index.php?route=updateQuantityAjax`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${encodeURIComponent(productId)}&quantity=${encodeURIComponent(quantity)}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showConfirmation("Quantité mise à jour !");
          recalcCartTotal();
        } else {
          alert(data.message);
        }
      })
      .catch(error => console.error('Erreur AJAX (updateQuantity):', error));
    });
  });
  
  // === Boutons d'Incrémentation / Décrémentation ===
  document.querySelectorAll('.btn-increment').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      const form = this.closest('.update-quantity-form');
      let input = form.querySelector('input[name="quantity"]');
      let currentQuantity = parseInt(input.value) || 0;
      input.value = currentQuantity + 1;
      form.dispatchEvent(new Event('submit'));
    });
  });
  
  document.querySelectorAll('.btn-decrement').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      const form = this.closest('.update-quantity-form');
      let input = form.querySelector('input[name="quantity"]');
      let currentQuantity = parseInt(input.value) || 0;
      if (currentQuantity > 1) {
        input.value = currentQuantity - 1;
        form.dispatchEvent(new Event('submit'));
      }
    });
  });
  
  // === Suppression d'un Article du Panier ===
  document.querySelectorAll('.remove-from-cart').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      const productId = this.getAttribute('data-product-id');
      
      fetch(`index.php?route=removeFromCartAjax`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${encodeURIComponent(productId)}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showConfirmation("Produit retiré du panier !");
          const itemElement = document.getElementById(`cart-item-${productId}`);
          if (itemElement) itemElement.remove();
          recalcCartTotal();
        } else {
          alert(data.message);
        }
      })
      .catch(error => console.error('Erreur AJAX (removeFromCart):', error));
    });
  });
  
  // === Suppression d'une Commande dans le Dashboard  ===
  document.querySelectorAll('.delete-order').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      const orderId = this.getAttribute('data-order-id');
      if (!confirm("Voulez-vous vraiment supprimer cette commande ?")) return;
      
      fetch('index.php?route=deleteOrderAjax', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `orderId=${encodeURIComponent(orderId)}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showConfirmation("Commande supprimée !");
          const row = document.getElementById(`order-${orderId}`);
          if (row) row.remove();
        } else {
          alert(data.message);
        }
      })
      .catch(error => console.error('Erreur AJAX (deleteOrder):', error));
    });
  });
  
  // === Validation du Panier : Bouton "Valider le panier" pour recalculer et afficher le total ===
  const validateCartBtn = document.getElementById('validate-cart');
  if (validateCartBtn) {
    validateCartBtn.addEventListener('click', (e) => {
      e.preventDefault();
      recalcCartTotal();
      const total = document.getElementById('cart-total').innerText;
      showConfirmation("Total du panier : " + total);
    });
  }
  
  // === Fonction de Recalcul du Total du Panier ===
  function recalcCartTotal() {
    let total = 0;
    document.querySelectorAll('table tbody tr').forEach(row => {
      const priceCell = row.children[1];
      const quantityInput = row.querySelector('input[name="quantity"]');
      if (priceCell && quantityInput) {
        let priceText = priceCell.textContent.replace('€','').trim();
        let price = parseFloat(priceText);
        let quantity = parseInt(quantityInput.value);
        total += price * quantity;
      }
    });
    const totalElement = document.getElementById('cart-total');
    if (totalElement) {
      totalElement.innerText = total.toFixed(2) + '€';
    }
  }
  
  // === Fonction d'Affichage de la Confirmation (Popup) ===
  function showConfirmation(message) {
    let popup = document.createElement('div');
    popup.className = 'confirmation-popup';
    popup.textContent = message;
    document.body.appendChild(popup);
    setTimeout(() => {
      popup.style.opacity = '0';
      setTimeout(() => popup.remove(), 500);
    }, 2000);
  }
});
