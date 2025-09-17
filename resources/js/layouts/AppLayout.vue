<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref, computed } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { useCartStore } from '@/stores/cart';
import { watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const page = usePage();
const cartStore = useCartStore();

// Inicializa a store com os dados do backend
cartStore.setCart(page.props.cartItems || []);

// Atualiza a store caso as props mudem (ex: navegação Inertia)
watch(
  () => page.props.cartItems,
  (newItems) => {
    cartStore.setCart(newItems || []);
  }
);


// Declara uma variável reativa para armazenar o ano atual.
const currentYear = ref(0);
const isMenuOpen = ref(false);

onMounted(() => {
    currentYear.value = new Date().getFullYear();
});

// Função para alternar o estado do menu
const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
};

// Função para fechar o menu ao clicar em um link (opcional, mas boa prática)
const closeMenu = () => {
    isMenuOpen.value = false;
};

const user = computed(() => !!page.props.auth?.user);
const userLogado = user.value;

const logoutForm = useForm({});

const logout = () => {
  logoutForm.post('/logout', {
    onFinish: () => {
      Inertia.visit('/');
    },
  });
};
</script>

<template>
    <Head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="/css/app.css" />
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        
    </Head>
    <header class="fixed top-0 right-0 left-0 z-50 bg-white shadow-md">
        <div class="flex h-[70px] w-screen justify-center px-4 sm:px-6 lg:px-8">
            <div class="flex w-full max-w-[1366px] items-center justify-between">
                <div class="flex w-full items-center">
                    <img src="/images/logo-sem-fundo.png" class="h-[50px] cursor-pointer" alt="familiaMogi-logo" @click="$inertia.visit('/')" />
                    <div
                        class="w-full justify-between"
                        :class="{
                            'hidden md:flex': !isMenuOpen,
                            'absolute top-full left-0 z-40 flex w-full flex-col items-center bg-white py-4 text-center shadow-lg md:hidden':
                                isMenuOpen, // mostra no mobile com estilo
                        }"
                    >
                        <nav class="ml-4 flex flex-col gap-2 md:flex-row md:gap-4">
                            <Link href="/produtos" @click="closeMenu" class="rounded px-4 py-2 hover:bg-gray-100">Produtos</Link>
                            <Link href="/about-us" @click="closeMenu" class="rounded px-4 py-2 hover:bg-gray-100">Quem Somos</Link>
                            <Link href="/how-to-buy" @click="closeMenu" class="rounded px-4 py-2 hover:bg-gray-100">Como Comprar</Link>
                            <Link href="/contact" @click="closeMenu" class="rounded px-4 py-2 hover:bg-gray-100">Contato</Link>
                            <Link href="#" @click="closeMenu" class="rounded px-4 py-2 hover:bg-gray-100">Área do Produtor</Link>
                        </nav>
                        <Link v-if="userLogado" :href="'/logout'" @click.prevent="logout" class="rounded bg-[#6aab9c] px-4 py-2 text-white transition hover:bg-[#77bdad]">
                            Logout
                        </Link>
                        <Link v-else href="/login" @click="closeMenu" class="rounded bg-[#6aab9c] px-4 py-2 text-white transition hover:bg-[#77bdad]">
                            Login
                        </Link>                        
                        <Link href="/carrinho" @click="closeMenu" class="rounded bg-[#6aab9c] px-4 py-2 text-white transition hover:bg-[#77bdad]">
                            Carrinho ({{ cartStore.cartQuantity }})
                        </Link>
                    </div>
                </div>

                <button @click="toggleMenu" class="p-2 text-gray-700 hover:text-gray-900 focus:outline-none md:hidden">
                    <svg v-if="!isMenuOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg v-else class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <main class="min-h-screen pt-[70px]"><slot /></main>

    <footer class="mt-8 w-full bg-[#d3b381] px-4 py-8">
        <div class="mx-auto flex max-w-[1366px] flex-col justify-between gap-8 md:flex-row">
            <div>
                <h3 class="font-bold">Contatos</h3>
                <ul class="leading-[2]">
                    <li>
                        <Link href="#">Fale Conosco</Link>
                    </li>
                    <li class="flex items-center">
                        <i class="fa-brands fa-whatsapp mr-2 flex" style="color: #fff; font-size: 25px"></i>
                        <Link href="#">1199999-9999</Link>
                    </li>
                    <li class="flex items-center">
                        <i class="fa-brands fa-instagram mr-2 flex" style="color: #fff; font-size: 25px"></i>
                        <Link href="#">@familiaMogi</Link>
                    </li>
                    <li class="flex items-center">
                        <i class="fa-solid fa-location-dot mr-2" style="color: #fff; font-size: 25px"></i>
                        <Link href="#">Estrada Missao Kinoshita</Link>
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="mb-1 font-bold">Categorias</h3>
                <ul>
                    <li><Link href="#">COGUMELOS FRESCOS</Link></li>
                    <li><Link href="#">COMBOS</Link></li>
                    <li><Link href="#">CONGELADOS DIVERSOS</Link></li>
                    <li><Link href="#">EMPÓRIO</Link></li>
                    <li><Link href="#">ORGÂNICOS</Link></li>
                    <li><Link href="#">PROMO</Link></li>
                </ul>
            </div>
            <div>
                <h3 class="mb-1 font-bold">Ferramentas</h3>
                <ul>
                    <li>
                        <Link href="#">Seja um Parceiro</Link>
                    </li>
                    <li>
                        <Link href="#">Área de Entrega</Link>
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="mb-1 font-bold">Termos</h3>
                <ul>
                    <li>
                        <Link href="#">Política de Privacidade</Link>
                    </li>
                    <li>
                        <Link href="#">Trocas e Devoluções</Link>
                    </li>
                </ul>
            </div>
        </div>
        <div class="mt-8 text-center text-sm">Família Mogi - CNPJ: ©Todos os direitos reservados. {{ currentYear }}</div>
    </footer>

    <a href="https://wa.me/5511999999999" target="_blank" class="fixed right-10 bottom-10 z-50 lg:right-15 lg:bottom-15">
        <img src="/images/whatsapp.png" alt="WhatsApp" class="h-16 w-16 rounded-full shadow-lg transition-shadow duration-300 hover:shadow-xl" />
    </a>
</template>
