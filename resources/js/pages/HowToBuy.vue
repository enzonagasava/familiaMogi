<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import HowToBuy from '@/components/HowToBuy.vue';
</script>

<template>
    <Head>
        <title>Família Mogi - Como Comprar</title>
        <meta name="description" content="Anúncio da Família Mogi" />
    </Head>
    <AppLayout>
        <HowToBuy>
        </HowToBuy>
    </AppLayout>
</template>