<script setup>
import { containerMaxW } from "@/config.js";
import BaseLevel from "@/components/BaseLevel.vue";
import JustboilLogo from "@/components/JustboilLogo.vue";

const year = new Date().getFullYear();
</script>

<template>
  <footer class="py-0 px-6" :class="containerMaxW">
    <BaseLevel>
      <div class="text-center md:text-left">
        ©2023, Meide
        <slot />
      </div>

    </BaseLevel>
  </footer>
</template>
