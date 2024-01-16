<template>
  <div  class="filter-field relative" v-bind:class = "(props.can.value.all || props.can.value['jobrequest_AdvFilter'])?'can-access':''" v-for="(filter, key) in    filters   " :key="key">
    <FormControl v-if="getColumn(filter.key).type === 'select'" v-model="computedValue[filter.key]"
      :options="getColumn(filter.key).options" @keydown.enter.prevent="keydownHandler" :defaultDisabled="true"/>
    <div v-else-if="getColumn(filter.key).type === 'datePicker'" class="grid grid-cols-1 lg:grid-cols-2 gap-1 custom-filters">
      <div class="vuepicker-demo grid grid-cols-2 gap-1 relative">

        <VueDatePicker
          input-class-name="!border-gray-700 text-gray-400 dark:text-slate-200 dark:placeholder-gray-400 shadow-sm text-sm bg-gray-100 dark:bg-slate-800"
          @update:model-value="setFilterValue(`${filter.key}_start`, $event)" :month-change-on-scroll="false" :range="false"
          :enable-time-picker="false" format="yyyy-MM-dd" auto-apply :model-value="computedValue[`${filter.key}_start`]"
          :name="`${filter.key}_start`" :placeholder="`${filter.label}  From`"
          v-if="getColumn(filter.key).type === 'datePicker'">
        </VueDatePicker>
        <VueDatePicker
          input-class-name="!border-gray-700 text-gray-400 dark:text-slate-200 dark:placeholder-gray-400 shadow-sm text-sm bg-gray-100 dark:bg-slate-800"
          @update:model-value="setFilterValue(`${filter.key}_end`, $event)" :month-change-on-scroll="false" :range="false"
          :enable-time-picker="false" format="yyyy-MM-dd" auto-apply :model-value="computedValue[`${filter.key}_end`]"
          :name="`${filter.key}_end`" :placeholder="`${filter.label}  To`"
          v-if="getColumn(filter.key).type === 'datePicker'">
        </VueDatePicker>
      </div>
  </div> 
    <FormControl v-else :name="filter.key" v-model="computedValue[filter.key]" :placeholder="filter.label" @keydown.enter.prevent="keydownHandler" />
    <!-- &&  -->
    <span v-if="(getColumn(filter.key).type !== 'datePicker')" class="advance-filter" :data-class="filter.key" @click="showAdvanceFilterModal(filter.key, getColumn(filter.key)?.options, resourceName)">
      <img src="/images/filter.png" />
    </span>
    <span v-else-if="((props.can.value.all || props.can.value['jobrequest_AdvFilter']))" class="advance-filter" :data-class="filter.key" @click="showAdvanceFilterModal(filter.key, getColumn(filter.key)?.options, resourceName)">
      <img src="/images/filter.png" />
    </span>

    <div class="advance-filter-modal dark:bg-slate-800" v-show="showAdvanceFilter" v-if="isAdvanceFilterActive == filter.key">      
      <a href="#" class="underline text-sm text-green-600" @click="selectDeselect(filter.key, 'select')">Select All </a>
      <a href="#" class="underline text-sm text-green-600 ml-2" @click="selectDeselect(filter.key, 'deselect')"> Clear All</a>
      <multiselect
      v-model="selectedValue" 
      :options="selectOptions"
      :multiple="true"
      :close-on-select="false"
      :clear-on-select="false"
      :show-labels="false"
      label="label"
      track-by="id"
      class="multiselect-tags-wrapper"
      >
      </multiselect>
      <BaseButtons>
        <BaseButton :label="buttonLabel" :color="button" @click="confirm(filter.key)" />
        <BaseButton :label="cancelButtonLabel" :color="button" outline  @click="cancel(filter.key)" />
      </BaseButtons>
    </div>
  </div>
</template>

<script setup>
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import FormControl from "@/components/FormControl.vue";
import { ref, onMounted, computed } from 'vue';
import BaseButton from "@/components/BaseButton.vue";
import BaseButtons from "@/components/BaseButtons.vue";
import axios from "axios";
import Multiselect from 'vue-multiselect'
import jQuery from "jquery";
import { usePage } from '@inertiajs/vue3';
const $ = jQuery;
window.$ = $;

const props = defineProps({
  filters: {
    type: Object,
    required: true,
  },
  columns: {
    type: Object,
    required: true,
  },
  modelValue: {
    type: [String, Number, Boolean, Array, Object],
    default: [],
  },
  resourceName: {
    type: String,
    required: true,
  },
  button: {
    type: String,
    default: "success",
  },
  buttonLabel: {
    type: String,
    default: "OK",
  },
  cancelButtonLabel: {
    type: String,
    default: "Cancel",
  },
  can: {
    type: Object,
    default: computed(() => usePage().props.can),
  }

});
const isAdvanceFilterActive = ref(false);
const showAdvanceFilter = ref(false);
const selectOptions = ref([]);
const selectedValue = ref(null);
const emit = defineEmits(["update:modelValue", 'formSubmit']);

const confirmCancel = (filterkey, mode) => {
  if(mode == 'confirm'){
    let fieldValue = '';
    if(selectedValue.value.length > 0){
      const mapped = selectedValue.value.map(item => (item.id ));
      const fieldValueArray = mapped.filter(function(e){return e});
      fieldValue = fieldValueArray.join(",");
    }
    let datesColums = ['dob_kid','dob_old_kid','start_date','dob_young_kid','created_at'];
    //Date values update
    if(datesColums.includes(filterkey)){
     computedValue.value[`${filterkey}_start`] = fieldValue;
    }else{   
      computedValue.value[filterkey] = fieldValue;
    }
  }
  showAdvanceFilter.value = false;
};

const confirm = (filterkey) => confirmCancel(filterkey, "confirm");

const cancel = (filterkey) => confirmCancel(filterkey, "cancel");


const computedValue = computed({
  get: () => props.modelValue,
  set: (value) => emit("update:modelValue", value),
});

const keydownHandler = (e) => {
  emit("formSubmit")
}
function getColumn(key) {
  let foundcoulmn = null;
  props.columns.forEach(element => {
    if (element.key == key) {
      foundcoulmn = element.extra;
      return;
    }
  });
  return foundcoulmn;
}

function setFilterValue(key, val) {
  var d = new Date(val),
    month = (d.getMonth() + 1),
    day = d.getDate(),
    year = d.getFullYear();
  computedValue.value[key] = val ? year + '-' + month + '-' + day : '';
}

const showAdvanceFilterModal = async (filterKey, keyOptions, resourceName) => {
  isAdvanceFilterActive.value = filterKey; 
  showAdvanceFilter.value = (showAdvanceFilter.value == true)? false: true;
  selectedValue.value = [];
console.log("resourceName",resourceName)
    if(filterKey =="last_edited"){
      selectOptions.value = keyOptions;
    }else{
      let response = await axios.get(route(`${resourceName}.fieldData`) + '?fieldName=' + filterKey);
      selectOptions.value = response.data.data;
    }
    let datesColums = ['dob_kid','dob_old_kid','start_date','dob_young_kid','created_at'];
    //Date values update    
    let prevFieldValue;   
    if(datesColums.includes(filterKey)){
      prevFieldValue = computedValue.value[`${filterKey}_start`]
    }
    else{   
      prevFieldValue = computedValue.value[filterKey]
    }
  
  const selectedValuesArray = prevFieldValue ? ((typeof prevFieldValue == 'object') ? (prevFieldValue.id ? prevFieldValue.id.split(',') : []) : prevFieldValue.split(',')) : []
  const filteredArray = selectOptions.value.filter(function(itm){
    return selectedValuesArray.indexOf(String(itm.id)) > -1;
  });
  selectedValue.value = filteredArray
  //openMultiSelect(); // open multiselect by default
  
}

function openMultiSelect(){
  $('.multiselect').addClass('multiselect--active dark:bg-slate-800');
  $('.multiselect__content-wrapper').css({"display":"block"});
  $('.multiselect__input').css({"width":"100%"});
}

function selectDeselect(filterKey, modeValue){
    // select all / clear all
    selectedValue.value = (modeValue == 'select') ? selectOptions.value : [];
}

</script>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<style>
.advance-filter {
  position: absolute;
  top: 7px;
  right: 0;
  cursor: pointer;
}
.advance-filter img{
  height: 25px;
}
.dark .advance-filter img{
  filter: grayscale(1) invert(1);
}
.filter-field.can-access input, 
.filter-field.can-access select{
  width: 90%
}
.advance-filter-modal {
    border: 1px solid black;
    z-index: 999999;
    background: white;
    padding: 24px;
    max-width: 480px;
    width: 100%;
    position: absolute;
}
.dark .multiselect__tags{  
  --tw-bg-opacity: 1;
    background-color: rgb(30 41 59 / var(--tw-bg-opacity));

}

select[name="filter-options"]{
  margin-top:20px !important;
  max-width: 300px !important;
  margin-bottom: 50px !important;
}
.multiselect{
  margin-top:20px;
  margin-bottom:20px;
}
.custom-filters {
    display: flex;
    flex-direction: column !important;
}
.custom-filters .vuepicker-demo .dp__main.dp__theme_light {
    margin-bottom: 8px;
}

.custom-filters .vuepicker-demo .dp__main.dp__theme_light svg.dp__icon.dp__clear_icon.dp__input_icons {
    right: 6px;
    width: 16px;
    height: 16px;
    color: #000;
}
.dark .custom-filters .vuepicker-demo .dp__main.dp__theme_light svg.dp__icon.dp__clear_icon.dp__input_icons {
    color: #fff;
}
.Filter-and-Search > div {
    /* max-height: 92vh;
    overflow-y: unset;
    overflow-x: visible; */
    padding: 20px;
}
.multiselect-tags-wrapper .multiselect__tags {
  max-height: 170px;
  overflow: auto;
  overflow-x: hidden;
}
</style>

