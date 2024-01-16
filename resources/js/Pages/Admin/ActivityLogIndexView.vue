<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import {
    mdiAlert,
    mdiArchiveEye,
    mdiFileEdit,
    mdiTrashCan,
} from "@mdi/js";
import SectionMain from "@/components/SectionMain.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import BaseButton from "@/components/BaseButton.vue";

import CardBoxModal from "@/components/CardBoxModal.vue";
import CardBox from "@/components/CardBox.vue";
import NotificationBar from "@/components/NotificationBar.vue";
import { ref, onMounted } from 'vue';
import Table from "@/components/DataTable/Table.vue";

import { formatedDate } from "@/helpers/helpers"

import { computed } from 'vue';


const message = computed(() => usePage().props.flash.message)
const props = defineProps({
    activitylogs: {
        type: Object,
        default: () => ({}),
    },
    allmodules: {
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

const actions = ['d', 'ex'];

const delselect = ref(0);
const isModalDangerActive = ref(false);

const deletePage = () => {
    if (delselect.value != 0) {
        router.delete(route("activitylog.destroy", delselect.value), {
            preserveScroll: true,
            resetOnSuccess: false,
            onFinish: () => {
                delselect.value = 0;
            }
        });
    }
}



</script>

<template>
    <LayoutAuthenticated>

        <Head title="Activity Log" />
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiArchiveEye" title="Activity Logs" main>
                &nbsp;
            </SectionTitleLineWithButton>
            <NotificationBar v-if="message" @closed="usePage().props.flash.message = ''" color="warning" :icon="mdiAlert"
                :outline="true">
                {{ message }}
            </NotificationBar>
            <CardBox has-table>
                <Table :resourceNeo="resourceNeo" :resource="activitylogs" :stickyHeader=(!0)>
                    <template #cell(created_at)="{ item: activitylog }">
                        {{ formatedDate(activitylog.created_at) }}
                    </template>
                    <template #cell(module)="{ item: activitylog }">
                        {{ allmodules[activitylog.module] }}
                    </template>


                    <template #cell(actions)="{ item: activitylog }">
                        <Link :href="route('activitylog.edit', activitylog.id)" class="-mb-3 mr-2" v-if="(actions.indexOf('u') !== -1) &&
                            (props.can.all || props.can['activitylog_edit'])">
                        <BaseButton color="info" :icon="mdiFileEdit" small />
                        </Link>
                        <BaseButton color="danger" :icon="mdiTrashCan" small
                            @click="delselect = activitylog.id; isModalDangerActive = true"
                            v-if="(actions.indexOf('d') !== -1) && (props.can.all || props.can['activitylog_delete'])" />
                    </template>
                </Table>
            </CardBox>
        </SectionMain>
        <CardBoxModal v-model="isModalDangerActive" buttonLabel="Confirm" title="Please confirm" button="danger" has-cancel
            @confirm="deletePage">
            <p>Are you sure to delete?</p>
        </CardBoxModal>
    </LayoutAuthenticated>
</template>
