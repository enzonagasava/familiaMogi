<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';


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

const menuClasses = computed(() => {
  return isMenuOpen.value
    ? 'flex flex-col md:flex-row' // mobile aberto
    : 'hidden md:flex';           // mobile fechado, desktop visível
});

</script>

<template>    
    <Head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="/css/app.css" />
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <header class="fixed top-0 left-0 right-0 z-50 bg-white shadow-md">
        <div class="flex justify-center w-screen h-[70px] px-4 sm:px-6 lg:px-8"> 
            <div class="flex justify-between w-full max-w-[1366px] items-center"> 
                <div class="flex items-center w-full">
                    <img src="/images/logo-sem-fundo.png" class="h-[50px] cursor-pointer" alt="familiaMogi-logo" @click="$inertia.visit('/')">
                    <div class="w-full justify-between" :class="{'hidden md:flex': !isMenuOpen,
                        'flex md:hidden absolute top-full bg-white w-full left-0 py-4 shadow-lg z-40 items-center flex-col text-center': isMenuOpen // mostra no mobile com estilo
                        }">
                        <nav class="flex flex-col md:flex-row gap-2 md:gap-4 ml-4"
        >
                        <Link href="/produtos" @click="closeMenu" class="py-2 px-4 hover:bg-gray-100 rounded">Produtos</Link>
                        <Link href="/about-us" @click="closeMenu" class="py-2 px-4 hover:bg-gray-100 rounded">Quem Somos</Link>
                        <Link href="/how-to-buy" @click="closeMenu" class="py-2 px-4 hover:bg-gray-100 rounded">Como Comprar</Link>
                        <Link href="/contact" @click="closeMenu" class="py-2 px-4 hover:bg-gray-100 rounded">Contato</Link>
                        <Link href="#" @click="closeMenu" class="py-2 px-4 hover:bg-gray-100 rounded">Área do Produtor</Link>
                        </nav>
                        <Link href="/carrinho" @click="closeMenu" class="bg-[#6aab9c] text-white py-2 px-4 rounded hover:bg-[#77bdad] transition">
                            Carrinho (0)
                        </Link>
                    </div>
                </div>  

                <button @click="toggleMenu" class="md:hidden p-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                    <svg v-if="!isMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
            </header>

    <main class="min-h-screen pt-[70px]"> <slot />
    </main>

    <footer class="bg-[#d3b381] w-full py-8 px-4 mt-8">
        <div class="max-w-[1366px] mx-auto flex flex-col md:flex-row justify-between gap-8">
            <div>
                <h3 class="font-bold">Contatos</h3>
                <ul class="leading-[2]">
                    <li>
                        <Link href="#">Fale Conosco</Link>
                    </li>
                    <li class="flex items-center">
                        <i class="fa-brands fa-whatsapp mr-2 flex" style="color: #fff; font-size: 25px;"></i>
                        <Link href="#">1199999-9999</Link>
                    </li>
                    <li class="flex items-center">
                        <i class="fa-brands fa-instagram mr-2 flex" style="color: #fff; font-size: 25px;"></i>
                        <Link href="#">@familiaMogi</Link>
                    </li>
                    <li class="flex items-center">
                        <i class="fa-solid fa-location-dot mr-2" style="color: #fff; font-size: 25px;"></i>
                        <Link href="#">Estrada Missao Kinoshita</Link>
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="font-bold mb-1">Categorias</h3>
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
                <h3 class="font-bold mb-1">Ferramentas</h3>
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
                <h3 class="font-bold mb-1">Termos</h3>
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
        <div class="text-center text-sm mt-8">Família Mogi - CNPJ: ©Todos os direitos reservados. {{currentYear}}</div>
    </footer>

    <a href="https://wa.me/5511999999999" target="_blank" class="fixed lg:bottom-15 bottom-10 lg:right-15 right-10 z-50 ">
        <img src="/images/whatsapp.png" alt="WhatsApp" class="w-16 h-16 rounded-full shadow-lg hover:shadow-xl transition-shadow duration-300">
    </a>
</template>