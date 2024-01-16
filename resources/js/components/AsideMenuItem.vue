<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";
//import { RouterLink } from "vue-router";
import { useStyleStore } from "@/stores/style.js";
import { mdiChevronDown, mdiChevronRight } from "@mdi/js";
import { getButtonColor } from "@/colors.js";
import BaseIcon from "@/components/BaseIcon.vue";
import AsideMenuList from "@/components/AsideMenuList.vue";

const itemHref = computed(() =>
  props.item.route ? route(props.item.route) : props.item.href
);

// Add activeInactiveStyle
const activeInactiveStyle = computed(() =>
  props.item.route && route().current(props.item.route)
    ? styleStore.asideMenuItemActiveStyle
    : ""
);

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
  isDropdownList: Boolean,
});

const emit = defineEmits(["menu-click", "dropdown-active"]);

const styleStore = useStyleStore();

const hasColor = computed(() => props.item && props.item.color);

const asideMenuItemActiveStyle = computed(() =>
  hasColor.value ? "" : styleStore.asideMenuItemActiveStyle
);

const isDropdownActive = ref(false);

const componentClass = computed(() => [
  props.isDropdownList ? "py-2 pr-3 text-sm" : "py-3",
  hasColor.value
    ? getButtonColor(props.item.color, false, true)
    : `${styleStore.asideMenuItemStyle} dark:text-slate-300 dark:hover:text-white`,
]);

const can = computed(() => usePage().props.can)
const filteredMenu = computed(() => props.item.menu.filter(i => !i.resource || can.value.all || can.value[i.resource + '_list']))

const hasDropdown = computed(() => !!props.item.menu && filteredMenu.value.length > 0);

const menuClick = (event) => {
  emit("menu-click", event, props.item);

  if (hasDropdown.value) {
    isDropdownActive.value = !isDropdownActive.value;
  }
};
onMounted(() => {
  if (props.item.route && route().current(props.item.route)) {
    emit("dropdown-active");
  }
});
const dropdownActive = () => {
  isDropdownActive.value = true;
}

</script>

<template>
  <li>
    <component :is="item.route ? Link : 'a'" :href="itemHref" :target="item.target ?? null" class="flex cursor-pointer"
      :class="componentClass" @click="menuClick" v-if="!props.item.menu || hasDropdown">
      <BaseIcon :title="item.label" v-if="item.icon" :path="item.icon" class="flex-none" :class="activeInactiveStyle"
        w="w-12" h="h-6" :size="18" />
      <span class="grow text-ellipsis line-clamp-1" :class="[activeInactiveStyle]">{{
        item.label }}</span>
      <BaseIcon v-if="hasDropdown" :path="isDropdownActive ? mdiChevronDown : mdiChevronRight" class="flex-none"
        :class="activeInactiveStyle" w="w-12" />
    </component>
    <AsideMenuList v-if="hasDropdown" :menu="item.menu" :class="[
      activeInactiveStyle,
      isDropdownActive ? 'block dark:bg-slate-800/50' : 'hidden',
    ]" is-dropdown-list @dropdown-active="dropdownActive" />
  </li>
</template>
