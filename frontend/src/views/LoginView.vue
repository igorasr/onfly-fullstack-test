<template>
  <div class="min-h-screen w-full flex items-center justify-center bg-background px-4">
    <div class="w-full max-w-md">
      <div class="text-center mb-8">
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Travel Orders</h1>
        <p class="text-sm text-muted-foreground mt-1">Entre com suas credenciais</p>
      </div>

      <div class="rounded-lg border border-border bg-card p-6 shadow-sm">
        <!-- Error alert -->
        <!-- <div
          v-if="auth.error"
          class="mb-4 rounded-md bg-destructive/10 border border-destructive/30 px-4 py-3 flex items-start gap-3"
        >
          <svg class="w-5 h-5 text-destructive flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="text-sm text-destructive">{{ auth.error }}</p>
        </div> -->

        <form @submit.prevent="handleLogin" class="flex flex-col gap-4">
          <div class="flex flex-col gap-2">
            <BaseInput
              v-model="email"
              label="Email"
              name="email"
              type="email"
              placeholder="seu@email.com"
              required
              autocomplete="email"
            />
          </div>

          <div class="flex flex-col gap-2">
            <BaseInput
              v-model="password"
              label="Senha"
              name="password"
              type="password"
              placeholder="Digite sua senha"
              required
              autocomplete="current-password"
            />
          </div>

          <button
            type="submit"
            class="mt-2 w-full border rounded-md bg-primary px-4 py-2.5 text-sm font-semibold text-primary-foreground hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:ring-offset-background disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2"
          >
            <!-- <LoadingSpinner v-if="auth.loading" size="sm" /> -->
            <span>{{ 'Entrar' }}</span>
          </button>
        </form>
        <div class="text-sm text-center mt-4">
         <span class="text-muted-foreground">Não tem uma conta?</span>
         <a href="/register" class="text-primary hover:underline ml-1">Registrar-se</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import BaseInput from '@/components/BaseInput.vue';
import { useToast } from '@/composables/useToast';

const { showToast } = useToast()

// import LoadingSpinner from '@/components/LoadingSpinner.vue'

const auth = useAuthStore()
const router = useRouter()

const email = ref('')
const password = ref('')

onMounted(() => {
  auth.clearError()
})

async function handleLogin() {
  try {
    
    await auth.login(email.value, password.value)
    router.push({ name: 'dashboard' })
  } catch (error){
    showToast('Erro na autenticação, verifique suas credenciais e tente novamente.', 'error')
  }
}
</script>
