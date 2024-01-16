<script setup>
import { Head } from '@inertiajs/vue3';
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import {
    mdiImage,
    mdiAlert,
    mdiPackage,
} from "@mdi/js";
import SectionMain from "@/components/SectionMain.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import BaseButton from "@/components/BaseButton.vue";

import CardBox from "@/components/CardBox.vue";
import NotificationBar from "@/components/NotificationBar.vue";
import ListTableSimple from "@/components/ListTableSimple.vue";
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
const message = computed(() => usePage().props.flash.message)
const props = defineProps({
    permissions: {
        type: Array,
        default: () => ([]),
    },
    can: {
        type: Object,
        default: () => ({}),
    },
})
const fields = [['name', 'Name'], ['perm_label', 'Label']];
const actions = ['c', 'u', 'r', 'd'];
</script>

<template>
    <LayoutAuthenticated>

        <Head title="Permissions" />
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiPackage" title="Permissions" main>
                <div class="flex">
                    <Link :href="route('permission.create')" v-if="can.all || can.permission_create">
                    <BaseButton class="m-2" :icon="mdiImage" color="success" rounded-full small label="Add New" />
                    </Link>
                </div>
            </SectionTitleLineWithButton>
            <NotificationBar v-if="message" @closed="usePage().props.flash.message = ''" color="warning" :icon="mdiAlert"
                :outline="true">
                {{ message }}
            </NotificationBar>
            <CardBox has-table>
                <ListTableSimple :listData="permissions" resource="permission" :fields="fields" :actions="actions" />
            </CardBox>
        </SectionMain>
    </LayoutAuthenticated>
</template>
