<script setup>
import NavBarItem from "@/components/NavBarItem.vue";
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
const can = computed(() => usePage().props.can)

const props = defineProps({
  menu: {
    type: Array,
    default: () => [],
  },
});

const filteredMenu = computed(() => props.menu.filter(i => {

  return !i.resource || can.value.all || can.value[i.resource + '_list']
}))

const emit = defineEmits(["menu-click"]);

const menuClick = (event, item) => {
  emit("menu-click", event, item);
};
</script>

<template>
  <NavBarItem v-for="(item, index) in filteredMenu" :key="index" :item="item" @menu-click="menuClick" />
</template>
