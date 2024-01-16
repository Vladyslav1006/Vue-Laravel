<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import {
  mdiViewDashboard,
} from "@mdi/js";
import SectionMain from "@/components/SectionMain.vue";
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
const user_rol_name = usePage().props.auth.user.name + ' Dashboard';

</script>

<template>
  <LayoutAuthenticated>

    <Head title="Dashboard" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiViewDashboard" :title="user_rol_name" main>
        &nbsp;
      </SectionTitleLineWithButton>
    </SectionMain>
  </LayoutAuthenticated>
</template>
