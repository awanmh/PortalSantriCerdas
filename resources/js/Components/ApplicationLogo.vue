<script setup>
defineProps({
  class: {
    type: String,
    default: "h-8 w-8", // ukuran default, bisa override
  },
  alt: {
    type: String,
    default: "Logo Sekolah",
  },
});
</script>

<template>
  <img
    src="images/logo.png"
    :class="class"
    :alt="alt"
  />
</template>
