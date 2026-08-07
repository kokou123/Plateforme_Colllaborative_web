<script setup lang="ts">
const props = defineProps<{
  modelValue: string
  label?: string
  type?: string
  icon?: string
  placeholder?: string
  error?: string
}>()
defineEmits(['update:modelValue'])
</script>

<template>
  <div>
    <label v-if="label" class="text-xs font-medium text-slate mb-1.5 block">{{ label }}</label>
    <div class="relative">
      <UIcon v-if="icon" :name="icon" class="w-4 h-4 text-slate absolute left-3.5 top-1/2 -translate-y-1/2" />
      <input
        :value="modelValue"
        @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
        :type="type || 'text'"
        :placeholder="placeholder"
        :class="['w-full bg-white border border-border rounded-lg py-2.5 text-sm focus:outline-none focus:border-brand transition-colors', icon ? 'pl-10 pr-3' : 'px-3']"
      />
    </div>
    <p v-if="error" class="text-danger text-xs mt-1.5">{{ error }}</p>
  </div>
</template>