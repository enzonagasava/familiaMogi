<template>
  <div class="container">
    <section class="form-section">
      <h2>Informações do Comprador</h2>
      <form @submit.prevent="handleSubmit">
        <label for="name">Nome Completo</label>
        <input
          id="name"
          v-model="form.name"
          type="text"
          placeholder="Seu nome completo"
          required
        />

        <label for="email">E-mail</label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          placeholder="seu@email.com"
          required
        />

        <h2>Dados do Cartão</h2>

        <label for="cardNumber">Número do Cartão</label>
        <input
          id="cardNumber"
          v-model="form.cardNumber"
          type="text"
          placeholder="1234 5678 9012 3456"
          maxlength="19"
          @input="formatCardNumber"
          required
        />

        <div class="card-inputs">
          <div>
            <label for="expiry">Validade</label>
            <input
              id="expiry"
              v-model="form.expiry"
              type="text"
              placeholder="MM/AA"
              maxlength="5"
              @input="formatExpiry"
              required
            />
          </div>
          <div>
            <label for="cvv">CVV</label>
            <input
              id="cvv"
              v-model="form.cvv"
              type="password"
              placeholder="123"
              maxlength="3"
              required
            />
          </div>
        </div>

        <button type="submit">Finalizar Pagamento</button>
      </form>
    </section>

    <section class="summary-section">
      <h2>Resumo do Pedido</h2>
      <div v-for="item in orderItems" :key="item.id" class="summary-item">
        <span>{{ item.name }}</span>
        <span>R$ {{ item.price.toFixed(2).replace('.', ',') }}</span>
      </div>
      <div class="summary-item total">
        <span>Total</span>
        <span>R$ {{ totalPrice.toFixed(2).replace('.', ',') }}</span>
      </div>
    </section>
  </div>
</template>

<script lang="ts">
import { defineComponent, reactive, computed } from 'vue';

interface OrderItem {
  id: number;
  name: string;
  price: number;
}

interface FormData {
  name: string;
  email: string;
  cardNumber: string;
  expiry: string;
  cvv: string;
}

export default defineComponent({
  name: 'PaymentPage',
  setup() {
    const form = reactive<FormData>({
      name: '',
      email: '',
      cardNumber: '',
      expiry: '',
      cvv: '',
    });

    const orderItems = reactive<OrderItem[]>([
      { id: 1, name: 'Produto A', price: 99.0 },
      { id: 2, name: 'Produto B', price: 49.0 },
    ]);

    const totalPrice = computed(() =>
      orderItems.reduce((acc, item) => acc + item.price, 0)
    );

    function formatCardNumber() {
      // Formata número do cartão para "1234 5678 9012 3456"
      form.cardNumber = form.cardNumber
        .replace(/\D/g, '')
        .replace(/(.{4})/g, '$1 ')
        .trim();
    }

    function formatExpiry() {
      // Formata validade para "MM/AA"
      form.expiry = form.expiry
        .replace(/\D/g, '')
        .replace(/^(\d{2})(\d)/, '$1/$2')
        .slice(0, 5);
    }

    function validateForm(): boolean {
      if (!form.name.trim()) {
        alert('Por favor, insira seu nome completo.');
        return false;
      }
      if (!form.email.trim() || !/\S+@\S+\.\S+/.test(form.email)) {
        alert('Por favor, insira um e-mail válido.');
        return false;
      }
      if (!/^\d{4} \d{4} \d{4} \d{4}$/.test(form.cardNumber)) {
        alert('Número do cartão inválido. Use o formato 1234 5678 9012 3456.');
        return false;
      }
      if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(form.expiry)) {
        alert('Validade inválida. Use o formato MM/AA.');
        return false;
      }
      if (!/^\d{3}$/.test(form.cvv)) {
        alert('CVV inválido. Deve conter 3 dígitos.');
        return false;
      }
      return true;
    }

    function handleSubmit() {
      if (!validateForm()) return;
      alert('Pagamento processado com sucesso!');
      // Aqui você pode integrar com API de pagamento
    }

    return {
      form,
      orderItems,
      totalPrice,
      formatCardNumber,
      formatExpiry,
      handleSubmit,
    };
  },
});
</script>

<style scoped>
.container {
  background: white;
  max-width: 900px;
  width: 100%;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  display: flex;
  gap: 40px;
  padding: 30px;
  margin: 40px auto;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.form-section,
.summary-section {
  flex: 1;
}
h2 {
  margin-bottom: 20px;
  color: #333;
}
label {
  display: block;
  margin-bottom: 6px;
  font-weight: 600;
  color: #555;
}
input {
  width: 100%;
  padding: 10px 14px;
  margin-bottom: 20px;
  border: 1.5px solid #ccc;
  border-radius: 6px;
  font-size: 16px;
  transition: border-color 0.3s ease;
}
input:focus {
  border-color: #48bb78;
  outline: none;
}
.card-inputs {
  display: flex;
  gap: 15px;
}
.card-inputs > div {
  flex: 1;
}
.summary-section {
  background: #f9fafb;
  border-radius: 10px;
  padding: 20px 25px;
  box-shadow: inset 0 0 10px #e0e5e8;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
.summary-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
  font-size: 16px;
}
.summary-item.total {
  font-weight: 700;
  font-size: 18px;
  border-top: 1px solid #ddd;
  padding-top: 15px;
}
button {
  background-color: #48bb78;
  color: white;
  border: none;
  padding: 15px;
  font-size: 18px;
  font-weight: 600;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  margin-top: 20px;
}
button:hover {
  background-color: #3ca663;
}
@media (max-width: 768px) {
  .container {
    flex-direction: column;
    padding: 20px;
  }
  .form-section,
  .summary-section {
    width: 100%;
  }
  .card-inputs {
    flex-direction: column;
  }
}
</style>