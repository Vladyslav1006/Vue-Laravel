<template>
  <div class="flex flex-col">
    <div class=" overflow-x-auto " :class="{ 'max-h-[calc(100vh-221px)]': stickyHeader }">
      <div class="align-middle inline-block min-w-full ">
        <div class="border-gray-200 relative">
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
const props = defineProps({
  stickyHeader: {
    type: Boolean,
    default: false,
    required: false,
  },
})
</script>
