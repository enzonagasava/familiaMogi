<script setup>
defineProps({
  paymentId: String,
  status: String
})
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-green-50">
    <div class="bg-white shadow-lg rounded-xl p-8 max-w-md text-center">
      <h1 class="text-2xl font-bold text-green-600 mb-3">Pagamento Aprovado! 🎉</h1>

      <p class="text-gray-700 mb-4">
        Obrigado por sua compra! Seu pagamento foi processado com sucesso.
      </p>

      <div class="bg-gray-100 p-4 rounded-md text-left mb-4">
        <p><strong>ID do Pagamento:</strong> {{ paymentId }}</p>
        <p><strong>Status:</strong> {{ status }}</p>
      </div>

      <a href="/" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg">
        Voltar ao início
      </a>
    </div>
  </div>
</template>
