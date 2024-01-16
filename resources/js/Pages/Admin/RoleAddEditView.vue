<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import {
  mdiViewList,
  mdiAccountGroup,
} from "@mdi/js";
import SectionMain from "@/components/SectionMain.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import BaseButton from "@/components/BaseButton.vue";
import CardBox from "@/components/CardBox.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";

import FormCheckRadio from "@/components/FormCheckRadio.vue";



const props = defineProps({
  formdata: {
    type: Object,
    default: () => ({}),
  },
  allpermissions: {
    type: Object,
    default: () => ({}),
  },
});



const form = useForm({
  name: props.formdata.name,
  permission: props.allpermissions,
});

const updatechild = (param) => {
  const childObject = props.allpermissions[param].child;
  const keys = Object.keys(childObject).map(Number).sort((a, b) => a - b);
  for (let i = 0; i < keys.length; i++) {
    const key = keys[i];
    childObject[key][2] = props.allpermissions[param].sts;
  }
};



const submitform = () => {
  form.permission = props.allpermissions;
  if (props.formdata.id) {
    form.put(route('role.update', props.formdata.id));
  }
  else {
    form.post(route('role.store'));
  }
};
</script>
<template>
  <LayoutAuthenticated>

    <Head title="Roles" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Roles" main>
        <div class="flex">
          <Link :href="route('role.index')">
          <BaseButton class="m-2" :icon="mdiViewList" color="success" rounded-full small label="List Roles" />
          </Link>

        </div>
      </SectionTitleLineWithButton>
      <form @submit.prevent="submitform">
        <CardBox>
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <FormField label="Name" help="" :error="form.errors.name">
              <FormControl name="name" v-model="form.name" required />
            </FormField>
          </div>
        </CardBox>
        <h2 class="m-3">Modules</h2>
        <CardBox>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">


            <div v-for="(item, index) in allpermissions">
              <div class="flex justify-between mb-1">
                <div>{{ item.name }}</div>
                <div>
                  <FormCheckRadio type="switch" v-model="item.sts" name="index" @update:modelValue="updatechild(index)"
                    prelabel="All Permissions" input-value="0" />
                </div>
              </div>
              <div class="grid grid-cols-1 lg:grid-cols-2 bg-gray-50 dark:bg-slate-800 dark:text-slate-100 modules">
                <div v-if="Object.keys(item.child).length % 2 != 0" v-for="perm in item.child" class="  py-2 px-2  ">
                  <div class="flex items-start  ">
                    <input :id="'checkbox-' + perm[1]" type="checkbox" value="" class="rounded  "
                      style="height: 20px;width: 20px; margin-top: 2px;" v-model="perm[2]" :name="perm[1]" :value="0">
                    <label :for="'checkbox-' + perm[1]" class="ms-2   text-gray-900 dark:text-gray-300">
                      {{ perm[3] ?? perm[1] }}
                    </label>
                  </div>
                </div>
                <div v-else v-for="perm in item.child" class="py-2 px-2     ">
                  <div v-if="perm[3] != 'Activate Mod'" class="flex items-start ">
                    <input :id="'checkbox-' + perm[1]" type="checkbox" value="" class="rounded  "
                      style="height: 20px;width: 20px; margin-top: 2px;" v-model="perm[2]" :name="perm[1]" :value="0">
                    <label :for="'checkbox-' + perm[1]" class="ms-2   text-gray-900 dark:text-gray-300">
                      {{ perm[3] ?? perm[1] }}
                    </label>
                  </div>
                  <div v-else class="activate-mod pt-3 pb-2" style="background-color: rgb(249,250,251);width: 100%;">
                    <div class="flex items-start ps-2 ">
                      <input :id="'checkbox-' + perm[1]" type="checkbox" value="" class="rounded  "
                        style="height: 20px;width: 20px; margin-top: 2px;" v-model="perm[2]" :name="perm[1]" :value="0">
                      <label :for="'checkbox-' + perm[1]" class="ms-2   text-gray-900 dark:text-gray-300">
                        {{ perm[3] ?? perm[1] }}
                      </label>
                    </div>

                  </div>
                </div>
              </div>
            </div>
          </div>


        </CardBox>
        <div class="mt-4 flex">
          <BaseButton class="mr-2" type="submit" small color="info" :label="props.formdata.id ? 'Update' : 'Save'" />
          <Link :href="route('role.index')">
          <BaseButton type="reset" small color="info" outline label="Cancel" />
          </Link>
        </div>

      </form>
    </SectionMain>
  </LayoutAuthenticated>
</template>

<style scoped>
.modules {
  position: relative;

}

.activate-mod {
  position: absolute;
  left: 0;
  bottom: -35px;


}
</style>