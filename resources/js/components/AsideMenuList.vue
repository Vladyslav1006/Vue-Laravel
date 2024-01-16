<script setup>
import AsideMenuItem from "@/components/AsideMenuItem.vue";
import { usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
const can = computed(() => usePage().props.can)

const props = defineProps({
  isDropdownList: Boolean,
  menu: {
    type: Array,
    required: true,
  },
});


const filteredMenu = computed(() => props.menu.filter(i => !i.resource || can.value.all || can.value[i.resource + '_list']))

const emit = defineEmits(["menu-click", "dropdown-active"]);

const menuClick = (event, item) => {
  emit("menu-click", event, item);
};

const dropdownActive = (event, item) => {
  emit("dropdown-active", event, item);
}
</script>

<template>
  <ul>
    <AsideMenuItem v-for="(item, index) in filteredMenu" :key="index" :item="item" :is-dropdown-list="isDropdownList"
      @menu-click="menuClick" @dropdown-active="dropdownActive" />
  </ul>
</template>
