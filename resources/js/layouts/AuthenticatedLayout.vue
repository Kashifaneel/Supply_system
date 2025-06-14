<template>
  <div class="flex h-screen bg-gray-50">
    <AppSidebar />
    <div class="flex-1 overflow-auto">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';
import AppSidebar from '@/components/AppSidebar.vue';

defineProps({
  title: {
    type: String,
    default: 'Dashboard'
  }
});
</script>