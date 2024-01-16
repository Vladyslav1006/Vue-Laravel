<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import {
    mdiAlert,
    mdiFileEdit,
    mdiTrashCan,
    mdiContentCopy,
    mdiCancel, mdiCheckAll, mdiEyeCircleOutline, mdiAccountMultiplePlus
} from "@mdi/js";
import SectionMain from "@/components/SectionMain.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import BaseButton from "@/components/BaseButton.vue";
import BaseIcon from "@/components/BaseIcon.vue";
import CardBoxModal from "@/components/CardBoxModal.vue";
import CardBox from "@/components/CardBox.vue";

import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import NotificationBar from "@/components/NotificationBar.vue";
import { ref } from 'vue';
import Table from "@/components/DataTable/Table.vue";

import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';

import { formatedDate, extractDate, extractTime } from "@/helpers/helpers"

import { computed } from 'vue';
const message = computed(() => usePage().props.flash.message)
const msg_type = computed(() => usePage().props.flash.msg_type ?? 'warning')
const rData = computed(() => usePage().props.flash.rData)
const computednx = computed(() => {
    let x = [];
    for (let i = 1; i < 100; i++) {
        x.push(i);
    }
    return x;
})
const props = defineProps({
    moduledatas: {
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
    public_holidays:{
        type: Object,
        default: () => ({}),
    }
});

const actions = ['d', 'ex'];

const delselect = ref(0);
const isModalDangerActive = ref(false);
const isModalAcceptsActive = ref(false);
const isModalRejectsActive = ref(false);
const isModalDuplicateActive = ref(false);
const deletePage = () => {
    if (delselect.value != 0) {
        router.delete(route("bbapplicant.destroy", delselect.value), {
            preserveScroll: true,
            resetOnSuccess: false,
            onFinish: () => {
                delselect.value = 0;
            }
        });
    }
}

const selectedRowsele = ref([]);
const selectedRows = (event) => {
    selectedRowsele.value = event;
}

const vueDataTable = ref(null);
const indAccept = (id) => {
    indiRowsele.value = id;
    isModalAcceptsActive.value = true
}
const acceptJobs = () => {
    if (indiRowsele.value != '') {
        router.post(route(props.resourceNeo.resourceName + ".bulkAccept"), { ids: [indiRowsele.value] })
    }
    else {
        router.post(
            route(props.resourceNeo.resourceName + ".bulkAccept"),
            { ids: selectedRowsele.value },
            /*{
                onFinish: (page) => {
                    console.log(rData.value, msg_type.value, message.value)
                    copyToClipboard(rData.value.toString())
                }
            }*/
        )
    }

    selectedRowsele.value = [];
    indiRowsele.value = '';
    vueDataTable.value.resetSelect();
}
const rejectJobs = () => {

    if (indiRowsele.value != '') {
        router.post(route(props.resourceNeo.resourceName + ".bulkReject"), { ids: [indiRowsele.value] })
    }
    else {
        router.post(route(props.resourceNeo.resourceName + ".bulkReject"), { ids: selectedRowsele.value })
    }
    vueDataTable.value.resetSelect();

}
const indReject = (id) => {
    indiRowsele.value = id;
    isModalRejectsActive.value = true
}
const indiRowsele = ref('');
const nx = ref(1);
const indDuplicate = (id) => {
    indiRowsele.value = id;
    isModalDuplicateActive.value = true
    nx.value = 1;
}
const bulkDuplicate = () => {
    if (indiRowsele.value != '') {
        router.post(route(props.resourceNeo.resourceName + ".bulkDuplicate"), { ids: [indiRowsele.value], nx: nx.value })
    }
    else {
        router.post(route(props.resourceNeo.resourceName + ".bulkDuplicate"), { ids: selectedRowsele.value, nx: nx.value })
    }
    selectedRowsele.value = [];
    indiRowsele.value = '';
    vueDataTable.value.resetSelect();
}
const showHideAllValue = ref(false);
const showHideAll = () => {
    vueDataTable.value.showHideAll(showHideAllValue.value);
    showHideAllValue.value = !showHideAllValue.value;
}

const selemailStr = computed(() => {
    let selemail = [];
    let cellref = '';
    let el;
    if (indiRowsele.value != '') {
        cellref = 't' + indiRowsele.value;
        el = document.getElementById(cellref.toUpperCase());
        selemail.push(el.getAttribute("dataval"));
    }
    else {
        selectedRowsele.value.forEach(element => {
            cellref = 't' + element;
            el = document.getElementById(cellref.toUpperCase());
            selemail.push(el.getAttribute("dataval"));
        });
    }
    return selemail;
});

const copyemailaddress = () => {

    copyToClipboard(selemailStr.value.join('; '));
    useToast().info('Emails copied to clipboard!!');

}
function copyToClipboard(text) {
    var dummy = document.createElement("textarea");
    document.body.appendChild(dummy);
    dummy.value = text;
    dummy.select();
    document.execCommand("copy");
    document.body.removeChild(dummy);
}
function isHolidayDateEqual(dateToCheck) {
   // console.log("dateToCheck"+dateToCheck)
    const parsedDateToCheck = new Date(extractDate(dateToCheck));
    for (const holiday of props.public_holidays) {
        const parsedHolidayDate = new Date(holiday.holiday_date);
        if (parsedDateToCheck.getTime() === parsedHolidayDate.getTime()) {
            return "yes";
        }
    }
    return "no";
}


</script>

<template>
    <LayoutAuthenticated>

        <Head title="New BB Applicants" />
        <SectionMain class="!py-0">
            <SectionTitleLineWithButton :icon="mdiAccountMultiplePlus" title="New BB Applicants" main class="!mb-1">
                &nbsp;
            </SectionTitleLineWithButton>
            <NotificationBar v-if="(!1)" @closed="usePage().props.flash.message = ''" :color="msg_type" :icon="mdiAlert"
                :outline="true">
                {{ message }}
            </NotificationBar>
            <CardBox has-table>
                <Table :resourceNeo="resourceNeo" :resource="moduledatas" @selectedRows="selectedRows($event)"
                    ref="vueDataTable" :multipleSort=(!0) :advanceSort=(!0) :hideSearchColumn=(!0) :hideFiltersColumn=(!0)
                    :popupSearch=(!0) :stickyHeader=(!0) :colHeader=(!0) headerColor='bg-customCayn'
                    :seleLimit="((!props.can.all && props.can['bbapplicant_limCheckSelect']) ? Number(resourceNeo['max-checkbox-select']) : 0)"
                    :enterKeyToEdit="(props.can.all || props.can['bbapplicant_entertoedit'])">
                    <template #customButtons>
                        <div class="order-8 sm:order-2 mx-2 pt-1">
                            <button type="button" :disabled="!selectedRowsele.length" @click="isModalAcceptsActive = true"
                                class="w-full border border-gray-200 dark:border-slate-700   rounded-md shadow-sm px-2 py-2 inline-flex justify-center text-sm font-medium hover:border-gray-600 hover:dark:border-slate-100 border-gray-300"
                                title="Accept" :class="{
                                    'bg-gray-200 dark:bg-slate-700 text-gray-400': !selectedRowsele.length,
                                    'bg-blue-500 dark:bg-blue-500 text-gray-100': selectedRowsele.length,
                                }
                                    ">
                                <BaseIcon :path="mdiCheckAll" title="Accept" h="h-5" />
                                <span>Validate</span>
                            </button>
                        </div>
                        <div class="order-9 sm:order-2  mx-2 pt-1">
                            <button type="button" @click="isModalRejectsActive = true" :disabled="!selectedRowsele.length"
                                class="w-full border border-gray-200 dark:border-slate-700   rounded-md shadow-sm px-2 py-2 inline-flex justify-center text-sm font-medium hover:border-gray-600 hover:dark:border-slate-100 border-gray-300"
                                title="Reject" :class="{
                                    'bg-gray-200 dark:bg-slate-700 text-gray-400': !selectedRowsele.length,
                                    'bg-red-600 dark:bg-red-500 text-gray-100': selectedRowsele.length,
                                }
                                    ">
                                <BaseIcon :path="mdiCancel" title="Reject" h="h-5" />
                                <span>Reject</span>
                            </button>

                        </div>


                        <div class="order-9 sm:order-2 mx-2 pt-1"
                            v-if="(props.can.all || props.can['bbapplicant_duplicate'])">
                            <button type="button" :disabled="!selectedRowsele.length"
                                @click="isModalDuplicateActive = true; nx = 1"
                                class="w-full border border-gray-200 dark:border-slate-700   rounded-md shadow-sm px-2 py-2 inline-flex justify-center text-sm font-medium hover:border-gray-600 hover:dark:border-slate-100 border-gray-300"
                                title="Duplicate" :class="{
                                    'bg-gray-200 dark:bg-slate-700 text-gray-400': !selectedRowsele.length,
                                    'bg-purple-400 dark:bg-purple-400 text-gray-100': selectedRowsele.length,
                                }
                                    ">
                                <BaseIcon :path="mdiContentCopy" title="Duplicate" h="h-5" />
                                <span>Duplicate</span>
                            </button>
                        </div>
                        <div class=" order-7 sm:order-7 mx-2 pt-1"><!--flex  flex-row w-full sm:w-auto  sm:flex-grow-->
                            <button type="button" @click="showHideAll()"
                                class="w-full border border-gray-200 dark:border-slate-700   rounded-md shadow-sm px-2 py-2 inline-flex justify-center text-sm font-medium hover:border-gray-600 hover:dark:border-slate-100 border-gray-300 bg-gray-400 dark:bg-slate-500 text-white-400"
                                title="Click to Fully Expand/Collapse all COLUMNS.">
                                <BaseIcon :path="mdiEyeCircleOutline" title="Click to Fully Expand/Collapse all COLUMNS."
                                    h="h-5" />
                                <span>Exp/Collap</span>
                            </button>

                        </div>
                    </template>

                    <template #cell(s-n)="{ skey: sskey }">
                        {{ ((props.moduledatas.per_page ?? 1000) * ((props.moduledatas.current_page ?? 1) - 1)) + sskey + 1
                        }}
                    </template>

<!--                    <template #cell(BUniqueID)="{ item: moduledata }">-->
<!--                        <span :class="{ 'highlight-row': isHolidayDateEqual(moduledata.created_at)=='yes' }">{{moduledata.BUniqueID }}</span>-->
<!--                    </template>-->
<!--                    <template #cell(BUNO)="{ item: moduledata }">-->
<!--                        <span :class="{ 'highlight-row': isHolidayDateEqual(moduledata.created_at)=='yes' }">{{moduledata.BUNO }}</span>-->
<!--                    </template>-->
<!--                    <template #cell(full_bio)="{ item: moduledata }">-->
<!--                        <span :class="{ 'highlight-row': isHolidayDateEqual(moduledata.created_at)=='yes' }">{{moduledata.full_bio }}</span>-->
<!--                    </template>-->

                    <template #cell(date)="{ item: moduledata }">
                      {{ extractDate(moduledata.created_at) }}
                    </template>
                    <template #cell(time)="{ item: moduledata }">
                        {{ extractTime(moduledata.created_at) }}
                    </template>
                    <template #cell(review)="{ item: moduledata }">
                        <div class="h-16 flex items-center p-1 -m-1" v-if="moduledata.review">
                            <a :href="moduledata.review" target="_blank"
                                class="font-bold dark:text-orange-400 text-orange-800">Review</a>
                        </div>
                    </template>
                    <template #cell(aceptdate)="{ item: moduledata }">

                        <span v-if="moduledata.aceptdate == null"
                            class="dark:text-orange-400 text-orange-800">no-email-yet</span>
                        <span v-else-if="moduledata.aceptdate.toLowerCase() == 'rej'" class="text-red-400">{{
                            moduledata.aceptdate }}</span>
                        <span v-else class="font-bold dark:text-green-400 text-green-800">{{
                            moduledata.aceptdate }}</span>
                    </template>


                    <template #cell(created_at)="{ item: moduledata }">
                       {{ formatedDate(moduledata.created_at) }}
                    </template>

                    <template #cell(last_edited)="{ item: moduledata }">
                        {{ moduledata.last_edit_name }}
                    </template>

                    <template #cell(actions)="{ item: moduledata }">
                        <div class="flex flex-row gap-1">
                            <Link :href="route('bbapplicant.edit', moduledata.id)" class="-mb-3 mr-2" v-if="(actions.indexOf('u') !== -1) &&
                                (props.can.all || props.can['bbapplicant_edit'])
                                ">
                            <BaseButton color="info" :icon="mdiFileEdit" small />
                            </Link>

                            <BaseButton color="danger" :icon="mdiTrashCan" small title="Delete"
                                @click="delselect = moduledata.id; isModalDangerActive = true"
                                v-if="(actions.indexOf('d') !== -1) && (props.can.all || props.can['bbapplicant_delete'])" />
                            <BaseButton color="info"
                                class="bg-purple-400 dark:bg-purple-400 border-purple-400 dark:border-purple-400 hover:bg-purple-500 hover:border-purple-500 hover:dark:bg-purple-500 hover:dark:border-purple-500"
                                :icon="mdiContentCopy" small title="Duplicate" @click="indDuplicate(moduledata.id)"
                                v-if="(props.can.all || props.can['bbapplicant_duplicate'])" />
                            <BaseButton color="info" :icon="mdiCheckAll" small title="Accept"
                                @click="indAccept(moduledata.id)" />
                            <BaseButton color="danger" :icon="mdiCancel" small title="Reject"
                                @click="indReject(moduledata.id)" />
                        </div>
                    </template>
                </Table>
            </CardBox>
        </SectionMain>
        <CardBoxModal v-model="isModalDangerActive" buttonLabel="Confirm" title="Please confirm" button="danger" has-cancel
            @confirm="deletePage">
            <p>Are you sure to delete?</p>
        </CardBoxModal>

        <CardBoxModal v-model="isModalAcceptsActive" buttonLabel="Confirm" title="Please confirm" button="info" has-cancel
            @confirm="acceptJobs" @cancel="indiRowsele = ''">
            <p>Confirm to send out email<span class="dark:text-green-400 text-blue-900">({{ selemailStr.length }})</span>??
            </p>
            <p>{{ selemailStr.join('; ') }}</p>
            <div>
                <BaseButton class="float-right" label="Click here to copy all selected email addresses" color="success"
                    @click="copyemailaddress" />
            </div>


        </CardBoxModal>
        <CardBoxModal v-model="isModalDuplicateActive" buttonLabel="Confirm" title="Please confirm" button="info" has-cancel
            @confirm="bulkDuplicate" @cancel="indiRowsele = ''">
            <p>Are you sure you want to duplicate the SELECTED BB Applicants<span
                    class="dark:text-green-400 text-blue-900">({{
                        selemailStr.length }})</span>?</p>
            <p>{{ selemailStr.join('; ') }}</p>
            <FormField label="Number of times">
                <FormControl v-model="nx" :options="computednx" />
            </FormField>

        </CardBoxModal>
        <CardBoxModal v-model="isModalRejectsActive" buttonLabel="Confirm" title="Please confirm" button="info" has-cancel
            @confirm="rejectJobs" @cancel="indiRowsele = ''">
            <p>Are you sure to Reject these Applicant<span class="dark:text-green-400 text-blue-900">({{ selemailStr.length
            }})</span>?</p>
            <p>{{ selemailStr.join('; ') }}</p>
        </CardBoxModal>
    </LayoutAuthenticated>
</template>

<style scoped>

.highlight-row {
    font-weight: bold;
    color: #ffa500;
}
</style>
