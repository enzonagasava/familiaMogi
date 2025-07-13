<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

import { reactive } from 'vue'

const form = reactive({
  name: '',
  email: '',
  phone: '',
  message: ''
})

const handleSubmit = () => {
  // Aqui você pode fazer um POST para uma rota ou usar o fetch/axios
  console.log('Enviando dados:', form)
}

</script>

<template>
    <Head>
        <title>Família Mogi - Contato</title>
        <meta name="description" content="Anúncio da Família Mogi" />
    </Head>
    <AppLayout>
        <section class="bg-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <!-- Formulário -->
                <div>
                <h2 class="text-3xl font-bold mb-6">Fale Conosco</h2>
                <form @submit.prevent="handleSubmit" class="bg-green-50 p-6 rounded-xl shadow-md space-y-6">
                    <div>
                    <label class="block font-medium mb-1">Nome:</label>
                    <input v-model="form.name" type="text" placeholder="Seu nome"
                            class="w-full border border-green-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400" />
                    </div>

                    <div>
                    <label class="block font-medium mb-1">Email:</label>
                    <input v-model="form.email" type="email" placeholder="Seu e-mail"
                            class="w-full border border-green-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400" />
                    </div>

                    <div>
                    <label class="block font-medium mb-1">Telefone:</label>
                    <input v-model="form.phone" type="tel" placeholder="(99) 9 9999-9999"
                            class="w-full border border-green-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400" />
                    </div>

                    <div>
                    <label class="block font-medium mb-1">Assunto:</label>
                    <textarea v-model="form.message" rows="4" placeholder="Escreva sua mensagem aqui..."
                                class="w-full border border-green-300 rounded-md px-4 py-2 resize-none focus:outline-none focus:ring-2 focus:ring-green-400"></textarea>
                    </div>

                    <div class="text-center">
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-md transition duration-200">
                        Enviar
                    </button>
                    </div>
                </form>
                </div>

                <!-- Informações de Contato -->
                <div class="flex flex-col space-y-6">
                <div class="bg-white border-l-4 border-green-600 shadow p-6 rounded-xl">
                    <h3 class="text-xl font-bold mb-4">Informações de Contato</h3>
                    <p class="mb-2">
                    <strong>Telefone:</strong><br />
                    (99) 99999-9999
                    </p>
                    <p class="mb-2">
                    <strong>Email:</strong><br />
                    contato@exemplo.com
                    </p>
                    <p class="mb-2">
                    <strong>Endereço:</strong><br />
                    Rua Exemplo, 123 - Bairro Central<br />
                    Mogi das Cruzes - SP
                    </p>
                </div>

                <!-- Google Maps -->
                <div class="h-64 w-full rounded-xl overflow-hidden shadow-lg">
                    <iframe
                    class="w-full h-full"
                    frameborder="0"
                    allowfullscreen
                    referrerpolicy="no-referrer-when-downgrade"
                    loading="lazy"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d234069.33976631134!2d-46.35132931263291!3d-23.55646526008725!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94cdd81269172647%3A0xb3859ba7fe8bbb07!2sMogi%20das%20Cruzes%20-%20SP!5e0!3m2!1spt-BR!2sbr!4v1752414806190!5m2!1spt-BR!2sbr">
                    </iframe>
                </div>
                </div>
            </div>
            </div>
        </section>
    </AppLayout>
</template>