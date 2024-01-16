<script setup>
import { ref, computed } from "vue";
import axios from "axios";
import {
  mdiReload
} from "@mdi/js";
import BaseIcon from "@/components/BaseIcon.vue";
const props = defineProps({
  label: {
    type: String,
    default: null,
  },
  modelValue: {
    type: [String, Number, Boolean, Object],
    default: null,
  },
  sourceData: {
    type: [Array, String],
    default: '',
  },
  inputDebounceMs: {
    type: Number,
    default: 500,
    required: false,
  },
});
const dlistActive = ref(false)

const emit = defineEmits(["update:modelValue"]);

const itemSelected = (item) => {
  dlistActive.value = false;
  emit("update:modelValue", item);
}
const isAxsioLoader = ref(false);
const debounceTimeouts = {};
const filteredData = ref((Array.isArray(props.sourceData) ? props.sourceData : []));
const onChange = (keystr) => {
  query.value = keystr;
  clearTimeout(debounceTimeouts['cansearch']);
  isAxsioLoader.value = true;
  debounceTimeouts['cansearch'] = setTimeout(() => {
    if (Array.isArray(props.sourceData)) {
      if (keystr === '') {
        filteredData.value = props.sourceData
      }
      else if (props.sourceData.length > 0) {
        filteredData.value = props.sourceData.filter((listitem) =>
          listitem.label
            .toLowerCase()
            .replace(/\s+/g, '')
            .includes(keystr.toLowerCase().replace(/\s+/g, ''))
        )
        if (filteredData.value.length == 0) {
          filteredData.value.push({ id: 0, label: 'No Candidate found!!' })
        }
      }
      dlistActive.value = true;
      activeli.value = 0
      isAxsioLoader.value = false;
    }
    else {
      filteredData.value = [];
      axios.post(route(props.sourceData), { keystr: keystr })
        .then(response => {
          if (response.data.length == 0) {
            filteredData.value.push({ id: 0, label: 'No Candidate found!!' })
          }
          else {
            response.data.forEach(cand => filteredData.value.push(cand));
          }
          dlistActive.value = true;
          activeli.value = 0
          isAxsioLoader.value = false;
        });
    }
  }, props.inputDebounceMs);
}

const query = computed({
  get() {
    return props.modelValue ? props.modelValue.label : '';
  },
  set(val) {
    props.modelValue.label = val
  },
});
const activeli = ref(0);
const combodown = () => {
  activeli.value = (activeli.value < filteredData.value.length - 1 ? activeli.value + 1 : filteredData.value.length - 1)
  scrolltovie();
}
const comboup = () => {
  activeli.value = activeli.value > 1 ? activeli.value - 1 : 0
  scrolltovie();
}
const scrolltovie = () => {
  const el = document.getElementById('dli' + activeli.value);
  el.scrollIntoView({ block: "nearest", inline: "nearest" });
}
const combobox = ref(null);
const timeo = ref(null)
const hideDropDown = () => {
  clearTimeout(timeo.value)
  timeo.value = setTimeout(() => { dlistActive.value = false }, 500);
}
const showDropDown = () => {
  if (combobox.value.value === '' && Array.isArray(props.sourceData)) {
    filteredData.value = props.sourceData
  }
  clearTimeout(timeo.value)
  dlistActive.value = true; combobox.value.focus();

}
</script>

<template>
  <div class="relative">
    <div class="relative">
      <input autocomplete="off" ref="combobox"
        class="block w-full pl-8 text-sm rounded-md shadow-sm focus:border-transparent focus:ring-0 text-sm border border-gray-200 dark:border-slate-700 bg-gray-100 dark:bg-slate-600 text-opacity"
        :placeholder="label" :value="query" type="text" name="global" @input="onChange($event.target.value)"
        @keydown.enter.prevent="itemSelected(filteredData[activeli])" @keydown.up="comboup" @keydown.down="combodown"
        @blur="hideDropDown">
      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" v-if="isAxsioLoader">
        <BaseIcon :path="mdiReload" class="animate-spin" />
      </div>
      <div class="absolute inset-y-0 right-0 pr-3 flex items-center" @click="showDropDown">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
          <path
            d="M12,18.17L8.83,15L7.42,16.41L12,21L16.59,16.41L15.17,15M12,5.83L15.17,9L16.58,7.59L12,3L7.41,7.59L8.83,9L12,5.83Z"
            style="fill: currentcolor;"></path>
        </svg>
      </div>
    </div>
    <div v-if="filteredData.length > 0 && dlistActive"
      class="text-sm border-b border-gray-100 lg:border lg:bg-white lg:absolute lg:top-full lg:left-0 lg:min-w-full lg:z-20 lg:rounded-lg lg:shadow-lg lg:dark:bg-slate-800 dark:border-slate-700 mt-1 overflow-x-auto max-h-72">
      <li v-for="(item, ind) in filteredData"
        class="block lg:flex items-center relative cursor-pointer text-blue-600 dark:text-white dark:hover:text-slate-400 hover:text-black py-2 px-3 dark:border-slate-700 border-b border-gray-100"
        :id="`dli${ind}`" :class="{
          'bg-teal-600 text-white': ind == activeli
        }" @click="itemSelected(item)">
        {{ item.label }}</li>
    </div>
  </div>
</template>
