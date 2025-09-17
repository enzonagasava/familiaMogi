<script setup lang="ts">
import { reactive, computed, defineProps, ref } from 'vue';

//Trazendo o prop do PagePay arquivo pai
interface Props {
  cart: Record<string, any>;
}

const props = defineProps<Props>();

const cartItems = ref(
  Object.entries(props.cart).map(([key, item]) => ({
    key,
    ...item,
  }))
);

interface OrderItem {
  id: number;
  name: string;
  price: number;
}

interface FormData {
  name: string;
  email: string;
  paymentMethod: string;
  cardNumber: string;
  expiry: string;
  cvv: string;
}

const form = reactive<FormData>({
  name: '',
  email: '',
  paymentMethod: '',
  cardNumber: '',
  expiry: '',
  cvv: '',
});


const cartTotal = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + item.preco * item.quantidade, 0);
});


function formatCardNumber() {
  form.cardNumber = form.cardNumber
    .replace(/\D/g, '')
    .replace(/(.{4})/g, '$1 ')
    .trim();
}

function formatExpiry() {
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
  if (!form.paymentMethod) {
    alert('Por favor, selecione uma forma de pagamento.');
    return false;
  }
  if (form.paymentMethod === 'cartao') {
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
  }
  return true;
}

function handleSubmit() {
  if (!validateForm()) return;
  alert('Pagamento processado com sucesso!');
  // Aqui você pode integrar com API de pagamento
}
</script>
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

        <label for="email">Telefone</label>
        <input
          id="telefone"
          v-model="form.telefone"
          type="text"
          placeholder="0000000"
          required
        />

        <h2>Forma de Pagamento</h2>
        <label for="paymentMethod">Selecione a forma de pagamento</label>
        <select id="paymentMethod" v-model="form.paymentMethod" required>
          <option value="" disabled>Selecione...</option>
          <option value="cartao">Cartão de Crédito</option>
          <option value="pix">Pix</option>
          <option value="boleto">Boleto</option>
        </select>

        <template v-if="form.paymentMethod === 'cartao'">
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
        </template>

        <button type="submit">Finalizar Pagamento</button>
      </form>
    </section>

    <section class="summary-section">
    <h2 class="font-bold">Resumo do Pedido</h2>
    <div v-for="item in cartItems" :key="item.id" class="flex justify-between items-center">
         <div class="flex ">
            <img :src="item.thumbnail" :alt="item.nome" class="summary-item-thumbnail" />
            <div class="flex flex-col">
                <span>{{ item.nome }}</span>
                <span>Porção: {{ item.porcao }}</span>
            </div>
         </div>

        <!-- Quantidade -->
        <span>Qtd: {{ item.quantidade }}</span>

        <!-- Preço -->
        <span>R$ {{ Number(item.preco).toFixed(2).replace('.', ',') }}</span>
    </div>
    <div class="flex justify-between font-bold text-[18px]">
        <span>Total</span>
        <span>R$ {{ cartTotal.toFixed(2).replace('.', ',') }}</span>
    </div>
    </section>

  </div>
</template>
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
input,
select {
  width: 100%;
  padding: 10px 14px;
  margin-bottom: 20px;
  border: 1.5px solid #ccc;
  border-radius: 6px;
  font-size: 16px;
  transition: border-color 0.3s ease;
}
input:focus,
select:focus {
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

.summary-item-thumbnail {
  width: 50px;
  height: 50px;
  object-fit: cover;
  margin-right: 10px;
  border-radius: 4px;
}

</style>
