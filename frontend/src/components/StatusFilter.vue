<template>
  <div class="relative">
    <select
      :value="modelValue || ''"
      @change="onChange"
      class="h-9 rounded-md border border-input bg-background px-3 py-1 text-sm text-foreground shadow-sm focus:outline-none focus:ring-2 focus:ring-ring appearance-none pr-8 cursor-pointer"
    >
      <option
        v-for="opt in Object.values(options)"
        :key="opt.value"
        :value="opt.value"
      >
        {{ opt.label }}
      </option>
    </select>

    <svg
      class="absolute right-2 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground pointer-events-none"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      stroke-width="2"
    >
      <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
    </svg>
  </div>
</template>

<script setup lang="ts">
import { TravelRequestStatus } from '@/types'

interface Props {
  modelValue: TravelRequestStatus | ''
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: '',
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: TravelRequestStatus | ''): void
}>()

const options: Record<TravelRequestStatus | '', { label: string; value: TravelRequestStatus | '' }> = {
  '': { label: 'Todos', value: '' },
  [TravelRequestStatus.REQUESTED]: { label: 'Pending', value: TravelRequestStatus.REQUESTED },
  [TravelRequestStatus.APPROVED]: { label: 'Approved', value: TravelRequestStatus.APPROVED },
  [TravelRequestStatus.CANCELLED]: { label: 'Cancelled', value: TravelRequestStatus.CANCELLED }
}

function onChange(e: Event) {
  const val = (e.target as HTMLSelectElement).value
  emit('update:modelValue', (val || '') as TravelRequestStatus | '')
}
</script>