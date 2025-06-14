<template>
  <p class="mt-2 text-sm text-red-600" v-if="message">
    {{ message }}
  </p>
</template>

<script>
export default {
  props: {
    message: {
      type: String,
      default: '',
    },
  },
};
</script>