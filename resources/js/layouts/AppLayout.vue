<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

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
        <div class="flex justify-center w-screen h-[70px] px-4 sm:px-6 lg:px-8"> <div class="flex justify-between w-full max-w-[1366px] items-center"> <div class="flex items-center">
                    <img src="/images/logo-sem-fundo.png" class="h-[50px] cursor-pointer" alt="familiaMogi-logo" @click="$inertia.visit('/')">
                    
                    <nav class="hidden md:flex gap-[1rem] ml-4">
                        <Link href="#">Produto</Link>
                        <Link href="#">Quem Somos</Link>      
                        <Link href="#">Como Comprar</Link>
                        <Link href="#">Contato</Link>
                        <Link href="#">Área do Produtor</Link>
                    </nav>
                </div>  
                
                <Link href="/carrinho" class="bg-[#6aab9c] rounded-[8px] text-white pt-1 pb-1 pl-8 pr-8 hidden md:block">Carrinho (0)</Link>

                <button @click="toggleMenu" class="md:hidden p-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                    <svg v-if="!isMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
        
        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <nav v-show="isMenuOpen" class="md:hidden bg-white shadow-lg py-2 pb-4">
                <div class="flex flex-col items-center space-y-2">
                    <Link href="#" @click="closeMenu" class="block py-2 px-4 text-gray-700 hover:bg-gray-100 w-full text-center">Produto</Link>
                    <Link href="#" @click="closeMenu" class="block py-2 px-4 text-gray-700 hover:bg-gray-100 w-full text-center">Quem Somos</Link>      
                    <Link href="#" @click="closeMenu" class="block py-2 px-4 text-gray-700 hover:bg-gray-100 w-full text-center">Como Comprar</Link>
                    <Link href="#" @click="closeMenu" class="block py-2 px-4 text-gray-700 hover:bg-gray-100 w-full text-center">Contato</Link>
                    <Link href="#" @click="closeMenu" class="block py-2 px-4 text-gray-700 hover:bg-gray-100 w-full text-center">Área do Produtor</Link>
                    <Link href="/carrinho" @click="closeMenu" class="bg-[#6aab9c] rounded-[8px] text-white py-2 px-8 mt-2 hover:bg-[#77bdad] transition">Carrinho (0)</Link>
                </div>
            </nav>
        </transition>
    </header>

    <main class="min-h-screen pt-[70px]"> <slot />
    </main>

    <footer class="bg-[#d3b381] w-full py-8 px-4 mt-8">
        <div class="max-w-[1366px] mx-auto flex flex-col md:flex-row justify-between gap-8">
            <div>
                <div class="font-bold">Contatos</div>
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
            <div class="font-bold mb-1">Categorias</div>
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
            <div class="font-bold mb-1">Ferramentas</div>
            <div>Seja um Parceiro</div>
            <div>Área de Entrega</div>
            </div>
            <div>
            <div class="font-bold mb-1">Termos</div>
            <div>Política de Privacidade</div>
            <div>Trocas e Devoluções</div>
            </div>
        </div>
        <div class="text-center text-sm mt-8">Família Mogi - CNPJ: ©Todos os direitos reservados. {{currentYear}}</div>
    </footer>

     <a href="https://wa.me/5511999999999" target="_blank" class="fixed bottom-15 right-15 z-50">
        <img src="/images/whatsapp.png" alt="WhatsApp" class="w-16 h-16 rounded-full shadow-lg hover:shadow-xl transition-shadow duration-300">
    </a>
</template>