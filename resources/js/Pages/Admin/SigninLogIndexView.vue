<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import {
    mdiAlert,
    mdiAccountEye,
    mdiFileEdit,
    mdiTrashCan,
} from "@mdi/js";
import SectionMain from "@/components/SectionMain.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import BaseButton from "@/components/BaseButton.vue";

import CardBoxModal from "@/components/CardBoxModal.vue";
import CardBox from "@/components/CardBox.vue";
import NotificationBar from "@/components/NotificationBar.vue";
import { ref } from 'vue';
import Table from "@/components/DataTable/Table.vue";
import { formatedDate } from "@/helpers/helpers"
import { computed } from 'vue';
const message = computed(() => usePage().props.flash.message)
const msg_type = computed(() => usePage().props.flash.msg_type ?? 'warning')
const props = defineProps({
    signinlogs: {
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
        router.delete(route("signinlog.destroy", delselect.value), {
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

        <Head title="Signin Log" />
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiAccountEye" title="Signin Logs" main>
                &nbsp;
            </SectionTitleLineWithButton>
            <NotificationBar v-if="message" @closed="usePage().props.flash.message = ''" :color="msg_type" :icon="mdiAlert"
                :outline="true">
                {{ message }}
            </NotificationBar>
            <CardBox has-table>
                <Table :resource="signinlogs" :resourceNeo="resourceNeo" :stickyHeader=(!0)>
                    <template #cell(created_at)="{ item: signinlog }">
                        {{ formatedDate(signinlog.created_at) }}
                    </template>
                    <template #cell(actions)="{ item: signinlog }">
                        <Link :href="route('signinlog.edit', signinlog.id)" class="-mb-3 mr-2" v-if="(actions.indexOf('u') !== -1) &&
                            (props.can.all || props.can['signinlog_edit'])">
                        <BaseButton color="info" :icon="mdiFileEdit" small />
                        </Link>
                        <BaseButton color="danger" :icon="mdiTrashCan" small
                            @click="delselect = signinlog.id; isModalDangerActive = true"
                            v-if="(actions.indexOf('d') !== -1) && (props.can.all || props.can['signinlog_delete'])" />
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
