<script setup lang="ts">
import { defineEmits, defineProps } from 'vue';

interface FormData {
  name: string;
  email: string;
  telefone: string;
  paymentMethod: string;
  cardNumber: string;
  expiry: string;
  cvv: string;
}

const props = defineProps<{
  form: FormData;
}>();

const emit = defineEmits(['submit', 'whatsapp']);

function formatCardNumber() {
  props.form.cardNumber = props.form.cardNumber
    .replace(/\D/g, '')
    .replace(/(.{4})/g, '$1 ')
    .trim();
}

function formatExpiry() {
  props.form.expiry = props.form.expiry
    .replace(/\D/g, '')
    .replace(/^(\d{2})(\d)/, '$1/$2')
    .slice(0, 5);
}

function onSubmit() {
  // Aqui você pode validar o form antes de emitir
  emit('submit');
}

function onWhatsapp() {
  emit('whatsapp');
}
</script>

<template>
  <section class="form-section">
    <h2>Informações do Comprador</h2>
    <form @submit.prevent="onSubmit">
      <label for="name">Nome Completo</label>
      <input id="name" v-model="form.name" type="text" placeholder="Seu nome completo" required />

      <label for="email">E-mail</label>
      <input id="email" v-model="form.email" type="email" placeholder="seu@email.com" required />

      <label for="telefone">Telefone</label>
      <input id="telefone" v-model="form.telefone" type="text" placeholder="0000000" required />

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
            <input id="cvv" v-model="form.cvv" type="password" placeholder="123" maxlength="3" required />
          </div>
        </div>
      </template>

      <div class="flex gap-4 h-auto">
        <button type="submit">Finalizar Pagamento</button>
        <button type="button" @click="onWhatsapp">Pedir no WhatsApp</button>
      </div>
    </form>
  </section>
</template>

<style scoped>
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


</style>
