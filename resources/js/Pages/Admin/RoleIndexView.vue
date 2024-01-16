<script setup>
import { Head } from '@inertiajs/vue3';
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import {
    mdiPackageVariantPlus,
    mdiAlert,
    mdiAccountGroup,
    mdiFileEdit,
    mdiTrashCan,
} from "@mdi/js";
import SectionMain from "@/components/SectionMain.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import BaseButton from "@/components/BaseButton.vue";

import CardBox from "@/components/CardBox.vue";
import NotificationBar from "@/components/NotificationBar.vue";
import Table from "@/components/DataTable/Table.vue";
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
const message = computed(() => usePage().props.flash.message)
const msg_type = computed(() => usePage().props.flash.msg_type ?? 'warning')
const props = defineProps({
    roles: {
        type: Object,
        default: () => ({}),
    },
    can: {
        type: Object,
        default: () => ({}),
    },
    resourceNeo: {
        type: Object,
        default: () => ({}),
    },
});
const fields = [['name', 'Name']];
const actions = ['c', 'r', 'u', 'd'];
</script>

<template>
    <LayoutAuthenticated>

        <Head title="Roles" />
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Roles" main>
                <div class="flex">
                    <Link :href="route('role.create')" v-if="can.all || can.role_create">
                    <BaseButton class="m-2" :icon="mdiPackageVariantPlus" color="success" rounded-full small
                        label="Add New" />
                    </Link>
                </div>
            </SectionTitleLineWithButton>
            <NotificationBar v-if="message" @closed="usePage().props.flash.message = ''" :color="msg_type" :icon="mdiAlert"
                :outline="true">
                {{ message }}
            </NotificationBar>
            <CardBox has-table>
                <Table :resource="roles" :resourceNeo="resourceNeo">
                    <template #cell(actions)="{ item: role }">
                        <Link :href="route('role.edit', role.id)" class="-mb-3 mr-2" v-if="(actions.indexOf('u') !== -1) &&
                            (props.can.all || props.can['role_edit'])">
                        <BaseButton color="info" :icon="mdiFileEdit" small />
                        </Link>
                        <BaseButton color="danger" :icon="mdiTrashCan" small
                            @click="delselect = role.id; isModalDangerActive = true"
                            v-if="(actions.indexOf('d') !== -1) && (props.can.all || props.can['role_delete'])" />
                    </template>
                </Table>

            </CardBox>
        </SectionMain>
    </LayoutAuthenticated>
</template>
