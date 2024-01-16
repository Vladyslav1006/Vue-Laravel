<script setup>
import { ref, watch, onMounted } from "vue";
import { mdiClose, mdiDotsVertical } from "@mdi/js";
import { containerMaxW } from "@/config.js";
import BaseIcon from "@/components/BaseIcon.vue";
import NavBarMenuList from "@/components/NavBarMenuList.vue";
import NavBarItemPlain from "@/components/NavBarItemPlain.vue";

defineProps({
  menu: {
    type: Array,
    required: true,
  },
});

const emit = defineEmits(["menu-click"]);

const menuClick = (event, item) => {
  emit("menu-click", event, item);
};

const isMenuNavBarActive = ref(false);
const currentDateTime = ref('');
onMounted(() => {
  setInterval(() => {
    let local = new Date();
    const dateAsString = local.toString();
    const timezone = dateAsString.match(/\(([^\)]+)\)$/)[1];
    const month = local.toLocaleString('default', { month: 'short' });

    let timestr = local.getFullYear() + ' ' + month + ' ' + local.getDate() + ', ' + local.getHours() + ':' + (local.getMinutes() < 10 ? '0' : '') + local.getMinutes() + ':' + local.getSeconds() + ' ' + timezone;

    currentDateTime.value = timestr;
  }, 1000);
});

</script>

<template>
  <nav class="top-0 inset-x-0 fixed bg-gray-50 h-10 z-30 transition-position w-screen lg:w-auto dark:bg-slate-800">

    <div class="flex lg:items-stretch" :class="containerMaxW">
      <div class="flex flex-1 items-stretch h-10">
        <slot />
      </div>
      <div class="flex-none items-stretch flex h-10 lg:hidden">
        <NavBarItemPlain @click.prevent="isMenuNavBarActive = !isMenuNavBarActive">
          <BaseIcon :path="isMenuNavBarActive ? mdiClose : mdiDotsVertical" size="24" />
        </NavBarItemPlain>
      </div>
      <div
        class="max-h-screen-menu overflow-y-auto lg:overflow-visible absolute w-screen top-10 left-0 bg-gray-50 shadow-lg lg:w-auto lg:flex lg:static lg:shadow-none dark:bg-slate-800"
        :class="[isMenuNavBarActive ? 'block' : 'hidden']">
        <div
          class="block lg:flex items-center relative cursor-pointer text-blue-600 dark:text-white dark:hover:text-slate-400 hover:text-black py-2 px-3  lg:justify-center">
          {{ currentDateTime }} </div>
        <NavBarMenuList :menu="menu" @menu-click="menuClick" />
      </div>
    </div>
  </nav>
</template>
