<template lang="">
    <span
      :class="[
        'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold',
        statusConfig[status].classes
      ]"
    >
      {{ statusConfig[status].label }}
    </span>
</template>
<script setup lang="ts">
import { TravelRequestStatus } from '@/types';

  const statusConfig: Record<TravelRequestStatus, { label: string; classes: string }> = {
    [TravelRequestStatus.REQUESTED]: { label: 'Pending', classes: 'border-yellow-200 bg-yellow-50 text-yellow-800' },
    [TravelRequestStatus.APPROVED]: { label: 'Approved', classes: 'border-green-200 bg-green-50 text-green-800' },
    [TravelRequestStatus.CANCELLED]: { label: 'Cancelled', classes: 'border-red-200 bg-red-50 text-red-800' }
  }

  defineProps<{
    status: TravelRequestStatus
  }>()

</script>
<style lang="">
  
</style>