<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import CartDetails from '@/components/CartDetails.vue';
import SwiperThumbnail from '@/components/SwiperThumbnail.vue';

</script>

<template>
    <div>
        <Head>
            <title>Família Mogi - Anúncio</title>
            <meta name="description" content="Anúncio da Família Mogi" />
        </Head>
        <AppLayout>
        <CartDetails>
        </CartDetails>
        <SwiperThumbnail
            title="Veja Também"
        >
        </SwiperThumbnail>
        </AppLayout>    
    </div>
</template>