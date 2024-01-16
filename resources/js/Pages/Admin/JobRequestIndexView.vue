<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import {
    mdiAlert,
    mdiArrowDecision,
    mdiFileEdit,
    mdiTrashCan,
    mdiContentCopy,
    mdiAccountPlusOutline, mdiSwapHorizontal, mdiCancel, mdiCheckAll, mdiEyeCircleOutline
} from "@mdi/js";
import SectionMain from "@/components/SectionMain.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import BaseButton from "@/components/BaseButton.vue";
import BaseIcon from "@/components/BaseIcon.vue";
import CardBoxModal from "@/components/CardBoxModal.vue";
import CardBox from "@/components/CardBox.vue";
import NotificationBar from "@/components/NotificationBar.vue";
import { ref, onMounted } from 'vue';
import Table from "@/components/DataTable/Table.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import { formatedDate, extractDate, extractTime } from "@/helpers/helpers"
import axios from "axios";

import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import { computed } from 'vue';

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
    public_holidays: {
        type: Object,
        default: () => ({}),
    }
});

const actions = ['d', 'ex'];

const delselect = ref(0);
const isModalDangerActive = ref(false);
const isModalCRDRActive = ref(false);
const isModalAcceptsActive = ref(false);
const isModalRejectsActive = ref(false);
const isModalDuplicateActive = ref(false);
const deletePage = () => {
    if (delselect.value != 0) {
        router.delete(route("jobrequest.destroy", delselect.value), {
            preserveScroll: true,
            resetOnSuccess: false,
            onFinish: () => {
                delselect.value = 0;
            }
        });
    }
}

const assignHimself = (jobid) => {
    router.post(route(props.resourceNeo.resourceName + ".assignCRDR"),
        {
            id: jobid,
            key: 'CRDR1',
            ref: 'K' + jobid,
            value: usePage().props.auth.user.id
        })
}
const changeCRDRModel = ref('');
const changeCRDRReasonModel = ref('');
const changeCRDRTitle = ref('');
const changeCRDRfield = ref('');
const changeCRDRChange = ref('false');
const changeCRDRJobid = ref('');
const crdrreasonempty = ref(false);
const crdrChangeShow = (jobid, userid, username, fieldname) => {
    changeCRDRTitle.value = 'Change ' + fieldname
    isModalCRDRActive.value = true;
    changeCRDRfield.value = fieldname
    changeCRDRJobid.value = jobid;
    changeCRDRChange.value = true;
    changeCRDRReasonModel.value = '';
    changeCRDRModel.value = { 'id': userid, 'label': username }
    crdrreasonempty.value = false;
    setTimeout(function () {
        poperselect.value.selectFocus()
    }, 100);
}

const crdr2Select = (jobid, userid, username) => {
    changeCRDRChange.value = false;
    changeCRDRTitle.value = 'Select CRDR2'
    isModalCRDRActive.value = true;
    changeCRDRfield.value = 'CRDR2'
    changeCRDRJobid.value = jobid;
    changeCRDRReasonModel.value = '';
    changeCRDRModel.value = { 'id': userid, 'label': username }
    setTimeout(function () {
        poperselect.value.selectFocus()
    }, 100);
}

const crdr1Select = (jobid, userid, username) => {
    changeCRDRChange.value = false;
    changeCRDRTitle.value = 'Select CRDR1'
    isModalCRDRActive.value = true;
    changeCRDRfield.value = 'CRDR1'
    changeCRDRJobid.value = jobid;
    changeCRDRReasonModel.value = '';
    changeCRDRModel.value = { 'id': userid, 'label': username }
    setTimeout(function () {
        poperselect.value.selectFocus()
    }, 100);
}

const changeCrdr = () => {
    if (changeCRDRChange.value == true && changeCRDRReasonModel.value == '') {
        crdrreasonempty.value = true;
        isModalCRDRActive.value = true;
    } else {
        router.post(route(props.resourceNeo.resourceName + ".assignCRDR"),
            {
                id: changeCRDRJobid.value,
                key: changeCRDRfield.value,
                value: changeCRDRModel.value.id,
                ref: selectedCellsele.value,
                reason: changeCRDRReasonModel.value
            })
    }

}
const selectedRowsele = ref([]);
const selectedRows = (event) => {
    selectedRowsele.value = event;
}
const selectedCellsele = ref('');
const selectedCell = (event) => {
    selectedCellsele.value = event;
}
const rejectReasonModel = ref('');
const vueDataTable = ref(null);
const indAccept = (id) => {
    indiRowsele.value = id;
    isModalAcceptsActive.value = true
}
const isModalDuplicateClientActive = ref(false)
const duplicacyData = ref([])
const jobIDInAction = ref(0);
const ignoreDuplicateData = () => {
    let sdata = { ids: [jobIDInAction.value], ignore: true }
    axios.post(route(props.resourceNeo.resourceName + ".bulkAccept"), sdata)
        .then(response => {
            useToast().success(response.data.message, { duration: 7000 });
            isModalDuplicateClientActive.value = false;
            duplicacyData.value = [];
            jobIDInAction.value = 0;
            router.reload();
        });
}
const useThisCn = (id) => {
    let sdata = { ids: [jobIDInAction.value], ignore: true, smbjid: id }
    axios.post(route(props.resourceNeo.resourceName + ".bulkAccept"), sdata)
        .then(response => {
            useToast().success(response.data.message, { duration: 7000 });
            isModalDuplicateClientActive.value = false;
            duplicacyData.value = [];
            jobIDInAction.value = 0;
            router.reload();
        });

}
const propsd = ref({})
const acceptJobs = () => {
    let sdata = {};
    if (indiRowsele.value != '') {
        sdata = { ids: [indiRowsele.value] }
    } else {
        sdata = { ids: selectedRowsele.value }
    }

    axios.post(route(props.resourceNeo.resourceName + ".bulkAccept"), sdata)
        .then(response => {

            if (response.data.msg_type == 'warning') {
                useToast().warning(response.data.message, { duration: 12000 });
                router.reload();
            }
            else if (response.data.msg_type == 'success') {
                useToast().success(response.data.message, { duration: 7000 });
                router.reload();
            }
            else if (response.data.msg_type == 'danger') {
                useToast().error(response.data.message, { duration: 7000 });
            }
            else {
                useToast().info(response.data.message, { duration: 7000 });
            }
            if (response.data.rdata) {
                duplicacyData.value = response.data.rdata;
                isModalDuplicateClientActive.value = true;
                jobIDInAction.value = response.data.reqid;

                let cellref = response.data.reqid;

                propsd.value = { custname: document.getElementById('E' + cellref).getAttribute("dataval"), email: document.getElementById('H' + cellref).getAttribute("dataval"), addr: document.getElementById('AA' + cellref).getAttribute("dataval"), unitn: document.getElementById('AB' + cellref).getAttribute("dataval"), phn: document.getElementById('I' + cellref).getAttribute("dataval") }
            }

            selectedRowsele.value = [];
            indiRowsele.value = '';
            vueDataTable.value.resetSelect();
        });
}
const reasonempty = ref(false);
const rejectJobs = () => {
    if (rejectReasonModel.value == '') {
        reasonempty.value = true;
        isModalRejectsActive.value = true;
    } else {
        if (indiRowsele.value != '') {
            router.post(route(props.resourceNeo.resourceName + ".bulkReject"), {
                ids: [indiRowsele.value],
                rejectReason: rejectReasonModel.value
            })
        } else {
            router.post(route(props.resourceNeo.resourceName + ".bulkReject"), {
                ids: selectedRowsele.value,
                rejectReason: rejectReasonModel.value
            })
        }
        vueDataTable.value.resetSelect();
        reasonempty.value = false;
        rejectReasonModel.value = '';
    }

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
const showHideAllValue = ref(false);
const poperselect = ref(null);
const showHideAll = () => {
    vueDataTable.value.showHideAll(showHideAllValue.value);
    showHideAllValue.value = !showHideAllValue.value;
}

const selemailStr = computed(() => {
    let selemail = [];
    let cellref = '';
    let el;
    if (indiRowsele.value != '') {
        cellref = 'h' + indiRowsele.value;
        el = document.getElementById(cellref.toUpperCase());
        selemail.push(el.getAttribute("dataval"));
    } else {
        selectedRowsele.value.forEach(element => {
            cellref = 'h' + element;
            el = document.getElementById(cellref.toUpperCase());
            selemail.push(el.getAttribute("dataval"));
        });
    }
    return selemail;
});

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
const setfocusback = () => {
    vueDataTable.value.focustoDisplayCell();
}
</script>

<template>
    <LayoutAuthenticated>

        <Head title="New BB Job Requests" />
        <SectionMain class="!py-0">
            <SectionTitleLineWithButton :icon="mdiArrowDecision" title="New BB Job Requests" main class="!mb-1">
                &nbsp;
            </SectionTitleLineWithButton>
            <NotificationBar v-if="(!1)" @closed="usePage().props.flash.message = ''" :color="msg_type" :icon="mdiAlert"
                :outline="true">
                {{ message }}
            </NotificationBar>
            <CardBox has-table>
                <Table :resourceNeo="resourceNeo" :resource="moduledatas" @selectedRows="selectedRows($event)"
                    @selectedCell="selectedCell($event)" ref="vueDataTable" :multipleSort=(!0) :advanceSort=(!0)
                    :hideSearchColumn=(!0) :hideFiltersColumn=(!0) :popupSearch=(!0) :stickyHeader=(!0) :colHeader=(!0)
                    :seleLimit="((!props.can.all && props.can['jobrequest_limCheckSelect']) ? Number(resourceNeo['max-checkbox-select']) : 0)"
                    headerColor='bg-customCayn' :enterKeyToEdit="(props.can.all || props.can['jobrequest_entertoedit'])">
                    <template #customButtons>
                        <div class="order-8 sm:order-2  mx-2 pt-1">
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
                        <div class="order-9 sm:order-2 mx-2 pt-1">
                            <button type="button" :disabled="!selectedRowsele.length" @click="isModalAcceptsActive = true"
                                class="w-full border border-gray-200 dark:border-slate-700   rounded-md shadow-sm px-2 py-2 inline-flex justify-center text-sm font-medium hover:border-gray-600 hover:dark:border-slate-100 border-gray-300"
                                title="Accept" :class="{
                                    'bg-gray-200 dark:bg-slate-700 text-gray-400': !selectedRowsele.length,
                                    'bg-blue-500 dark:bg-blue-500 text-gray-100': selectedRowsele.length,
                                }
                                    ">
                                <BaseIcon :path="mdiCheckAll" title="Accept" h="h-5" />
                                <span>Accept</span>
                            </button>
                        </div>

                        <div class="order-9 sm:order-2 mx-2 pt-1"
                            v-if="(props.can.all || props.can['jobrequest_duplicate'])">
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
                                <span>Expand/Collapse</span>
                            </button>

                        </div>
                    </template>
                    <template #cell(start_date)="{ item: moduledata }">
                        <span :class="{ 'highlight-row': isHolidayDateEqual(moduledata.start_date) == 'yes' }">{{
                            moduledata.start_date }}</span>
                    </template>
                    <template #cell(s-n)="{ skey: sskey }">
                        {{
                            ((props.moduledatas.per_page ?? 1000) * ((props.moduledatas.current_page ?? 1) - 1)) + sskey + 1
                        }}
                    </template>
                    <template #cell(date)="{ item: moduledata }">
                        {{ extractDate(moduledata.created_at) }}
                    </template>
                    <template #cell(time)="{ item: moduledata }">
                        {{ extractTime(moduledata.created_at) }}
                    </template>

                    <template #cell(crdr1_name)="{ item: moduledata }">
                        <div class="text-center h-5 overflow-hidden" v-if="moduledata.CRDR1">{{ moduledata.crdr1_name }}
                        </div>
                        <div v-if="moduledata.CRDR1 && (props.can.all || props.can['jobrequest_CRDRchange'])"
                            class="text-center">
                            <BaseButton color="info" :icon="mdiSwapHorizontal" small
                                @click="crdrChangeShow(moduledata.id, moduledata.CRDR1, moduledata.crdr1_name, 'CRDR1')"
                                title="Click to Change CRDR1" :id="`crdr1asn_${moduledata.id}`" />
                        </div>
                        <div class="text-center" v-if="!moduledata.CRDR1">
                            <BaseButton v-if="props.can.all" color="info" :icon="mdiAccountPlusOutline" small
                                @click="crdr1Select(moduledata.id, moduledata.CRDR1, moduledata.crdr1_name)"
                                title="Click to assign CRDR1" :id="`crdr1asn_${moduledata.id}`" />
                            <BaseButton v-else="props.can['jobrequest_edit']" color="info" :icon="mdiAccountPlusOutline"
                                small @click="assignHimself(moduledata.id)" title="Click to assign yourself as CRDR1"
                                :id="`crdr1asn_${moduledata.id}`" />
                        </div>
                    </template>

                    <template #cell(crdr2_name)="{ item: moduledata }">
                        <div class="text-center h-5 overflow-hidden" v-if="moduledata.CRDR2">{{ moduledata.crdr2_name }}
                        </div>
                        <div v-if="moduledata.CRDR2 && (props.can.all || props.can['jobrequest_edit'])" class="text-center">
                            <BaseButton color="info" :icon="mdiSwapHorizontal" small
                                @click="crdrChangeShow(moduledata.id, moduledata.CRDR2, moduledata.crdr2_name, 'CRDR2')"
                                title="Click to Change CRDR2" :id="`crdr2asn_${moduledata.id}`" />
                        </div>
                        <div class="text-center">
                            <BaseButton v-if="!moduledata.CRDR2 && (props.can.all || props.can['jobrequest_edit'])"
                                color="info" :icon="mdiAccountPlusOutline" small
                                @click="crdr2Select(moduledata.id, moduledata.CRDR2, moduledata.crdr2_name)"
                                title="Click to assign  CRDR2" :id="`crdr2asn_${moduledata.id}`" />
                        </div>
                    </template>

                    <template #cell(created_at)="{ item: moduledata }">
                        {{ formatedDate(moduledata.created_at) }}
                    </template>

                    <template #cell(last_edited)="{ item: moduledata }">
                        {{ moduledata.last_edit_name }}
                    </template>

                    <template #cell(actions)="{ item: moduledata }">
                        <div class="flex flex-row gap-1">
                            <Link :href="route('jobrequest.edit', moduledata.id)" class="-mb-3 mr-2" v-if="(actions.indexOf('u') !== -1) &&
                                (props.can.all || props.can['jobrequest_edit'])
                                ">
                            <BaseButton color="info" :icon="mdiFileEdit" small />
                            </Link>

                            <BaseButton color="danger" :icon="mdiTrashCan" small title="Delete"
                                @click="delselect = moduledata.id; isModalDangerActive = true"
                                v-if="(actions.indexOf('d') !== -1) && (props.can.all || props.can['jobrequest_delete'])" />
                            <BaseButton color="info"
                                class="bg-purple-400 dark:bg-purple-400 border-purple-400 dark:border-purple-400 hover:bg-purple-500 hover:border-purple-500 hover:dark:bg-purple-500 hover:dark:border-purple-500"
                                :icon="mdiContentCopy" small title="Duplicate" @click="indDuplicate(moduledata.id)"
                                v-if="(props.can.all || props.can['jobrequest_duplicate'])" />
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
        <CardBoxModal v-model="isModalCRDRActive" buttonLabel="Update" :title="changeCRDRTitle" button="danger" has-cancel
            @confirm="changeCrdr(); setfocusback()" @cancel="setfocusback()">
            <p>Please Select CRDR</p>
            <FormControl v-model="changeCRDRModel" :options="props.resourceNeo.allcordinator" ref="poperselect" />
            <div v-if="changeCRDRChange">
                <p>Resason to Change</p>
                <FormControl v-model="changeCRDRReasonModel" maxlength="255" />
                <div v-if="changeCRDRReasonModel && changeCRDRReasonModel.length >= 255" class="text-red-600"> Length
                    Exceed!!
                </div>
                <div class="danger text-xs text-red-600" v-if="crdrreasonempty">Please enter Reason</div>
            </div>
        </CardBoxModal>
        <CardBoxModal v-model="isModalAcceptsActive" buttonLabel="Confirm" title="Please confirm" button="info" has-cancel
            @confirm="acceptJobs" @cancel="indiRowsele = ''">
            <p>Are you sure to Accepts these Job<span class="dark:text-green-400 text-blue-900">({{
                selemailStr.length
            }})</span>?</p>
            <p>{{ selemailStr.join('; ') }}</p>

        </CardBoxModal>
        <CardBoxModal v-model="isModalDuplicateActive" buttonLabel="Confirm" title="Please confirm" button="info" has-cancel
            @confirm="bulkDuplicate" @cancel="indiRowsele = ''">
            <p>Are you sure you want to duplicate the SELECTED BB Job Request<span
                    class="dark:text-green-400 text-blue-900">({{
                        selemailStr.length
                    }})</span>?</p>
            <p>{{ selemailStr.join('; ') }}</p>
            <FormField label="Number of times">
                <FormControl v-model="nx" :options="computednx" />
            </FormField>
        </CardBoxModal>
        <CardBoxModal v-model="isModalRejectsActive" buttonLabel="Confirm" title="Please confirm" button="info" has-cancel
            @confirm="rejectJobs" @cancel="indiRowsele = ''">
            <p>Are you sure to Reject these Job<span class="dark:text-green-400 text-blue-900">({{
                selemailStr.length
            }})</span>?</p>
            <p>{{ selemailStr.join('; ') }}</p>
            <p>Resason to Reject</p>
            <FormControl v-model="rejectReasonModel" maxlength="255" />
            <p class="small">DUP = duplicate entry;
                STR = sitter and not client wrong entry;
                TST = for testing only,
                ERR = other errors.
                Others (free text) - please specify.</p>
            <div v-if="rejectReasonModel && rejectReasonModel.length >= 255" class="text-red-600"> Length Exceed!!</div>
            <div class="danger text-xs text-red-600" v-if="reasonempty">Please enter Reason</div>
        </CardBoxModal>
        <CardBoxModal v-model="isModalDuplicateClientActive" buttonLabel="Ignore & Create New " cancelButtonLabel="Close"
            title="PROPOSED MBJ" button="sucess" has-cancel @confirm="ignoreDuplicateData" :fullWidth="(true)">
            <table class="border text-white  border-gray-500  rounded">
                <thead>
                    <tr class="!bg-customCayn !border !border-black">

                        <th class="!bg-customCayn !border !border-black">Customer Name 名字</th>

                        <th class="!bg-purple-800 !border !border-black">Email</th>
                        <th class="!bg-purple-800 !border !border-black">Address 地址</th>
                        <th class="!bg-purple-800 !border !border-black">#</th>
                        <th class="!bg-purple-800 !border !border-black">Phone number 电话</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="!bg-customCayn !border !border-black">{{ propsd.custname }}</td>
                        <td class="!bg-purple-800 !border !border-black">{{ propsd.email }}</td>
                        <td class="!bg-purple-800 !border !border-black">{{ propsd.addr }}</td>
                        <td class="!bg-purple-800 !border !border-black">{{ propsd.unitn }}</td>
                        <td class="!bg-purple-800 !border !border-black">{{ propsd.phn }}</td>

                    </tr>
                </tbody>
            </table>
            <h1 class="text-2xl">Duplicacy Found, Choose Existing</h1>
            <div class="lg:max-h-96 overflow-y-auto">
                <table class="border text-white  border-gray-500  rounded">
                    <thead>
                        <tr class="!bg-customCayn !border !border-black">
                            <th class="!bg-amber-400 !border !border-black">MBJN</th>
                            <th class="!bg-customCayn !border !border-black">Customer Name 名字</th>
                            <th class="!bg-purple-800 !border !border-black">CN</th>
                            <th class="!bg-purple-800 !border !border-black">Email</th>
                            <th class="!bg-purple-800 !border !border-black">Address 地址</th>
                            <th class="!bg-purple-800 !border !border-black">#</th>
                            <th class="!bg-purple-800 !border !border-black">Phone number 电话</th>
                            <th class="!bg-purple-800 !border !border-black">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="dupli in   duplicacyData  ">
                            <td class="!bg-amber-400 !border !border-black">{{ dupli.jobMBJ }}</td>
                            <td class="!bg-customCayn !border !border-black">{{ dupli.salut }} {{ dupli.full_name }}</td>
                            <td class="!bg-purple-800 !border !border-black">{{ dupli.cn }}</td>
                            <td class="!bg-purple-800 !border !border-black">{{ dupli.job_email }}</td>
                            <td class="!bg-purple-800 !border !border-black">{{ dupli.job_addr }}</td>
                            <td class="!bg-purple-800 !border !border-black">{{ dupli.job_addr_unit }}</td>
                            <td class="!bg-purple-800 !border !border-black">{{ dupli.job_phn }}</td>
                            <td class="!bg-purple-800 !border !border-black">
                                <BaseButton label="Use This" color="info" @click="useThisCn(dupli.id)" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </CardBoxModal>
    </LayoutAuthenticated>
</template>

<style scoped>
.highlight-row {
    font-weight: bold;
    color: #ffa500;
}
</style>
