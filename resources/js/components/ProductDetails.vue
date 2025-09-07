<script setup lang="ts">
import { ref, computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import 'vue3-carousel/carousel.css';
import { Carousel, Slide, Pagination, Navigation } from 'vue3-carousel';
import ButtonAddCart from './ui/button/ButtonAddCart.vue';



const page = usePage();

const produto = ref(page.props.produto || {});

const imagens = produto.value.imagens;

const selectedWeight = ref(
    produto.value.tamanhos && produto.value.tamanhos.length > 0
        ? produto.value.tamanhos[0].nome
        : ''
);

const imagem_paths = imagens.map(element => element.imagem_path);
const productStock = produto.value.estoque || 0;
const quantidadeSelecionada = ref(1); 

// Computed que busca o preço no produto atual
const precoSelecionado = computed(() => {
    if (!produto.value || !Array.isArray(produto.value.tamanhos)) return 0;
    const tamanho = produto.value.tamanhos.find(t => t.nome === selectedWeight.value);
    return tamanho && tamanho.pivot ? parseFloat(tamanho.pivot.preco) : 0;
});

const calculateShipping = () => {
    alert('Calcular frete!');
    // Lógica para calcular frete
};

const toggleFavorite = () => {
    alert('Togglou favorito!');
    // Lógica para favoritar/desfavoritar
};

const toggleShare = () => {
    alert('Togglou Share!');
    // Lógica para favoritar/desfavoritar
};

const carouselConfig = {
  itemsToShow: 1,
  wrapAround: true
}
</script>

<template>
    <div class="flex min-h-screen justify-center bg-gray-100 px-4 py-12 sm:px-6 lg:px-8">
        <div class="flex w-full max-w-7xl flex-col overflow-hidden rounded-lg bg-white shadow-xl md:flex-row">
            <div class="flex w-full flex-col items-center p-6 md:w-1/2">
                <div class="flex h-full w-full">
                    <div class="mr-4 flex flex-col space-y-2">
                        <button
                            v-for="(thumb, index) in thumbnails"
                            :key="index"
                            @click="selectImage(thumb)"
                            class="h-20 w-20 flex-shrink-0 cursor-pointer overflow-hidden rounded-md border-2"
                            :class="{ 'border-green-500': thumb === mainImage, 'border-gray-300': thumb !== mainImage }"
                        >
                            <img :src="thumb" alt="Miniatura" class="h-full w-full object-cover" />
                        </button>
                    </div>
                    <Carousel v-bind="carouselConfig" class="flex-grow">
                      <Slide v-for="(img, index) in imagem_paths" :key="index">
                        <div class="flex h-full items-center justify-center overflow-hidden rounded-lg border border-gray-200">
                          <img :src="`/storage/${img}`" :alt="productName" class="max-h-[500px] max-w-full object-contain" />
                        </div>
                      </Slide>
                      <template #addons>
                        <Pagination class="absolute bottom-4 left-1/2 -translate-x-1/2" />
                        <Navigation class="absolute top-1/2 -translate-y-1/2" />
                      </template>
                    </Carousel>
                </div>
            </div>

            <div class="flex w-full flex-col justify-between p-6 md:w-1/2">
                <div class="mb-4 flex items-center justify-between text-sm text-gray-600">
                    <div>Home > Cogumelos > <span class="font-semibold text-gray-800">Shitake</span></div>
                    <div class="flex">
                        <button @click="toggleFavorite" class="flex cursor-pointer items-center text-[#6aab9c] hover:text-[#6aab9c]">
                            <svg class="mr-1 h-5 w-5 fill-current" viewBox="0 0 24 24">
                                <path
                                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"
                                />
                            </svg>
                            Comparar /
                        </button>
                        <button @click="toggleShare" class="cursor-pointer text-[#6aab9c]">Compartilhar</button>
                    </div>
                </div>

                <h1 class="mb-2 text-3xl font-bold text-gray-900">{{ produto.nome }}</h1>

                <div class="mb-4 text-2xl font-bold text-gray-900">
                    R${{ precoSelecionado.toFixed(2) }}
                    <p class="mt-1 cursor-pointer text-sm font-normal text-black hover:underline">Ver formas de pagamento</p>
                </div>

                <div class="mb-4">
                    <label class="mb-2 block text-sm font-bold text-gray-700">Selecione a porção</label>
                    <div class="flex space-x-2">
                        <button
                                v-for="tamanho in produto.tamanhos"
                                :key="tamanho.nome"
                                @click="selectedWeight = tamanho.nome"
                                :class="{
                                'bg-[#6aab9c] text-white': selectedWeight === tamanho.nome,
                                'border-gray-300 bg-white text-gray-700 hover:bg-gray-50': selectedWeight !== tamanho.nome,
                                }"
                                class="cursor-pointer rounded-md border-2 px-4 py-2 text-sm font-medium"
                            >
                                {{ tamanho.nome }}
                            </button>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="mb-2 text-sm font-bold text-gray-700">Estoque disponível</p>
                    <div class="flex items-center">
                        <label class="mr-2 text-gray-900">Quantidade: </label>
                        <select v-model="quantidadeSelecionada"  name="quantidade" id="quantidade">
                            <option v-for="n in Math.min(productStock)" :key="n" :value="n">{{ n }}</option>
                        </select>
                    </div>
                </div>

                <div class="mb-6 flex flex-col space-y-3">
                    <Link
                        :href="route('carrinho.adicionar')"
                        :data="{ id: produto.id, porcao: selectedWeight, produto: produto, preco: precoSelecionado, quantidade: quantidadeSelecionada }"
                        method="post"
                        class="w-full cursor-pointer rounded-md bg-[#6aab9c] px-4 py-3 font-semibold text-white transition duration-300 hover:bg-[#77bdad]"
                    >
                        Comprar agora
                    </Link>
                    <ButtonAddCart 
                    :produto="produto" 
                    :portion="selectedWeight" 
                    @add-to-cart="cartItemCount++"
                    />  
                </div>

                <div class="mb-6">
                    <p class="mb-2 text-sm font-bold text-gray-700">Calcular o frete:</p>
                    <div class="flex items-center">
                        <input
                            type="text"
                            placeholder="Insira o seu CEP"
                            class="mr-2 flex-grow rounded-md border border-gray-300 px-3 py-2 text-gray-700 focus:border-transparent focus:ring-2 focus:ring-[#6aab9c] focus:outline-none"
                        />
                        <button
                            @click="calculateShipping"
                            class="rounded-md bg-gray-200 px-4 py-2 font-semibold text-gray-700 transition duration-300 hover:bg-gray-300"
                        >
                            Calcular
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto mt-8 max-w-7xl rounded-lg bg-white px-4 py-8 shadow-xl sm:px-6 lg:px-8">
        <h2 class="mb-4 text-2xl font-bold text-gray-900">Descrição:</h2>
        <div class="prose max-w-none text-gray-700" v-html="produto.descricao"></div>
    </div>
</template>

<style scoped>
/* Estilos específicos se necessário, mas o Tailwind já faz a maior parte */
.prose ul {
    list-style-type: disc;
    margin-left: 1.25em;
}
.prose li {
    margin-bottom: 0.5em;
}
</style>
