<script setup lang="ts">
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { ref } from 'vue';

import { Autoplay, Navigation, Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/vue';

const activeModules = [Navigation, Pagination, Autoplay];

const slides = ref([
    { id: 1, title: 'Imagem 1', image: '/images/capa1.jpg', color: 'bg-blue-100' },
    { id: 2, title: 'Imagem 2', image: '/images/capa1.jpg', color: 'bg-green-100' },
    { id: 3, title: 'Imagem 3', image: '/images/capa1.jpg', color: 'bg-red-100' },
]);
</script>

<template>
    <div class="swiper-container">
        <Swiper :modules="activeModules" :pagination="{ clickable: true }" :autoplay="{ delay: 3000 }" :loop="true" class="my-custom-swiper">
            <SwiperSlide v-for="slide in slides" :key="slide.id">
                <div :class="`${slide.color} flex h-64 items-center justify-center`">
                    <img :src="slide.image" :title="slide.title" alt="teste" />
                </div>
            </SwiperSlide>
        </Swiper>
    </div>
</template>

<style scoped>
.my-custom-swiper {
    width: 100%;
    height: 400px;
}
</style>
