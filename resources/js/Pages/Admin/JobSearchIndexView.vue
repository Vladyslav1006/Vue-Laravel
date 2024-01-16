<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import {
    mdiAlert,
    mdiFileEdit,
    mdiTrashCan,
    mdiCarLightDimmed,
    mdiAccountPlusOutline, mdiSwapHorizontal, mdiAccountMultiplePlus, mdiEyeCircleOutline, mdiLayersSearch, mdiReload, mdiMapMarkerPlus, mdiPhonePlus, mdiEmailPlus, mdiDotsHorizontalCircle, mdiFileDocumentPlus, mdiReceiptTextCheck, mdiContentCopy, mdiUpdate
} from "@mdi/js";
import SectionMain from "@/components/SectionMain.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import BaseButton from "@/components/BaseButton.vue";
import BaseIcon from "@/components/BaseIcon.vue";
import CardBoxModal from "@/components/CardBoxModal.vue";

import CardBox from "@/components/CardBox.vue";
import ComboBox from "@/components/ComboBox.vue";
import NotificationBar from "@/components/NotificationBar.vue";
import { ref } from 'vue';
import Table from "@/components/DataTable/Table.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import { extractDate, extractTime, extractJobNumber, extractJobType } from "@/helpers/helpers"
import axios from "axios";
import { VueDraggableNext } from 'vue-draggable-next';
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
const isModalDuplicateActive = ref(false);
const deletePage = () => {
    if (delselect.value != 0) {
        router.delete(route("jobsearch.destroy", delselect.value), {
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
const changeCRDRTitle = ref('');
const changeCRDRfield = ref('');
const changeCRDRChange = ref('false');
const changeCRDRJobid = ref('');
const crdrChangeShow = (jobid, userid, username, fieldname) => {
    changeCRDRTitle.value = 'Change ' + fieldname
    isModalCRDRActive.value = true;
    changeCRDRfield.value = fieldname
    changeCRDRJobid.value = jobid;
    changeCRDRChange.value = true;
    changeCRDRModel.value = { 'id': userid, 'label': username }
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
    changeCRDRModel.value = { 'id': userid, 'label': username }
    setTimeout(function () {
        poperselect.value.selectFocus()
    }, 100);
}

const changeCrdr = () => {

    router.post(route(props.resourceNeo.resourceName + ".assignCRDR"),
        {
            id: changeCRDRJobid.value,
            key: changeCRDRfield.value,
            value: changeCRDRModel.value.id,
            ref: selectedCellsele.value,
        })

}
const selectedRowsele = ref([]);
const selectedRows = (event) => {
    selectedRowsele.value = event;
}
const selectedCellsele = ref('');
const selectedCell = (event) => {
    selectedCellsele.value = event;
}

const vueDataTable = ref(null);

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

const updateLcn = () => {
    axios.post(route("jobsearch.updateLcn"), {})
        .then(response => {
            useToast().success('LCN Updated Successfully', { duration: 7000 });
            router.reload();
        });
}

const selemailStr = computed(() => {
    let selemail = [];
    let cellref = '';
    let el;
    if (indiRowsele.value != '') {
        cellref = 'A' + indiRowsele.value;
        el = document.getElementById(cellref.toUpperCase());
        selemail.push(el.getAttribute("dataval"));
    }
    else {
        selectedRowsele.value.forEach(element => {
            cellref = 'A' + element;
            el = document.getElementById(cellref.toUpperCase());
            selemail.push(el.getAttribute("dataval"));
        });
    }
    return selemail;
});
const isModalOtherEmailActive = ref(false);
const otherEmailFields = ref([]);
const OtherEmailShow = (id) => {
    modalActiveRecord.value = id;
    isModalActiveLoader.value = true
    otherEmailFields.value = [];
    axios.post(route("otheremail.list"), { jobid: id })
        .then(response => {
            response.data.forEach(email => otherEmailFields.value.push({ email: email.email, lb: email.label, id: email.id, primary: email.is_primary == 1 ? true : false }));
            isModalActiveLoader.value = false
            isModalOtherEmailActive.value = true;
        });
}
const addEmailFields = () => {
    otherEmailFields.value.push({ email: '', lb: '', id: -1, primary: false });
}
const delEmailFields = (el) => {
    otherEmailFields.value.splice((el * 1), 1);
}
const setPrimaryEmailFields = ($event, el) => {
    if (otherEmailFields.value[el].primary === true) {
        $event.target.checked = true;
        return false;
    }
    if (!validateEmail(otherEmailFields.value[el].email)) {
        useToast().error('Email ' + (el + 1) + ' is Invalid');
        $event.target.checked = false;
        return false;
    }
    $event.target.checked = false;
    let newEmailData = [];
    let data = [];
    newEmailData.push({ email: otherEmailFields.value[el].email, lb: otherEmailFields.value[el].lb, id: otherEmailFields.value[el].id, primary: true });
    otherEmailFields.value.forEach((cel, index) => {
        if (index != el) {
            data.push({ email: cel.email, lb: cel.lb, id: cel.id, primary: false });
        }
    });
    data.sort(function (x, y) {
        if (x.id == -1 && y.id > 0) {
            return 1
        }
        if (x.id < y.id) {
            return -1;
        }
        if (x.id > y.id) {
            return 1;
        }
        return 0;
    });
    data.forEach((cel) => {
        newEmailData.push({ email: cel.email, lb: cel.lb, id: cel.id, primary: false });
    });
    otherEmailFields.value = [];
    otherEmailFields.value = newEmailData;
}
const validateEmail = (email) => {
    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)) {
        return true;
    } else {
        return false;
    }
}

const OtherEmailUpdate = () => {
    let valid = true;
    otherEmailFields.value.forEach((data, index) => {
        if (!validateEmail(data.email)) {
            useToast().error('Email ' + (index + 1) + ' is Invalid');
            valid = false;
        }
    });
    if (!valid) {
        isModalOtherEmailActive.value = true;
        return false;
    }

    let dcellselectid = document.getElementById('dcellselectid').value;
    axios.post(route("otheremail.replace"), { jobid: modalActiveRecord.value, emails: otherEmailFields.value, cellref: dcellselectid })
        .then(response => {
            useToast().success('Email List Updated', { duration: 7000 });
            router.reload();
        });
}
const isModalOtherEtyActive = ref(false);
const otherEtyFields = ref([]);
const OtherEtyShow = (id) => {
    modalActiveRecord.value = id;
    isModalActiveLoader.value = true
    otherEtyFields.value = [];
    axios.post(route("otherEty.list"), { jobid: id })
        .then(response => {
            response.data.forEach(ety => otherEtyFields.value.push({ ety: ety.ety, lb: ety.label, id: ety.id, primary: ety.is_primary == 1 ? true : false }));
            isModalActiveLoader.value = false
            isModalOtherEtyActive.value = true;
        });
}
const addEtyFields = () => {
    otherEtyFields.value.push({ ety: '', lb: '', id: -1, primary: false });
}
const delEtyFields = (el) => {
    otherEtyFields.value.splice((el * 1), 1);
}
const setPrimaryEtyFields = ($event, el) => {
    if (otherEtyFields.value[el].primary === true) {
        $event.target.checked = true;
        return false;
    }
    if (otherEtyFields.value[el].ety.trim() == '') {
        useToast().error('Ety ' + (el + 1) + ' is empty');
        $event.target.checked = false;
        return false;
    }
    $event.target.checked = false;
    let newEmailData = [];
    let data = [];
    newEmailData.push({ ety: otherEtyFields.value[el].ety, lb: otherEtyFields.value[el].lb, id: otherEtyFields.value[el].id, primary: true });
    otherEtyFields.value.forEach((cel, index) => {
        if (index != el) {
            data.push({ ety: cel.ety, lb: cel.lb, id: cel.id, primary: false });
        }
    });
    data.sort(function (x, y) {
        if (x.id == -1 && y.id > 0) {
            return 1
        }
        if (x.id < y.id) {
            return -1;
        }
        if (x.id > y.id) {
            return 1;
        }
        return 0;
    });
    data.forEach((cel) => {
        newEmailData.push({ ety: cel.ety, lb: cel.lb, id: cel.id, primary: false });
    });
    otherEtyFields.value = [];
    otherEtyFields.value = newEmailData;
}

const OtherEtyUpdate = () => {
    let valid = true;
    otherEtyFields.value.forEach((data, index) => {
        if (data.ety.trim() == '') {
            useToast().error('Ety ' + (index + 1) + ' is empty');
            valid = false;
        }
    });
    if (!valid) {
        isModalOtherEtyActive.value = true;
        return false;
    }

    let dcellselectid = document.getElementById('dcellselectid').value;
    axios.post(route("otherEty.replace"), { jobid: modalActiveRecord.value, ety: otherEtyFields.value, cellref: dcellselectid })
        .then(response => {
            useToast().success('Ety List Updated', { duration: 7000 });
            router.reload();
        });
}

const isModalOtherPhoneActive = ref(false);
const otherPhoneFields = ref([]);
const OtherPhoneShow = (id) => {
    modalActiveRecord.value = id;
    isModalActiveLoader.value = true
    otherPhoneFields.value = [];
    axios.post(route("otherphone.list"), { jobid: id })
        .then(response => {
            response.data.forEach(phn => otherPhoneFields.value.push({ num: phn.phone, lb: phn.label, id: phn.id, primary: phn.is_primary == 1 ? true : false }));
            isModalActiveLoader.value = false
            isModalOtherPhoneActive.value = true;
            document.getElementById('dummyinput').focus()
        });
}
const validatePhoneNumber = (ph) => {
    var re = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im;
    return re.test(ph);
}

const OtherPhoneUpdate = () => {
    let dcellselectid = document.getElementById('dcellselectid').value;
    let valid = true;
    otherPhoneFields.value.forEach((data, index) => {
        if (data.num.trim() == '' || !validatePhoneNumber(data.num.trim())) {
            useToast().error('Phone Number ' + (index + 1) + ' is Invalid');
            valid = false;
        }
    });
    if (!valid) {
        isModalOtherPhoneActive.value = true;
        return false;
    }

    axios.post(route("otherphone.replace"), { jobid: modalActiveRecord.value, phones: otherPhoneFields.value, cellref: dcellselectid })
        .then(response => {
            useToast().success('Phone List Updated', { duration: 7000 });
            router.reload();
        });
}
const addPhoneFields = () => {
    otherPhoneFields.value.push({ num: '', lb: '', id: -1, primary: false });
}
const delPhoneFields = (el) => {
    otherPhoneFields.value.splice((el * 1), 1);
}
const setPrimaryPhoneFields = ($event, el) => {
    if (otherPhoneFields.value[el].primary === true) {
        $event.target.checked = true;
        return false;
    }
    if (otherPhoneFields.value[el].num.trim() == '' || !validatePhoneNumber(otherPhoneFields.value[el].num.trim())) {
        useToast().error('Phone Number' + (el + 1) + ' is Invalid');
        $event.target.checked = false;
        return false;
    }
    $event.target.checked = false;
    let newEmailData = [];
    let data = [];
    newEmailData.push({ num: otherPhoneFields.value[el].num, lb: otherPhoneFields.value[el].lb, id: otherPhoneFields.value[el].id, primary: true });
    otherPhoneFields.value.forEach((cel, index) => {
        if (index != el) {
            data.push({ num: cel.num, lb: cel.lb, id: cel.id, primary: false });
        }
    });
    data.sort(function (x, y) {
        if (x.id == -1 && y.id > 0) {
            return 1
        }
        if (x.id < y.id) {
            return -1;
        }
        if (x.id > y.id) {
            return 1;
        }
        return 0;
    });
    data.forEach((cel) => {
        newEmailData.push({ num: cel.num, lb: cel.lb, id: cel.id, primary: false });
    });
    otherPhoneFields.value = [];
    otherPhoneFields.value = newEmailData;
}



const isModalOtherAddrActive = ref(false);
const isModalActiveLoader = ref(true);
const modalActiveRecord = ref('');
const otherAddrFields = ref([]);
const otherAddrRanks = ref([]);

const OtherAddrShow = (id) => {
    modalActiveRecord.value = id;
    isModalActiveLoader.value = true
    otherAddrFields.value = [];
    otherAddrRanks.value = [];
    addressPrime.value = 0;
    axios.post(route("otheraddress.list"), { jobid: id })
        .then(response => {
            response.data.forEach((addr, index) => {
                otherAddrFields.value.push({ num: addr.address, lb: addr.label, rn: addr.rank, un: addr.aunit, pr: addr.prime == 1 ? true : false, id: addr.id });
                otherAddrRanks.value.push(addr.rank)
                if (addr.prime == 1) {
                    addressPrime.value = index;
                }
            });
            isModalActiveLoader.value = false
            isModalOtherAddrActive.value = true;
            document.getElementById('dummyinput').focus()
        });
}
const addressPrime = ref(0);
const OtherAddrUpdate = () => {
    let dcellselectid = document.getElementById('dcellselectid').value;
    let valid = true;
    otherAddrFields.value.forEach((data, index) => {
        if (data.num.trim() == '') {
            useToast().error('Address ' + (index + 1) + ' is empty');
            valid = false;
        }
    });
    if (!valid) {
        isModalOtherAddrActive.value = true;
        return false;
    }
    axios.post(route("otheraddress.replace"), { jobid: modalActiveRecord.value, address: otherAddrFields.value, cellref: dcellselectid, prime: addressPrime.value })
        .then(response => {
            useToast().success('Address List Updated', { duration: 7000 });
            router.reload();
        });
}
const addAddrFields = () => {
    otherAddrFields.value.push({ num: '', lb: '', rn: '', un: '', pr: false, id: -1 });
}
const delAddrFields = (el) => {
    otherAddrFields.value.splice((el * 1), 1);
}

const setPrimaryAddressFields = ($event, el) => {
    if (otherAddrFields.value[el].pr === true) {
        $event.target.checked = true;
        return false;
    }
    if (otherAddrFields.value[el].num.trim() == '') {
        useToast().error('Address ' + (el + 1) + ' is empty');
        $event.target.checked = false;
        return false;
    }
    $event.target.checked = false;
    let newEmailData = [];
    let data = [];
    newEmailData.push({ num: otherAddrFields.value[el].num, lb: otherAddrFields.value[el].lb, id: otherAddrFields.value[el].id, pr: true, rn: otherAddrFields.value[el].rn, un: otherAddrFields.value[el].un });
    otherAddrFields.value.forEach((cel, index) => {
        if (index != el) {
            data.push({ num: cel.num, lb: cel.lb, id: cel.id, pr: false, rn: cel.rn, un: cel.un });
        }
    });
    data.sort(function (x, y) {
        if (x.id == -1 && y.id > 0) {
            return 1
        }
        if (x.id < y.id) {
            return -1;
        }
        if (x.id > y.id) {
            return 1;
        }
        return 0;
    });
    data.forEach((cel) => {
        newEmailData.push({ num: cel.num, lb: cel.lb, id: cel.id, pr: false, rn: cel.rn, un: cel.un });
    });
    otherAddrFields.value = [];
    otherAddrFields.value = newEmailData;
}


const isModalFamilyProfActive = ref(false);
const familyProfFields = ref([]);
const declineFProof = ref(0);
const declineChange = () => {
    if (declineFProof.value == 1 && familyProfFields.value.length > 0) {
        useToast().warning('Please confirm that the information already present in data fields are  correct, since client declined to reveal.', { duration: 7000 });
    }
}
const familyProfShow = (id) => {
    modalActiveRecord.value = id;
    isModalActiveLoader.value = true
    familyProfFields.value = [];
    axios.post(route("familyproof.list"), { jobid: id })
        .then(response => {
            response.data.data.forEach(addr => familyProfFields.value.push({ member: addr.member, nati: addr.nati, occup: addr.occup }));
            declineFProof.value = response.data.declineFProof;
            isModalActiveLoader.value = false
            isModalFamilyProfActive.value = true;
            document.getElementById('dummyinput').focus()
        });
}
const familyProfUpdate = () => {
    let dcellselectid = document.getElementById('dcellselectid').value;
    axios.post(route("familyproof.replace"), { jobid: modalActiveRecord.value, declineFProof: declineFProof.value, members: familyProfFields.value, cellref: dcellselectid })
        .then(response => {
            useToast().success('Family Prof Updated', { duration: 7000 });
            router.reload();
        });
}
const addFamilyProfFields = () => {
    familyProfFields.value.push({ member: '', nati: '', occup: '' });
}
const delFamilyProfFields = (el) => {
    familyProfFields.value.splice((el * 1), 1);
}

const generateinvc = (id) => {
    let dcellselectid = document.getElementById('dcellselectid').value;
    router.post(route(props.resourceNeo.resourceName + ".geneinv"),
        {
            id: id, cellref: dcellselectid
        })

}

const isModalAllocatBBActive = ref(false);
const allocateBBFields = ref([]);
const allocateBBRanks = ref([]);
const allocateBBIds = ref([]);
const selectedItemObj = ref({});
const shohiddtal = ref([]);
const showAllocateBB = (item) => {
    shohiddtal.value = [];
    selectedItemObj.value = item;
    modalActiveRecord.value = item.id;
    isModalActiveLoader.value = true
    allocateBBFields.value = [];
    allocateBBRanks.value = [];
    allocateBBIds.value = [];
    axios.post(route("allocatedbb.list"), { jobid: modalActiveRecord.value })
        .then(response => {
            response.data.forEach(addr => {
                allocateBBFields.value.push(addr)
                allocateBBRanks.value.push(addr.rank)
                allocateBBIds.value.push(addr.baysitterId)
            });
            isModalActiveLoader.value = false
            isModalAllocatBBActive.value = true;
            document.getElementById('dummyinput').focus()
        });
}
const setfocusback = () => {
    vueDataTable.value.focustoDisplayCell();
}
const allocateBBUpdate = () => {
    router.reload();
}
const isModelChooseBBActive = ref(false);

const allBuniqueIds = ref([]);

const addMoreBBFields = () => {
    if (allBuniqueIds.value.length == 0) {
        axios.post(route('allocatedbb.babysitterall'))
            .then(response => {
                if (response.data.length == 0) {
                    allBuniqueIds.value.push({ id: 0, label: 'No Candidate found!!' })
                }
                else {
                    response.data.forEach(cand => allBuniqueIds.value.push(cand));
                }
            });
    }

    isModelChooseBBActive.value = true;
    slectedAllocBB.value = {}
    slectedAllocBBRank.value = 0
    slectedAllocBBRemark.value = ''

}
const delAllocateBBFields = (el) => {
    isModalActiveLoader.value = true
    let dcellselectid = document.getElementById('dcellselectid').value;
    axios.post(route("allocatedbb.delete"), { id: el, cellref: dcellselectid, jobid: modalActiveRecord.value })
        .then(response => {
            updateAllocatedBBList();
            useToast().info('Candidate Removed');
        });
}
const slectedAllocBB = ref({})
const slectedAllocBBRank = ref(0)
const slectedAllocBBRemark = ref('')
const BBSelected = () => {
    if (!slectedAllocBB.value.id) {
        useToast().warning('Valid Candidate not selected!!');
    }
    else if (allocateBBIds.value.includes(slectedAllocBB.value.id)) {
        useToast().warning('Candidate Already Added!!');
    }
    else if (!slectedAllocBBRank.value) {
        useToast().warning('Please select rank');
    }
    else {
        isModalActiveLoader.value = true
        let dcellselectid = document.getElementById('dcellselectid').value;
        axios.post(route("allocatedbb.save"), { jobid: modalActiveRecord.value, baysitterId: slectedAllocBB.value.id, rank: slectedAllocBBRank.value, remark: slectedAllocBBRemark.value, cellref: dcellselectid })
            .then(response => {
                updateAllocatedBBList();
                useToast().info('Candidate Added');
            });
    }
}
const updateAllocatedBBList = () => {
    isModalActiveLoader.value = true
    allocateBBFields.value = [];
    allocateBBRanks.value = [];
    allocateBBIds.value = [];
    axios.post(route("allocatedbb.list"), { jobid: modalActiveRecord.value })
        .then(response2 => {
            response2.data.forEach(addr => {
                allocateBBFields.value.push(addr)
                allocateBBRanks.value.push(addr.rank)
                allocateBBIds.value.push(addr.baysitterId)
            });
            isModalActiveLoader.value = false
        });
}

const changeOrder = () => {
    const neworder = [];
    for (let i = 0; i < allocateBBFields.value.length; i++) {
        //allocateBBFields.value[i].rank = i + 1;
        neworder.push({ id: allocateBBFields.value[i].alocbbid, rank: i });
    }
    isModalActiveLoader.value = true
    let dcellselectid = document.getElementById('dcellselectid').value;
    axios.post(route("allocatedbb.changerank"), { ranks: neworder, cellref: dcellselectid, jobid: modalActiveRecord.value })
        .then(response => {
            updateAllocatedBBList();
            useToast().info('Candidates Rank Changed');
        });
}
const isModalEditRankNumActive = ref(false);
const editRankNum = (id, txt) => {
    isModalEditRankNumActive.value = true;
    slectedAllocBBRank.value = txt;
    editRemarkIdModel.value = id;
}
const saveEditRankNum = () => {

    axios.post(route("allocatedbb.changeranknum"), { id: editRemarkIdModel.value, rank: slectedAllocBBRank.value })
        .then(response => {
            useToast().info('Candidates Rank Changed');
            updateAllocatedBBList();
        });

}

const isModalEditRemarkActive = ref(false);
const editRemarkModel = ref('');
const editRemarkIdModel = ref(0);
const editRemark = (id, txt) => {
    isModalEditRemarkActive.value = true;
    editRemarkModel.value = txt;
    editRemarkIdModel.value = id;
}
const saveEditRemark = () => {

    axios.post(route("allocatedbb.changeremark"), { id: editRemarkIdModel.value, remark: editRemarkModel.value })
        .then(response => {
            useToast().info('Candidates Remark Changed');
            updateAllocatedBBList();
        });

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

        <Head title="Search MBJ" />
        <SectionMain class="!py-0">
            <SectionTitleLineWithButton :icon="mdiLayersSearch" title="Search MBJ" main class="!mb-1">
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
                    :seleLimit="((!props.can.all && props.can['jobsearch_limCheckSelect']) ? Number(resourceNeo['max-checkbox-select']) : 0)"
                    headerColor='bg-customCayn' :enterKeyToEdit="(props.can.all || props.can['jobsearch_entertoedit'])">
                    <template #customButtons>
                        <div class="order-9 sm:order-2 mx-2 pt-1"
                            v-if="(props.can.all || props.can['jobsearch_duplicate'])">
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
                        <div class=" order-7 sm:order-7 mx-2 pt-1">
                            <button type="button" @click="updateLcn()"
                                class="w-full border border-gray-200 dark:border-slate-700   rounded-md shadow-sm px-2 py-2 inline-flex justify-center text-sm font-medium hover:border-gray-600 hover:dark:border-slate-100 border-gray-300 bg-gray-400 dark:bg-slate-500 text-white-400"
                                title="Click to Update LCN.">
                                <BaseIcon :path="mdiUpdate" title="Click to Fully Expand/Collapse all COLUMNS." h="h-5" />
                                <span>Update LCN</span>
                            </button>
                        </div>
                    </template>

                    <template #cell(job-type)="{ item: moduledata }">
                        {{ extractJobType(moduledata.jobMBJ) }}
                    </template>
                    <template #cell(job-number)="{ item: moduledata }">
                        {{ extractJobNumber(moduledata.jobMBJ) }}
                    </template>
                    <template #cell(date)="{ item: moduledata }">
                        {{ extractDate(moduledata.created_at) }}
                    </template>
                    <template #cell(time)="{ item: moduledata }">
                        {{ extractTime(moduledata.created_at) }}
                    </template>
                    <template #cell(all-addresses)="{ item: moduledata }">
                        <div :id="`oaddrbtn_${moduledata.id}`"
                            class="flex justify-center cursor-pointer items-center overflow-auto max-h-12 pt-1"
                            @dblclick.stop="OtherAddrShow(moduledata.id)"
                            title="Click to view other addresses and descriptions. The top one is the primary/default one.">
                            <span v-if="moduledata.alladdress == '----'">{{ moduledata.job_addr + '-(Primary)' }}</span>
                            <span v-else>{{ moduledata.alladdress }}</span>
                        </div>
                    </template>
                    <template #cell(all-phone-numbers)="{ item: moduledata }">
                        <div :id="`ophnbtn_${moduledata.id}`"
                            class="flex justify-center cursor-pointer items-center overflow-auto max-h-12 pt-1"
                            @dblclick.stop="OtherPhoneShow(moduledata.id)"
                            title="Click to view other numbers and descriptions.  The top one is the primary/default one.">
                            <span v-if="moduledata.allphones == '---'">{{ moduledata.job_phn + '-(Primary)' }}</span>
                            <span v-else>{{ moduledata.allphones }}</span>
                        </div>
                    </template>
                    <template #cell(all-emails)="{ item: moduledata }">
                        <div :id="`altbl_${moduledata.id}`"
                            class="flex justify-center cursor-pointer items-center overflow-auto max-h-12 pt-1"
                            @dblclick.stop="OtherEmailShow(moduledata.id)"
                            title="Click to view other emails and descriptions. The top one is the primary/default one.">
                            <span v-if="moduledata.allemails == '---'">{{ moduledata.job_email + '-(Primary)' }}</span>
                            <span v-else>{{ moduledata.allemails }}</span>
                        </div>
                    </template>
                    <template #cell(all-ety)="{ item: moduledata }">
                        <div :id="`oetybtn_${moduledata.id}`"
                            class="flex justify-center cursor-pointer items-center overflow-auto max-h-12 pt-1"
                            @dblclick.stop="OtherEtyShow(moduledata.id)"
                            title="Other Entity / Organization / Company Associated with Client.">
                            <span v-if="moduledata.alletys == '---'">{{ moduledata.ety + '-(Primary)' }}</span>
                            <span v-else>{{ moduledata.alletys }}</span>
                        </div>
                    </template>
                    <template #cell(familyprof)="{ item: moduledata }">
                        <div class="text-center">
                            <BaseButton color="info" :icon="mdiFileDocumentPlus" small
                                @click="familyProfShow(moduledata.id)" title="Click to view Family profs"
                                :id="`fpbtn_${moduledata.id}`" />
                        </div>
                    </template>
                    <template #cell(crdr1_name)="{ item: moduledata }">
                        <div class="text-center h-5 overflow-hidden" v-if="moduledata.CRDR1">{{ moduledata.crdr1_name }}
                        </div>
                        <div v-if="moduledata.CRDR1 && (props.can.all || props.can['jobsearch_CRDRchange'])"
                            class="text-center">
                            <BaseButton color="info" :icon="mdiSwapHorizontal" small
                                @click="crdrChangeShow(moduledata.id, moduledata.CRDR1, moduledata.crdr1_name, 'CRDR1')"
                                title="Click to Change CRDR1" :id="`crdr1asn_${moduledata.id}`" />
                        </div>
                        <div class="text-center" v-if="!moduledata.CRDR1">
                            <BaseButton v-if="props.can.all" color="info" :icon="mdiAccountPlusOutline" small
                                @click="crdr1Select(moduledata.id, moduledata.CRDR1, moduledata.crdr1_name)"
                                title="Click to assign CRDR1" :id="`crdr1asn_${moduledata.id}`" />
                            <BaseButton v-else="props.can['jobsearch_edit']" color="info" :icon="mdiAccountPlusOutline"
                                small @click="assignHimself(moduledata.id)" title="Click to assign yourself as CRDR1"
                                :id="`crdr1asn_${moduledata.id}`" />
                        </div>
                    </template>

                    <template #cell(crdr2_name)="{ item: moduledata }">
                        <div class="text-center h-5 overflow-hidden" v-if="moduledata.CRDR2">{{ moduledata.crdr2_name }}
                        </div>
                        <div v-if="moduledata.CRDR2 && (props.can.all || props.can['jobsearch_edit'])" class="text-center">
                            <BaseButton color="info" :icon="mdiSwapHorizontal" small
                                @click="crdrChangeShow(moduledata.id, moduledata.CRDR2, moduledata.crdr2_name, 'CRDR2')"
                                title="Click to Change CRDR2" :id="`crdr2asn_${moduledata.id}`" />
                        </div>
                        <div class="text-center">
                            <BaseButton v-if="!moduledata.CRDR2 && (props.can.all || props.can['jobsearch_edit'])"
                                color="info" :icon="mdiAccountPlusOutline" small
                                @click="crdr2Select(moduledata.id, moduledata.CRDR2, moduledata.crdr2_name)"
                                title="Click to assign  CRDR2" :id="`crdr2asn_${moduledata.id}`" />
                        </div>
                    </template>
                    <template #cell(invc)="{ item: moduledata }">
                        <div class="text-center h-5 overflow-hidden" v-if="moduledata.invc"><a
                                href="https://drive.google.com/drive/u/1/folders/1Xhm4sFiTD9YY5KW6Fwqc5NhDQnBeN9Hc"
                                target="_blank" class="font-bold text-orange-800">{{ moduledata.invc
                                }}</a>
                        </div>
                        <div class="text-center">
                            <BaseButton v-if="!moduledata.invc && (props.can.all || props.can['jobsearch_edit'])"
                                color="info" :icon="mdiReceiptTextCheck" small @click="generateinvc(moduledata.id)"
                                title="Generate Invoice Number" />
                        </div>
                    </template>
                    <template #cell(auto-txt)="{ item: moduledata }">
                        <div class="text-center">{{ ((!moduledata.allctbbs || !moduledata.invc) ?
                            "Unable to auto-gen. Pls fill in first : EMAIL; ALLOCATED BB; INVOICE NUMBER; etc." : '') }}
                        </div>
                    </template>

                    <template #cell(a-l-l-o-c-a-t-e-d-b-b)="{ item: moduledata }">
                        <div :id="`altbl_${moduledata.id}`" class="flex justify-center items-center h-[58px]"
                            @dblclick.stop="showAllocateBB(moduledata)"
                            title=" Click to See List of Allocated Babysitters and their details">
                            <span>{{ moduledata.allctbbs || 'NILL FOUND' }}</span>
                        </div>

                    </template>

                    <template #cell(last_edited)="{ item: moduledata }">
                        {{ moduledata.last_edit_name }}
                    </template>

                    <template #cell(start_date)="{ item: moduledata }">
                        <span :class="{ 'highlight-row': isHolidayDateEqual(moduledata.start_date) == 'yes' }">{{
                            moduledata.start_date }}</span>
                    </template>

                    <template #cell(actions)="{ item: moduledata }">
                        <div class="flex flex-row gap-1">
                            <Link :href="route('jobsearch.edit', moduledata.id)" class="-mb-3 mr-2" v-if="(actions.indexOf('u') !== -1) &&
                                (props.can.all || props.can['jobsearch_edit'])
                                ">
                            <BaseButton color="info" :icon="mdiFileEdit" small />
                            </Link>

                            <BaseButton color="danger" :icon="mdiTrashCan" small title="Delete"
                                @click="delselect = moduledata.id; isModalDangerActive = true"
                                v-if="(actions.indexOf('d') !== -1) && (props.can.all || props.can['jobsearch_delete'])" />
                            <BaseButton color="info"
                                class="bg-purple-400 dark:bg-purple-400 border-purple-400 dark:border-purple-400 hover:bg-purple-500 hover:border-purple-500 hover:dark:bg-purple-500 hover:dark:border-purple-500"
                                :icon="mdiContentCopy" small title="Duplicate" @click="indDuplicate(moduledata.id)"
                                v-if="(props.can.all || props.can['jobsearch_duplicate'])" />
                        </div>
                    </template>
                </Table>
            </CardBox>
        </SectionMain>
        <CardBoxModal v-model="isModalDangerActive" buttonLabel="Confirm" title="Please confirm" button="danger" has-cancel
            @confirm="deletePage">
            <p>Are you sure to delete?</p>
        </CardBoxModal>
        <CardBoxModal v-model="isModalCRDRActive" buttonLabel="Update" :title="changeCRDRTitle" button="sucess" has-cancel
            @confirm="changeCrdr(); setfocusback()" @cancel="setfocusback()">
            <p>Please Select CRDR</p>
            <FormControl v-model="changeCRDRModel" :options="props.resourceNeo.allcordinator" ref="poperselect" />

        </CardBoxModal>

        <CardBoxModal v-model="isModalOtherEmailActive" buttonLabel="Save" cancelButtonLabel="Close" title="All Emails"
            button="info" has-cancel @confirm="OtherEmailUpdate"
            infoText="Click to view other emails and descriptions. The top one is the primary/default one.">
            <div class="lg:max-h-96 overflow-y-auto">
                <div class="text-center" v-if="isModalActiveLoader">
                    <div role="status">
                        <BaseIcon :path="mdiReload" class="animate-spin" />
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-8 gap-1 mb-1">
                    <div class="col-span-2">Pri</div>
                    <div class="col-span-3">Email</div>
                    <div class="col-span-2">Label</div>
                    <div class="col-span-1"></div>
                </div>
                <div v-if="otherEmailFields.length == 0 && !isModalActiveLoader">No Email Found</div>
                <div class="grid grid-cols-1 lg:grid-cols-7 gap-1 mb-1" v-for="(selm, index) in otherEmailFields"
                    :key="`sortkey${index}`" :index="index" :column="selm">
                    <input type="radio" v-model="otherEmailFields[index].primary"
                        class="all-email-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 m-1"
                        :checked="otherEmailFields[index].primary" @click="setPrimaryEmailFields($event, index)">
                    <FormControl v-model="otherEmailFields[index].email" placeholder="Email" class="col-span-3"
                        type="email" />
                    <FormControl v-model="otherEmailFields[index].lb" placeholder="Label" class="col-span-2" type="text" />
                    <div v-if="!otherEmailFields[index].primary" class="cursor-pointer col-span-1"
                        @click="delEmailFields(index)"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill=" currentColor"
                                d="M9,3V4H4V6H5V19A2,2 0 0,0 7,21H17A2,2 0 0,0 19,19V6H20V4H15V3H9M9,8H11V17H9V8M13,8H15V17H13V8Z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
            <BaseButton label="+ Add Email" color="success" @click="addEmailFields" />
        </CardBoxModal>


        <CardBoxModal v-model="isModalOtherEtyActive" buttonLabel="Save" cancelButtonLabel="Close" title="All Ety"
            button="info" has-cancel @confirm="OtherEtyUpdate"
            infoText="Click to view other emails and descriptions. The top one is the primary/default one.">
            <div class="lg:max-h-96 overflow-y-auto">
                <div class="text-center" v-if="isModalActiveLoader">
                    <div role="status">
                        <BaseIcon :path="mdiReload" class="animate-spin" />
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-8 gap-1 mb-1">
                    <div class="col-span-2">Pri</div>
                    <div class="col-span-3">Ety</div>
                    <div class="col-span-2">Label</div>
                    <div class="col-span-1"></div>
                </div>
                <div v-if="otherEtyFields.length == 0 && !isModalActiveLoader">No Ety Found</div>
                <div class="grid grid-cols-1 lg:grid-cols-7 gap-1 mb-1" v-for="(selm, index) in otherEtyFields"
                    :key="`sortkey${index}`" :index="index" :column="selm">
                    <input type=radio
                        class="rounded all-ety-checkbox border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 m-1"
                        :checked="otherEtyFields[index].primary" @click="setPrimaryEtyFields($event, index)">
                    <FormControl v-model="otherEtyFields[index].ety" placeholder="Ety" class="col-span-3" type="email" />
                    <FormControl v-model="otherEtyFields[index].lb" placeholder="Label" class="col-span-2" type="text" />
                    <div v-if="!otherEtyFields[index].primary" class="cursor-pointer col-span-1"
                        @click="delEtyFields(index)"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill=" currentColor"
                                d="M9,3V4H4V6H5V19A2,2 0 0,0 7,21H17A2,2 0 0,0 19,19V6H20V4H15V3H9M9,8H11V17H9V8M13,8H15V17H13V8Z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
            <BaseButton label="+ Add Ety" color="success" @click="addEtyFields" />
        </CardBoxModal>

        <CardBoxModal v-model="isModalOtherPhoneActive" buttonLabel="Save" cancelButtonLabel="Close"
            title="All Phone Numbers" button="info" has-cancel @confirm="OtherPhoneUpdate"
            infoText="Click to view other numbers and descriptions.  The top one is the primary/default one.">
            <div class="lg:max-h-96 overflow-y-auto">
                <div class="text-center" v-if="isModalActiveLoader">
                    <div role="status">
                        <BaseIcon :path="mdiReload" class="animate-spin" />
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-8 gap-1 mb-1">
                    <div class="col-span-2">Pri</div>
                    <div class="col-span-3">Phone</div>
                    <div class="col-span-2">Label</div>
                    <div class="col-span-1"></div>
                </div>
                <div v-if="otherPhoneFields.length == 0 && !isModalActiveLoader">No Phone Found</div>
                <div class="grid grid-cols-1 lg:grid-cols-8 gap-1 mb-1" v-for="(selm, index) in otherPhoneFields"
                    :key="`sortkey${index}`" :index="index" :column="selm">
                    <input type="radio" v-model="otherPhoneFields[index].primary"
                        class="all-email-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 m-1"
                        :checked="otherPhoneFields[index].primary" @click="setPrimaryPhoneFields($event, index)">
                    <FormControl v-model="otherPhoneFields[index].num" placeholder="Phone Number" class="col-span-3" />
                    <FormControl v-model="otherPhoneFields[index].lb" placeholder="Label" class="col-span-2" />
                    <div v-if="!otherPhoneFields[index].primary" class="cursor-pointer col-span-1"
                        @click="delPhoneFields(index)"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill=" currentColor"
                                d="M9,3V4H4V6H5V19A2,2 0 0,0 7,21H17A2,2 0 0,0 19,19V6H20V4H15V3H9M9,8H11V17H9V8M13,8H15V17H13V8Z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
            <BaseButton label="+ Add Phone" color="success" @click="addPhoneFields" />
        </CardBoxModal>

        <CardBoxModal v-model="isModalOtherAddrActive" buttonLabel="Save" cancelButtonLabel="Close" title="All Addresses"
            :fullWidth="(false)" button="info" has-cancel @confirm="OtherAddrUpdate"
            infoText="Click to view other addresses and descriptions. The top one is the primary/default one.">
            <div class="lg:max-h-96 overflow-y-auto">
                <div class="text-center" v-if="isModalActiveLoader">
                    <div role="status">
                        <BaseIcon :path="mdiReload" class="animate-spin" />
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div v-if="otherAddrFields.length == 0 && !isModalActiveLoader">No Address Found</div>
                <div class="grid grid-cols-1 lg:grid-cols-8 gap-1 mb-1">
                    <div class="col-span-1">Pri</div>
                    <div class="col-span-3">Address</div>
                    <div class="col-span-2">#</div>
                    <div class="col-span-2">Label</div>

                    <div class="col-span-1"></div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-9 gap-1 mb-1" v-for="(selm, index) in otherAddrFields"
                    :key="`sortkey${index}`" :index="index" :column="selm">
                    <input type="radio" v-model="otherAddrFields[index].pr"
                        class="all-email-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 m-1"
                        :checked="otherAddrFields[index].pr" @click="setPrimaryAddressFields($event, index)">
                    <FormControl v-model="otherAddrFields[index].num" placeholder="Address" class="col-span-3" />
                    <FormControl v-model="otherAddrFields[index].un" placeholder="Unit" class="col-span-2" />
                    <FormControl v-model="otherAddrFields[index].lb" placeholder="Label" class="col-span-2" />
                    <div v-if="!otherAddrFields[index].pr" class="cursor-pointer col-span-1" @click="delAddrFields(index)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill=" currentColor"
                                d="M9,3V4H4V6H5V19A2,2 0 0,0 7,21H17A2,2 0 0,0 19,19V6H20V4H15V3H9M9,8H11V17H9V8M13,8H15V17H13V8Z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
            <BaseButton label="+ Add Address" color="success" @click="addAddrFields" />
        </CardBoxModal>
        <CardBoxModal v-model="isModalFamilyProfActive" buttonLabel="Save" cancelButtonLabel="Close" title="Family Prof"
            button="sucess" has-cancel @confirm="familyProfUpdate(); setfocusback()" @cancel="setfocusback()">
            <div class="lg:max-h-96 overflow-y-auto">
                <div class="text-center" v-if="isModalActiveLoader">
                    <div role="status">
                        <BaseIcon :path="mdiReload" class="animate-spin" />
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div>
                    <input type=radio
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 m-3"
                        v-model="declineFProof" :checked="declineFProof" value="1" @change="declineChange">
                    Declined to Reveal Understanding Higher
                    Mismatch Risk
                </div>
                <div v-if="familyProfFields.length == 0 && !isModalActiveLoader">No Family Prof Found</div>
                <div v-if="!declineFProof || familyProfFields.length > 0">
                    <div class="grid grid-cols-1 lg:grid-cols-7 gap-1 mb-1" v-for="(selm, index) in familyProfFields"
                        :key="`sortkey${index}`" :index="index" :column="selm">
                        <FormControl v-model="familyProfFields[index].member" placeholder="FAMILY MEMBER"
                            class="col-span-2" />
                        <FormControl v-model="familyProfFields[index].nati" placeholder="NATIONALITY" class="col-span-2" />
                        <FormControl v-model="familyProfFields[index].occup" placeholder="OCCUPATION" class="col-span-2" />
                        <div class="cursor-pointer col-span-1" @click="delFamilyProfFields(index)"><svg
                                xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill=" currentColor"
                                    d="M9,3V4H4V6H5V19A2,2 0 0,0 7,21H17A2,2 0 0,0 19,19V6H20V4H15V3H9M9,8H11V17H9V8M13,8H15V17H13V8Z">
                                </path>
                            </svg>
                        </div>
                    </div>

                </div>

            </div>
            <div v-if="!declineFProof || familyProfFields.length > 0">
                <BaseButton label="+ Add More" color="success" @click="addFamilyProfFields" />
            </div>
        </CardBoxModal>
        <CardBoxModal v-model="isModalAllocatBBActive" buttonLabel="Update Table" cancelButtonLabel="Close"
            title="Allocated BB" button="sucess" has-cancel @confirm="allocateBBUpdate(); setfocusback()"
            @cancel="setfocusback()" :fullWidth="(true)">
            <table class="border text-white  border-gray-500  rounded">
                <thead>
                    <tr class="!bg-customCayn !border !border-black">
                        <th class="!bg-amber-400 !border !border-black">S/N</th>
                        <th class="!bg-customCayn !border !border-black">Customer Name 名字</th>
                        <th class="!bg-purple-800 !border !border-black">CN</th>
                        <th class="!bg-purple-800 !border !border-black">Email</th>
                        <th class="!bg-purple-800 !border !border-black">Address 地址</th>
                        <th class="!bg-purple-800 !border !border-black">#</th>
                        <th class="!bg-purple-800 !border !border-black">Phone number 电话</th>
                        <th class="!bg-purple-800 !border !border-black">Summary</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="!bg-amber-400 !border !border-black">{{ selectedItemObj.jobMBJ }}</td>
                        <td class="!bg-customCayn !border !border-black">{{ selectedItemObj.s_full_name }}</td>
                        <td class="!bg-purple-800 !border !border-black">{{ selectedItemObj.cn }}</td>
                        <td class="!bg-purple-800 !border !border-black">{{ selectedItemObj.job_email }}</td>
                        <td class="!bg-purple-800 !border !border-black">{{ selectedItemObj.job_addr }}</td>
                        <td class="!bg-purple-800 !border !border-black">{{ selectedItemObj.job_addr_unit }}</td>
                        <td class="!bg-purple-800 !border !border-black">{{ selectedItemObj.job_phn }}</td>
                        <td class="!bg-purple-800 !border !border-black"></td>
                    </tr>
                </tbody>
            </table>
            <div class="lg:max-h-96 overflow-y-auto">
                <div class="text-center" v-if="isModalActiveLoader">
                    <div role="status">
                        <BaseIcon :path="mdiReload" class="animate-spin" />
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div v-if="allocateBBFields.length == 0 && !isModalActiveLoader">No Allocated Job Found</div>
                <VueDraggableNext class="dragArea" :list="allocateBBFields" @change="changeOrder">

                    <div class=" m-1 cursor-move" v-for="(selm, index) in allocateBBFields" :key="`allocbb${index}`"
                        :index="index" :column="selm">
                        <table class="border border-gray-500 text-black  rounded !border-black">
                            <thead>
                                <tr>
                                    <th class="!bg-amber-400 !border !border-black w-[20%]">ALLOCATED BUNIQUEID</th>
                                    <th class="!bg-amber-400 !border !border-black w-[5%]">Rank</th>
                                    <th class="!bg-amber-400 !border !border-black w-[55%] ">Remarks</th>
                                    <th class="!bg-amber-400 !border !border-black w-[20%]"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="!bg-amber-400 !border !border-black">{{ selm.BUniqueID }}</td>
                                    <td class="!bg-amber-400 !border !border-black"
                                        @dblclick="editRankNum(selm.alocbbid, selm.rank)">{{ selm.rank }}</td>
                                    <td class="!bg-amber-400 !border !border-black"
                                        @dblclick="editRemark(selm.alocbbid, selm.remark)">{{
                                            selm.remark }}</td>
                                    <td class="text-center !bg-amber-400 !border !border-black">
                                        <BaseButton color="info" :icon="mdiDotsHorizontalCircle" small
                                            @click="shohiddtal[selm.alocbbid] = !shohiddtal[selm.alocbbid]"
                                            title="Click to SHOW / HIDE DETAILS" />
                                        <BaseButton color="danger" :icon="mdiTrashCan" small title="Delete" class="ml-1"
                                            @click="delAllocateBBFields(selm.alocbbid)" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="border border-gray-500  rounded" v-if="shohiddtal[selm.alocbbid]">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Numb</th>
                                    <th>Balance</th>
                                    <th>Phone number / 电话号码</th>
                                    <th>2nd phone number / 第二电话号码 (For WhatsApp)</th>
                                    <th>Gender / 男女</th>
                                    <th>Nationality / 国家</th>
                                    <th>Ethnicity</th>
                                    <th>Days Available</th>
                                    <th>Where were your previous experiences taking care of baby or kids? For how long?</th>
                                    <th>Email Address</th>
                                    <th>Birthday (16 to 75 preferred) / 年龄</th>
                                    <th>Combined</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ selm.full_name }}</td>
                                    <td></td>
                                    <td>{{ selm.bal }}</td>
                                    <td>{{ selm.whts_no }}</td>
                                    <td>{{ selm.whts_no2 }}</td>
                                    <td>{{ selm.gender }}</td>
                                    <td>{{ selm.nationality }}</td>
                                    <td>{{ selm.ethnicity }}</td>
                                    <td>{{ selm.day_avl }}</td>
                                    <td>{{ selm.exp }}</td>
                                    <td>{{ selm.email }}</td>
                                    <td>{{ selm.dob }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </VueDraggableNext>

            </div>
            <BaseButton label="+ Add More CANDIDATES" color="success" @click="addMoreBBFields" />
            <p>Double Click to Edit Remarks and Ranks; Drag Individual Allocated BUniqueID to Reset / Swap Rankings</p>
        </CardBoxModal>

        <CardBoxModal v-model="isModelChooseBBActive" buttonLabel="Add Candidate" title="Select BabySitter" button="info"
            has-cancel @confirm="BBSelected">
            <ComboBox label="Search Babysitter by BUniqueID" v-model="slectedAllocBB" :sourceData="allBuniqueIds">
            </ComboBox>
            <div class="grid grid-cols-1 lg:grid-cols-6 gap-1 mb-1">
                <div class="col-span-2">
                    <div>Rank</div>
                    <select v-model="slectedAllocBBRank"
                        class="block focus:border-transparent focus:ring-0  text-gray-500 dark:text-gray-100 w-full shadow-sm text-sm bg-gray-100 dark:bg-slate-600 rounded-md"
                        @change="">
                        <option v-for="index in 99" :key="index" :value="index" :disabled="allocateBBRanks.includes(index)">
                            {{ index }}
                        </option>
                    </select>
                </div>
                <div class="col-span-4">
                    <div>Remark</div>
                    <input autocomplete="off" v-model="slectedAllocBBRemark"
                        class="block w-full pl-2 text-sm rounded-md shadow-sm focus:border-transparent focus:ring-0 text-sm border border-gray-200 dark:border-slate-700 bg-gray-100 dark:bg-slate-600 text-opacity"
                        placeholder="Remark" type="text" />
                </div>
            </div>
        </CardBoxModal>
        <CardBoxModal v-model="isModalEditRemarkActive" buttonLabel="Update" title="Edit Remark" button="info" has-cancel
            @confirm="saveEditRemark">

            <div class="grid grid-cols-1 lg:grid-cols-6 gap-1 mb-1">
                <div class="col-span-4">
                    <div>Remark</div>
                    <input autocomplete="off" v-model="editRemarkModel"
                        class="block w-full pl-2 text-sm rounded-md shadow-sm focus:border-transparent focus:ring-0 text-sm border border-gray-200 dark:border-slate-700 bg-gray-100 dark:bg-slate-600 text-opacity"
                        placeholder="Remark" type="text" />
                </div>
            </div>
        </CardBoxModal>
        <CardBoxModal v-model="isModalEditRankNumActive" buttonLabel="Update" title="Edit Rank" button="info" has-cancel
            @confirm="saveEditRankNum">

            <div class="grid grid-cols-1 lg:grid-cols-6 gap-1 mb-1">
                <div class="col-span-4">
                    <div>Rank</div>
                    <select v-model="slectedAllocBBRank"
                        class="block focus:border-transparent focus:ring-0  text-gray-500 dark:text-gray-100 w-full shadow-sm text-sm bg-gray-100 dark:bg-slate-600 rounded-md"
                        @change="">
                        <option v-for="index in 99" :key="index" :value="index" :disabled="allocateBBRanks.includes(index)">
                            {{ index }}
                        </option>
                    </select>
                </div>
            </div>
        </CardBoxModal>
        <CardBoxModal v-model="isModalDuplicateActive" buttonLabel="Confirm" title="Please confirm" button="info" has-cancel
            @confirm="bulkDuplicate" @cancel="indiRowsele = ''">
            <p>Are you sure you want to duplicate the SELECTED MBJ<span class="dark:text-green-400 text-blue-900">({{
                selemailStr.length
            }})</span>?</p>
            <p>{{ selemailStr.join('; ') }}</p>
            <FormField label="Number of times">
                <FormControl v-model="nx" :options="computednx" />
            </FormField>
        </CardBoxModal>
    </LayoutAuthenticated>
    <div class="bg-blue-300 bg-blue-400 bg-slate-400 bg-green-200 bg-green-300 bg-cyan-200 bg-blue-900 bg-fuchsia-300">
    </div>
</template>

<style scoped>.highlight-row {
    font-weight: bold;
    color: #ffa500;
}</style>
