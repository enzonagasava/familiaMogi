<script setup>
defineProps({
  pedidoId: String,
  status: String
})

onMounted(() => {
  const timer = setInterval(async () => {
    const res = await fetch(`/api/pedido/status/${pedidoId}`);
    const pedido = await res.json();

    if (pedido.status === "approved") {
      clearInterval(timer);
      window.location.href = "/checkout/sucesso";
    }

    if (pedido.status === "rejected") {
      clearInterval(timer);
      window.location.href = "/checkout/falha";
    }

  }, 4000);
});

</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-yellow-50">
    <div class="bg-white shadow-lg rounded-xl p-8 max-w-md text-center">
      <h1 class="text-2xl font-bold text-yellow-600 mb-3">Pagamento Pendente ⏳</h1>

      <p class="text-gray-700 mb-4">
        Estamos aguardando a confirmação do pagamento.
      </p>

      <div class="bg-gray-100 p-4 rounded-md text-left mb-4">
        <p><strong>ID do Pagamento:</strong> {{ paymentId }}</p>
        <p><strong>Status:</strong> {{ status }}</p>
      </div>

      <a href="/" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg">
        Voltar ao início
      </a>
    </div>
  </div>
</template>
