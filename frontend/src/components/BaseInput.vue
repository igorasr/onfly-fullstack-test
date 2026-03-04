<template>
  <div class="base-input">
    <label v-if="label" class="base-input__label" :for="inputId">
      {{ label }}
    </label>

    <input
      :id="inputId"
      class="w-full rounded-md border border-input bg-background px-3 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all"
      :class="{ 'border-red-500': !!error }"
      :type="type"
      :name="name"
      :placeholder="placeholder"
      :autocomplete="autocomplete"
      :disabled="disabled"
      :required="required"
      :value="modelValue"
      @input="onInput"
      @blur="$emit('blur')"
      @focus="$emit('focus')"
    />

    <small v-if="error" class="text-red-500 text-sm">{{ error }}</small>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
    modelValue: string
    label?: string
    name?: string
    id?: string
    type?: string
    placeholder?: string
    autocomplete?: string
    disabled?: boolean
    required?: boolean
    error?: string
}

const props = withDefaults(
  defineProps<Props>(),
  {
    type: 'text',
    autocomplete: 'off',
    disabled: false,
    required: false,
    label: '',
    name: '',
    id: '',
    placeholder: '',
    error: '',
  }
)

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
  (e: 'blur'): void
  (e: 'focus'): void
}>()

const inputId = computed(() => props.id || props.name || `input-${Math.random().toString(36).slice(2, 9)}`)

function onInput(event: Event) {
  const target = event.target as HTMLInputElement
  emit('update:modelValue', target.value)
}
</script>