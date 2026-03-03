<template lang="">
  <div class="min-h-screen w-full flex items-center justify-center bg-background px-4">
    <div class="w-full max-w-md">
      <div class="text-center mb-8">
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Registrar-se</h1>
        <p class="text-sm text-muted-foreground mt-1">Crie sua conta para acessar o sistema</p>
      </div>
      <div class="bg-card rounded-lg shadow-md p-6">
        <form @submit.prevent="handleRegister" class="space-y-4">
          <div class="flex flex-col gap-2">
            <BaseInput
              v-model="name"
              label="Nome"
              name="name"
              type="text"
              placeholder="Seu nome completo"
              :error="nameError"
              required
            />
          </div>
          <div class="flex flex-col gap-2">
            <BaseInput
              v-model="email"
              label="Email"
              name="email"
              type="email"
              placeholder="seu@email.com"
              :error="emailError"
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
              :error="passwordError"
              required
              autocomplete="current-password"
            />
          </div>
          <div class="flex flex-col gap-2">
            <BaseInput
              v-model="password_confirmation"
              label="Confirmar Senha"
              name="password_confirmation"
              type="password"
              placeholder="Confirme sua senha"
              :error="passwordConfirmationError"
              required
              autocomplete="current-password"
            />
          </div>
          <button
            type="submit"
            class="mt-2 w-full border rounded-md bg-primary px-4 py-2.5 text-sm font-semibold text-primary-foreground hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:ring-offset-background disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2"
          >
            <!-- <LoadingSpinner v-if="auth.loading" size="sm" /> -->
            <span>{{ 'Registrar-se' }}</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</template >
<script setup lang="ts">
  import BaseInput from '@/components/BaseInput.vue';
  import { ref } from 'vue';
  import { useAuthStore } from '@/stores/auth'
  import { useRouter } from 'vue-router';

  const auth = useAuthStore()
  const router = useRouter()

  const name = ref('');
  const email = ref('');
  const password = ref('');
  const password_confirmation = ref('');

  const passwordConfirmationError = ref('');

  function validatePassword() {
      return password.value !== password_confirmation.value
  }

  async function handleRegister() {
    
    if (validatePassword()) {
      passwordConfirmationError.value = 'As senhas não coincidem';
      return;
    }
    
    await auth.register(name.value, email.value, password.value, password_confirmation.value)
    router.push({ name: 'login' })
  }

</script>
<style lang="">
  
</style>