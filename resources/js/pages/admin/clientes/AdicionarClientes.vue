<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue'
import { Head } from '@inertiajs/vue3'
import ClientesForm from '@/components/admin/clientes/ClientesForm.vue'

</script>

<template>
  <Head>
    <title>Adicionar Cliente - Família Mogi</title>
    <meta name="description" content="Gerenciamento de clientes da Família Mogi" />
  </Head>

  <AuthLayout>
    <ClientesForm />
  </AuthLayout>
</template>
