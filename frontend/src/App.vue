<script setup lang="ts">
import { RouterView } from 'vue-router'
import ToastNotification from '@/components/ToastNotification.vue'

</script>

<template>
  <main class="w-full bg-background text-foreground">
    <RouterView />
    <ToastNotification />
  </main>
</template>

<style scoped>

</style>
