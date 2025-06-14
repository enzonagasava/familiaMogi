<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import SwiperCapa from '@/components/SwiperCapa.vue';

const titlePage = "Bem-vindo";
</script>

<template>
    <Head :title="titlePage">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        <meta name="description" content="Bem-vindo a Familia Mogi" />
    </Head>
    <AppLayout>
    </AppLayout>
    <SwiperCapa>
    </SwiperCapa>
</template>   