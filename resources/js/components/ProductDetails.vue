<script setup lang="ts">
import { ref } from 'vue';

const props = defineProps<{
    product: {
        id: number;
        name: string;
        description: string;
        price: number;
        stock: number;
        weightOptions: { label: string; value: string }[];
        images: string[]; // Array de URLs das imagens do produto
        // Adicione outras propriedades do produto conforme necessário
    };
}>();

// Dados de exemplo para renderização (em um cenário real, viriam das props)
const productName = ref(props.product?.name || 'Bandeja Shitake');
const productPrice = ref(props.product?.price || 19.9);
const productDescription = ref(
    props.product?.description ||
        `
    Você precisa conhecer o nosso cogumelo Shitake superfresco de qualidade premium!
    Ele é cultivado pela família da Carol e Cecília em Sorocaba e tem um sabor bem marcante e acentuado.
    Apesar de ser mais comum prepará-lo com shoyu, você pode experimentar grelhá-lo em uma frigideira bem quente, apenas com azeite, sal e temperos como ervas, para sentir todo o seu sabor.
    E se você gosta de risoto, o Shitake é perfeito para dar um toque especial! Comece refogando os cogumelos com alho e separe em duas partes: uma para iniciar o risoto e outra para adicionar nos últimos minutos de cozimento do arroz e enriquecer ainda mais o prato.
    Veja a receita completa de risoto de cogumelo shitake no nosso Blog.
`,
);
const productStock = ref(props.product?.stock || 1); // Exemplo: 1 unidade disponível
const selectedWeight = ref(props.product?.weightOptions[0]?.value || '200g'); // Peso selecionado padrão
const mainImage = ref(props.product?.images[0] || '/images/default-main-product.jpg'); // Imagem principal
const thumbnails = ref(props.product?.images || ['/images/thumb1.jpg', '/images/thumb2.jpg', '/images/thumb3.jpg', '/images/thumb4.jpg']); // Miniaturas das imagens

// Funções para interação
const selectImage = (imageSrc: string) => {
    mainImage.value = imageSrc;
};

const addToCart = () => {
    alert('Produto adicionado ao carrinho!');
    // Lógica para adicionar ao carrinho (ex: fazer uma requisição Inertia ou Axios)
};

const buyNow = () => {
    alert('Comprar agora!');
    // Lógica para comprar agora
};

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
                    <div class="flex flex-grow items-center justify-center overflow-hidden rounded-lg border border-gray-200">
                        <img :src="mainImage" :alt="productName" class="max-h-[500px] max-w-full object-contain" />
                    </div>
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

                <h1 class="mb-2 text-3xl font-bold text-gray-900">{{ productName }}</h1>

                <div class="mb-4 text-2xl font-bold text-gray-900">
                    R${{ productPrice.toFixed(2).replace('.', ',') }}
                    <p class="mt-1 cursor-pointer text-sm font-normal text-black hover:underline">Ver formas de pagamento</p>
                </div>

                <div class="mb-4">
                    <label class="mb-2 block text-sm font-bold text-gray-700">Selecione a porção</label>
                    <div class="flex space-x-2">
                        <button
                            @click="selectedWeight = '200g'"
                            class="cursor-pointer rounded-md border-2 px-4 py-2 text-sm font-medium"
                            :class="{
                                'bg-[#6aab9c] text-white': selectedWeight === '200g',
                                'border-gray-300 bg-white text-gray-700 hover:bg-gray-50': selectedWeight !== '200g',
                            }"
                        >
                            200g
                        </button>
                        <button
                            @click="selectedWeight = '1kg'"
                            class="cursor-pointer rounded-md border-2 px-4 py-2 text-sm font-medium"
                            :class="{
                                'bg-[#6aab9c] text-white': selectedWeight === '1kg',
                                'border-gray-300 bg-white text-gray-700 hover:bg-gray-50': selectedWeight !== '1kg',
                            }"
                        >
                            1kg
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="mb-2 text-sm font-bold text-gray-700">Estoque disponível</p>
                    <div class="flex items-center">
                        <span class="mr-2 text-gray-900">Quantidade: {{ productStock }} Unidade</span>
                        <svg class="h-4 w-4 fill-current text-gray-600" viewBox="0 0 20 20">
                            <path
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            />
                        </svg>
                    </div>
                </div>

                <div class="mb-6 flex flex-col space-y-3">
                    <button
                        @click="buyNow"
                        class="w-full cursor-pointer rounded-md bg-[#6aab9c] px-4 py-3 font-semibold text-white transition duration-300 hover:bg-[#77bdad]"
                    >
                        Comprar agora
                    </button>
                    <button
                        @click="addToCart"
                        class="w-full cursor-pointer rounded-md border border-[#6aab9c] bg-white px-4 py-3 font-semibold text-[#6aab9c] transition duration-300 hover:bg-green-50"
                    >
                        Adicionar no carrinho
                    </button>
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
        <div class="prose max-w-none text-gray-700" v-html="productDescription"></div>
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
