<script setup lang="ts">
import { ref } from 'vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Thumbs, Pagination } from 'swiper/modules';
import ButtonAddCart from './ui/button/ButtonAddCart.vue';
import ButtonPortion from './ui/button/ButtonPortion.vue';

const activeModules = [Navigation, Thumbs, Pagination];

const props = defineProps<{
    title?: string;
}>();

const slides = ref([
    { id: 1, title: 'Cogumelo Shitake', image:'/images/capa1.jpg', porção: ['200g', '1kg']},
    { id: 2, title: 'Cogumelo Shitake', image:'/images/capa1.jpg', porção: ['200g','1kg']},
    { id: 3, title: 'Cogumelo Shitake', image:'/images/capa1.jpg', porção: ['200g', '1kg']},
    { id: 4, title: 'Cogumelo Shitake', image:'/images/capa1.jpg', porção: ['200g', '1kg']},
    { id: 5, title: 'Cogumelo Paris', image:'/images/capa2.jpg', porção: ['200g', '1kg']},
    { id: 6, title: 'Cogumelo Portobello', image:'/images/capa3.jpg', porção: ['200g', '1kg']},
    { id: 7, title: 'Cogumelo Eryngui', image:'/images/capa4.jpg', porção: ['200g', '1kg']},
    { id: 8, title: 'Cogumelo Hiratake', image:'/images/capa5.jpg', porção: ['200g', '1kg']},
    { id: 9, title: 'Cogumelo Shimeji', image:'/images/capa6.jpg', porção: ['200g', '1kg']},
    { id: 10, title: 'Cogumelo Enoki', image:'/images/capa7.jpg', porção: ['200g', '1kg']},
]);

// Reactive variables for portion selection and cart
import { type Ref } from 'vue'; // Import type Ref
const selectedPortions = ref<Record<number | string, string>>({});

const cartItemCount = ref(0);
interface CartItem {
  slideId: number;
  title: string;
  portion: string;
  quantity: number;
}
const cart: Ref<CartItem[]> = ref([]);

const selectPortion = (slideId:number, portion:string) => {
  selectedPortions.value[slideId] = portion;
  console.log(`Slide ${slideId}: Selected portion ${portion}`);
};

const addToCart = (slideId: number) => {
  console.log(slideId);
  const portion = selectedPortions.value[slideId];
  if (portion) {
    console.log(`✅ Tentando adicionar ao carrinho: Slide ${slideId} - ${portion}`);
    cartItemCount.value++;
    console.log(`🛒 Total de itens no carrinho: ${cartItemCount.value}`);

    const existingItemIndex = cart.value.findIndex(item => item.slideId === slideId && item.portion === portion);
    if (existingItemIndex !== -1) {
      cart.value[existingItemIndex].quantity++;
    } else {
      const slide = slides.value.find(s => s.id === slideId);
      if (slide) {
        cart.value.push({
          slideId: slide.id,
          title: slide.title,
          portion: portion,
          quantity: 1,
        });
      }
    }
    const currentTotalItemsInCart = cart.value.reduce((sum, item) => sum + item.quantity, 0);
    console.log(`🛒 Carrinho atual (detalhado):`, cart.value);
    console.log(`🔢 Total de itens únicos no carrinho: ${cart.value.length}`);
    console.log(`🔢 Total de quantidades no carrinho: ${currentTotalItemsInCart}`);
  } else {
    console.warn(`⚠️ Nenhuma porção selecionada para o Slide ${slideId}. Por favor, selecione uma porção primeiro.`);
  }
};
</script>

<template>
    <div class="flex flex-col items-center w-full bg-[#f5f5f5] py-6 px-2 rounded-lg">
        <div class="relative w-full max-w-[1366px]">
            <h2 class="text-[24px] lg:text-[36px] font-bold mb-4 w-full">{{ props.title }}</h2>
            <Swiper
                :modules="activeModules"
                :slides-per-view="4"
                :navigation="true"
                :loop="true"
                :space-between="20"
                class="my-custom-swiper"
                :breakpoints="{
                    0: { slidesPerView: 1, spaceBetween: 30 },
                    640: { slidesPerView: 2, spaceBetween: 30 },
                    768: { slidesPerView: 3, spaceBetween: 30 },
                    1024: { slidesPerView: 4, spaceBetween: 58 },
                }"         
                >
                <SwiperSlide v-for="slide in slides" :key="slide.id">
                    <div class="flex flex-col items-center bg-white">
                        <img
                            :src="slide.image"
                            :title="slide.title"
                            alt="Produto"
                            class="min-w-[300px] h-[300px] object-cover rounded-[8px] mb-2 border-4 cursor-pointer hover:brightness-[1.10]"
                            @click="$inertia.visit(route('anuncio'))"
                        />
                        <div class="font-semibold text-lg mt-2 mb-1">
                            <h4 class="cursor-pointer" @click="$inertia.visit(route('anuncio'))">{{ slide.title }}</h4>
                        </div>
                        <div class="text-xs text-gray-600 mb-2">selecione a porção</div>
                        <div class="flex gap-2 mb-3">
                          <ButtonPortion 
                            :slide="slide" 
                            :selectedPortions="selectedPortions" 
                            @select-portion="selectPortion"
                          />
                        </div> 
                        <ButtonAddCart 
                            :slide="slide"
                            :selectedPortions="selectedPortions" 
                            @add-to-cart="addToCart"
                        />                       
                    </div>
                </SwiperSlide>
            </Swiper>
        </div>
    </div>
</template>


<style scoped>
.my-custom-swiper {
    width: 100%;
    height: 370px;
    padding: 0 3rem !important;
}
.swiper-slide {
    display: flex;
    justify-content: center;
    align-items: stretch;
    position: relative;
}
</style>