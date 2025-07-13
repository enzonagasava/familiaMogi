<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import SwiperThumbnail from '@/components/SwiperThumbnail.vue';

</script>

<template>
    <Head>
        <title>Família Mogi - Produtos</title>
        <meta name="description" content="Produtos da Família Mogi" />
    </Head>
    <AppLayout>
    <SwiperThumbnail
        title="Os Mais Pedidos"
    >
    </SwiperThumbnail>
    <SwiperThumbnail
        title="Cogumelos"
    >
    </SwiperThumbnail>
    <SwiperThumbnail
        title="Verduras"
    >
    </SwiperThumbnail>
    </AppLayout>
</template>