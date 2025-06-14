<template>
  <input
    :id="id"
    :type="type"
    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    :autocomplete="autocomplete"
  />
</template>

<script>
export default {
  props: {
    id: {
      type: String,
      default: '',
    },
    type: {
      type: String,
      default: 'text',
    },
    modelValue: {
      type: [String, Number],
      default: '',
    },
    autocomplete: {
      type: String,
      default: '',
    },
  },

  emits: ['update:modelValue'],
};
</script>