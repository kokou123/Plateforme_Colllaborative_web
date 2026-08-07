<!-- frontend/app/components/AuthInput.vue -->
<script setup lang="ts">
const props = defineProps<{
  modelValue: string
  label?: string
  type?: string
  icon: string
  placeholder?: string
  error?: string
}>()
defineEmits(['update:modelValue'])

const showPassword = ref(false)
const isPassword = computed(() => props.type === 'password')
const resolvedType = computed(() => isPassword.value ? (showPassword.value ? 'text' : 'password') : (props.type || 'text'))
</script>

<template>
  <div>
    <label v-if="label" class="text-xs font-medium text-mist mb-1.5 block">{{ label }}</label>
    <div class="relative">
      <UIcon :name="icon" class="w-4 h-4 text-mist absolute left-3.5 top-1/2 -translate-y-1/2" />
      <input
        :value="modelValue"
        @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
        :type="resolvedType"
        :placeholder="placeholder"
        class="w-full bg-ink-900 border border-ink-700 rounded-lg pl-10 pr-10 py-2.5 text-sm text-white placeholder:text-mist/50 focus:outline-none focus:border-brand transition-colors"
      />
      <button
        v-if="isPassword" type="button" @click="showPassword = !showPassword"
        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-mist hover:text-white"
      >
        <UIcon :name="showPassword ? 'i-lucide-eye-off' : 'i-lucide-eye'" class="w-4 h-4" />
      </button>
    </div>
    <p v-if="error" class="text-danger text-xs mt-1.5">{{ error }}</p>
  </div>
</template>