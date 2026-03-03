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
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
          </svg>
          Novo Pedido
        </button>
        <button
          @click="logout"
          class="h-9 rounded-md border border-input bg-background px-3 text-sm font-medium text-foreground shadow-sm hover:bg-accent hover:text-accent-foreground transition-colors inline-flex items-center gap-2"
        >
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          Sair
        </button>
      </div>
    </header>
</template>
<script setup lang="ts">
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

    const authStore = useAuthStore()
    const router = useRouter()

    async function logout() {
      await authStore.logout()
      router.push({ name: 'login' })
    }
</script>
<style lang="">
  
</style>