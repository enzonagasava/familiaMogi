<script setup lang="ts">
import SwiperThumbnail from '@/components/SwiperThumbnail.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head>
        <title>Família Mogi - Produtos</title>
        <meta name="description" content="Produtos da Família Mogi" />
    </Head>
    <AppLayout>
        <SwiperThumbnail title="Os Mais Pedidos"> </SwiperThumbnail>
        <SwiperThumbnail title="Cogumelos"> </SwiperThumbnail>
        <SwiperThumbnail title="Verduras"> </SwiperThumbnail>
    </AppLayout>
</template>
