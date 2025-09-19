<script setup>
import { computed } from 'vue'

const emit = defineEmits(['update:checked'])

const props = defineProps({
  checked: {
    type: [Array, Boolean],
    required: true,
  },
  value: {
    type: [String, Number, Boolean, Object],
    default: null,
  },
  id: {
    type: String,
    default: null,
  },
  name: {
    type: String,
    default: null,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
})

const proxyChecked = computed({
  get: () => props.checked,
  set: (val) => emit('update:checked', val),
})
</script>

<template>
  <input
    type="checkbox"
    :id="id"
    :name="name"
    :value="value"
    v-model="proxyChecked"
    :disabled="disabled"
    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
  />
</template>
