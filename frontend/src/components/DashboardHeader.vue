<template lang="">
<header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-foreground tracking-tight text-balance">Pedidos de Viagem</h1>
        <p class="text-sm text-muted-foreground mt-0.5">
          Bem-vindo, <span class="font-medium text-foreground">{{ authStore.user?.name || '...' }}</span>
        </p>
      </div>
      <div class="flex items-center gap-2">
        <button
          @click="onCreateClick"
          class="h-9 rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground shadow-sm hover:bg-primary/90 transition-colors inline-flex items-center gap-2"
        >
          <PlusIcon />
          Novo Pedido
        </button>
        <button
          @click="logout"
          class="h-9 rounded-md border border-input bg-background px-3 text-sm font-medium text-foreground shadow-sm hover:bg-accent hover:text-accent-foreground transition-colors inline-flex items-center gap-2"
        >
          <ExitIcon />
          Sair
        </button>
      </div>
    </header>
</template>
<script setup lang="ts">

  import { useAuthStore } from '@/stores/auth';
  import { useRouter } from 'vue-router';
  import PlusIcon from '@/components/icons/PlusIcon.vue';
  import ExitIcon from '@/components/icons/ExitIcon.vue';

  const authStore = useAuthStore()
  const router = useRouter()

  interface Props {
    onCreateClick: () => void
  }

  const props = defineProps<Props>()

  async function logout() {
    await authStore.logout()
    router.push({ name: 'login' })
  }
</script>
<style lang="">
  
</style>