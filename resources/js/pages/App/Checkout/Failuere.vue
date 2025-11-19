<script setup lang="ts">
defineProps({
  status: String,
  message: String
})
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-red-50">
    <div class="bg-white shadow-lg rounded-xl p-8 max-w-md text-center">
      <h1 class="text-2xl font-bold text-red-600 mb-3">Pagamento Falhou ❌</h1>

      <p class="text-gray-700 mb-4">
        Parece que houve um problema ao processar seu pagamento.
      </p>

      <div class="bg-gray-100 p-4 rounded-md text-left mb-4">
        <p><strong>Status:</strong> {{ status || "erro" }}</p>
        <p><strong>Mensagem:</strong> {{ message || "O pagamento não foi concluído." }}</p>
      </div>

      <a href="/carrinho" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg">
        Tentar novamente
      </a>
    </div>
  </div>
</template>
