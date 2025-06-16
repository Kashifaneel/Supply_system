<template>
  <button
    :type="type"
    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
    :class="{
      'opacity-25 cursor-not-allowed': disabled,
    }"
    :disabled="disabled"
  >
    <slot />
  </button>
</template>

<script lang="ts">
export default {
  props: {
    type: {
      type: String as () => 'button' | 'submit' | 'reset',
      default: 'button',
    },
    disabled: {
      type: Boolean,
      default: false,
    },
  },
};
</script>
