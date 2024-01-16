<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from "vue";
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import {
  mdiImage,
  mdiPackage,
} from "@mdi/js";
import SectionMain from "@/components/SectionMain.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import BaseButton from "@/components/BaseButton.vue";
import CardBox from "@/components/CardBox.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";

const props = defineProps({
  formdata: {
    type: Object,
    default: () => ({}),
  },
  roles: {
    type: Object,
    default: () => ({}),
  },
});


const form = useForm({
  name: props.formdata.name,
  perm_label: props.formdata.perm_label,
});

const submitform = () => {
  if (props.formdata.id) {
    form.put(route('permission.update', props.formdata.id));
  }
  else {
    form.post(route('permission.store'));
  }
};
</script>
<template>
  <LayoutAuthenticated>

    <Head title="Permissions" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiPackage" title="Permissions" main>
        <div class="flex">
          <Link :href="route('permission.index')">
          <BaseButton class="m-2" :icon="mdiImage" color="success" rounded-full small label="List Permissions" />
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
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <FormField label="Label" help="" :error="form.errors.perm_label">
              <FormControl name="perm_label" v-model="form.perm_label" />
            </FormField>
          </div>
        </CardBox>

        <div class="mt-4 flex">
          <BaseButton class="mr-2" type="submit" small color="info" :label="props.formdata.id ? 'Update' : 'Save'" />
          <Link :href="route('permission.index')">
          <BaseButton type="reset" small color="info" outline label="Cancel" />
          </Link>
        </div>

      </form>
    </SectionMain>
  </LayoutAuthenticated>
</template>
