<script setup>
import {Head, Link, usePage, router} from '@inertiajs/vue3';
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import {
    mdiPackageVariantPlus,
    mdiAlert,
    mdiAccountBoxMultiple,
    mdiFileEdit,
    mdiTrashCan,
} from "@mdi/js";
import SectionMain from "@/components/SectionMain.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import BaseButton from "@/components/BaseButton.vue";

import CardBoxModal from "@/components/CardBoxModal.vue";
import CardBox from "@/components/CardBox.vue";
import NotificationBar from "@/components/NotificationBar.vue";

import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import {ref} from 'vue';
import Table from "@/components/DataTable/Table.vue";

import {computed} from 'vue';

const message = computed(() => usePage().props.flash.message)
const msg_type = computed(() => usePage().props.flash.msg_type ?? 'warning')
const computednx = computed(() => {
    let x = [];
    for (let i = 1; i < 100; i++) {
        x.push(i);
    }
    return x;
})

const props = defineProps({
    public_holidays: {
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
    }
});

const actions = ['c', 'r', 'u', 'd'];

const delselect = ref(0);
const import_file = ref('');
const isModalDangerActive = ref(false);
const isModalDuplicateActive = ref(false);
const isModalImportActive = ref(false);

const selectedRowsele = ref([]);
const selectedRows = (event) => {
    selectedRowsele.value = event;
}
const indiRowsele = ref('');
const nx = ref(1);
const indDuplicate = (id) => {
    indiRowsele.value = id;
    isModalDuplicateActive.value = true
    nx.value = 1;

}
const vueDataTable = ref(null);
const bulkDuplicate = () => {
    if (indiRowsele.value != '') {
        router.post(route(props.resourceNeo.resourceName + ".bulkDuplicate"), {ids: [indiRowsele.value], nx: nx.value})
    } else {
        router.post(route(props.resourceNeo.resourceName + ".bulkDuplicate"), {
            ids: selectedRowsele.value,
            nx: nx.value
        })
    }
    selectedRowsele.value = [];
    indiRowsele.value = '';
    vueDataTable.value.resetSelect();
}

const deletePublicHoliday = () => {
    if (delselect.value != 0) {
        router.delete(route("public-holidays.destroy", {id: delselect.value}), {
            preserveScroll: true,
            resetOnSuccess: false,
            onFinish: () => {
                authPass.value = '';
                delselect.value = 0;
            }
        });
    }
}

const saveExportActivityLog = () => {
    router.get(route(props.resourceNeo.resourceName + ".saveExportActivityLog", { ids: selectedRowsele.value }), {});
}

</script>

<template>
    <LayoutAuthenticated>

        <Head title="Public Holidays"/>
        <SectionMain class="!py-0">
            <SectionTitleLineWithButton :icon="mdiAccountBoxMultiple" title="Public Holidays" main class="!mb-1">

                <div class="flex">
                    <Link :href="route('public-holidays.create')" v-if="can.all || can.publicholiday_create">
                        <BaseButton class="m-2" :icon="mdiPackageVariantPlus" color="success" rounded-full small
                                    label="Add New"/>
                    </Link>
                    <Link :href="route('public-holidays.import-file')" v-if="can.all">
                        <BaseButton class="m-2" :icon="mdiPackageVariantPlus" color="success" rounded-full small
                                    label="Import File"/>
                    </Link>
                </div>
            </SectionTitleLineWithButton>
            <NotificationBar v-if="message" @closed="usePage().props.flash.message = ''" :color="msg_type"
                             :icon="mdiAlert"
                             :outline="true">
                {{ message }}
            </NotificationBar>
            <CardBox has-table>
                <Table :resource="public_holidays" :resourceNeo="resourceNeo" @selectedRows="selectedRows($event)"
                :stickyHeader=(!0)
                       :colHeader=(!0)
                       ref="vueDataTable" headerColor='bg-customCayn' @saveExportActivityLog="saveExportActivityLog">

                    <template #customButtons>
                        <div class="order-9 sm:order-2 mx-2 pt-1"
                             v-if="(props.can.all)">
                            <button type="button" :disabled="!selectedRowsele.length"
                                    @click="isModalDuplicateActive = true; nx = 1"
                                    class="w-full border border-gray-200 dark:border-slate-700   rounded-md shadow-sm px-2 py-2 justify-center text-sm font-medium hover:border-gray-600 hover:dark:border-slate-100 border-gray-300"
                                    title="Duplicate" :class="{
                                    'bg-gray-200 dark:bg-slate-700 text-gray-400': !selectedRowsele.length,
                                    'bg-purple-400 dark:bg-purple-400 text-gray-100': selectedRowsele.length,
                                }
                                    ">
                                <BaseIcon :path="mdiContentCopy" title="Duplicate" h="h-5"/>
                                <span>Duplicate</span>
                            </button>
                        </div>

                    </template>

                    <template #cell(actions)="{ item: single_ph }">
                        <Link :href="route('public-holidays.edit', single_ph.id)" class="-mb-3 mr-2" v-if="(actions.indexOf('u') !== -1) &&
                            (props.can.all || props.can['publicholiday_edit'])">
                            <BaseButton color="info" :icon="mdiFileEdit" small/>
                        </Link>
                        <BaseButton color="danger" :icon="mdiTrashCan" small
                                    @click="delselect = single_ph.id; isModalDangerActive = true"
                                    v-if="(actions.indexOf('d') !== -1) && (props.can.all || props.can['publicholiday_delete'])"/>
                    </template>
                </Table>
            </CardBox>
        </SectionMain>
        <CardBoxModal v-model="isModalDangerActive" @confirm="deletePublicHoliday" buttonLabel="Confirm"
                      title="Please confirm" button="danger" has-cancel>
            <p>Are you sure to delete?</p>
        </CardBoxModal>
        <CardBoxModal v-model="isModalDuplicateActive" buttonLabel="Confirm" title="Please confirm" button="info"
                      has-cancel
                      @confirm="bulkDuplicate" @cancel="indiRowsele = ''">
            <p>Are you sure you want to duplicate the SELECTED Record</p>
            <FormField label="Number of times">
                <FormControl v-model="nx" :options="computednx"/>
            </FormField>
        </CardBoxModal>

    </LayoutAuthenticated>
</template>
