<script setup lang="ts">
import HowToBuy from '@/components/HowToBuy.vue';
import SwiperCapa from '@/components/SwiperCapa.vue';
import SwiperThumbnail from '@/components/SwiperThumbnail.vue';
import Testimonials from '@/components/Testimonials.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

const titlePage = 'Bem-vindo';
</script>

<template>
    <div>
        <Head :title="titlePage">
            <link rel="preconnect" href="https://rsms.me/" />
            <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
            <meta name="description" content="Bem-vindo a Familia Mogi" />
        </Head>
        <AppLayout>
            <SwiperCapa> </SwiperCapa>
            <SwiperThumbnail title="Conheça os Nossos Produtos"> </SwiperThumbnail>
            <HowToBuy> </HowToBuy>
            <Testimonials> </Testimonials>
        </AppLayout>
    </div>
</template>
