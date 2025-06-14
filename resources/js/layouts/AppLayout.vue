<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>    
    <Head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="/css/app.css" />
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <header>
        <div class="flex justify-center w-screen h-[70px]">
            <div class="flex justify-between w-[1366px] items-center">
                <div class="flex items-center">
                <img src="/images/logo-sem-fundo.png" class="h-[50px]" alt="familiaMogi-logo">
                <nav class="flex gap-[1rem] ml-4">
                    <Link href="#">Produto</Link>
                    <Link href="#">Quem Somos</Link>      
                    <Link href="#">Como Comprar</Link>
                    <Link href="#">Contato</Link>
                    <Link href="#">Área do Produtor</Link>
                </nav>
                </div>  
                    <Link href="#" class="bg-[#6aab9c] rounded-[8px] text-white pt-1 pb-1 pl-8 pr-8">Carrinho (0)</Link>
            </div>
        </div>      
    </header>
</template>
