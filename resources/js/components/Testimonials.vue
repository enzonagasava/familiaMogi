<template>
    <div class="w-full bg-[#f7f7f7] py-8 px-2 flex justify-center">
        <div class=" w-full max-w-[1366px]">
            <h2 class="text-[24px] lg:text-[36px] font-bold mb-8  w-full">Depoimentos</h2>
            <Swiper
                :modules="activeModules"
                :slides-per-view="3"
                :space-between="30"
                :navigation="true"
                :loop="true"
                :breakpoints="{
                    0: { slidesPerView: 1, spaceBetween: 30 },
                    640: { slidesPerView: 2, spaceBetween: 30 },
                    768: { slidesPerView: 3, spaceBetween: 30 },
                }"         
                class="testimonials-swiper"
            >
                <SwiperSlide v-for="(testimonial, idx) in testimonials" :key="testimonial.name">
                    <div :class="['flex flex-col items-center rounded-2xl p-6 w-full h-full', idx % 2 === 0 ? 'bg-[#8ee6c5]' : 'bg-[#c9a875]']">
                        <div class="flex items-center gap-3 mb-2">
                            <img :src="testimonial.avatar" alt="Avatar" class="w-14 h-14 rounded-full border-2 border-white object-cover" />
                            <div class="flex flex-col">
                                <span class="font-semibold text-lg text-black">{{ testimonial.name }}</span>
                                <div class="flex items-center gap-1">
                                    <span v-for="star in 5" :key="star" class="text-yellow-400 text-base">★</span>
                                </div>
                            </div>
                        </div>
                        <hr class="w-4/5 border-t-2 border-gray-700 my-2" />
                        <!-- Espaço para depoimento futuro -->
                    </div>
                </SwiperSlide>
            </Swiper>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Pagination } from 'swiper/modules';

const testimonials = ref([
  {
    name: 'Igor S.',
    avatar: '/images/avatar-igor.jpg',
  },
  {
    name: 'Maria',
    avatar: '/images/avatar-maria.jpg',
  },
  {
    name: 'Rafael',
    avatar: '/images/avatar-rafael.jpg',
  },
  {
    name: 'Ana Paula',
    avatar: '/images/avatar-ana.jpg',
  },
  {
    name: 'Carlos',
    avatar: '/images/avatar-carlos.jpg',
  },
  {
    name: 'Juliana',
    avatar: '/images/avatar-juliana.jpg',
  },
]);

const activeModules = [Navigation, Pagination];
</script>

<style scoped>
.testimonials-swiper {
  width: 100%;
  padding-bottom: 40px;
  padding: 0 3rem !important;
}
.swiper-slide {
  display: flex;
  justify-content: center;
  align-items: stretch;
  height: 220px;
}
</style>
