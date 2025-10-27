<script setup lang="ts">
defineProps({
  imagensParaRenderizar: Array,
  imagemModal: String,
})

defineEmits(['onFilesChange', 'removerImagem', 'abrirModal', 'fecharModal'])
</script>

<template>
  <div>
    <label class="block font-semibold mb-2 text-gray-700">Imagem do Produto</label>
    <input
      type="file"
      multiple
      accept="image/*"
      @change="$emit('onFilesChange', $event)"
      class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-green-600 file:text-white hover:file:bg-green-700 cursor-pointer"
    />
    <p class="mt-1 text-gray-600">
      {{ imagensParaRenderizar.length }} arquivo{{ imagensParaRenderizar.length !== 1 ? 's' : '' }} selecionado{{ imagensParaRenderizar.length !== 1 ? 's' : '' }}.
    </p>
    <div class="mt-4 flex gap-4 flex-wrap">
      <div
        v-for="imagem in imagensParaRenderizar"
        :key="imagem.id"
        class="relative w-24 h-24 rounded overflow-hidden border border-gray-300 shadow cursor-pointer"
      >
        <img
          :src="imagem.url"
          alt="Preview da Imagem"
          class="w-full h-full object-cover"
          @click="$emit('abrirModal', imagem.id)"
        />
        <a
          @click.stop="$emit('removerImagem', imagem.id)"
          class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-800"
          title="Remover imagem"
        >
          ×
        </a>
      </div>
    </div>

    <div
      v-if="imagemModal"
      @click.self="$emit('fecharModal')"
      class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50"
    >
      <div class="relative max-w-3xl max-h-[80vh]">
        <button
          @click="$emit('fecharModal')"
          class="absolute top-2 right-2 text-white bg-black bg-opacity-50 rounded-full w-8 h-8 flex items-center justify-center text-xl font-bold hover:bg-opacity-80"
          title="Fechar"
        >
          ×
        </button>
        <img
          :src="imagemModal"
          alt="Imagem ampliada"
          class="max-w-full max-h-[80vh] rounded"
        />
      </div>
    </div>
  </div>
</template>