<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from "vue";
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import {
    mdiViewList,
    mdiAccountBoxMultiple,
    mdiEyeOutline, mdiEyeOffOutline
} from "@mdi/js";
import SectionMain from "@/components/SectionMain.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import BaseButton from "@/components/BaseButton.vue";
import CardBox from "@/components/CardBox.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import FormCheckRadio from "@/components/FormCheckRadio.vue";


const props = defineProps({
    formdata: {
        type: Object,
        default: () => ({}),
    }
});

const form = useForm({
    holiday_name: props.formdata.holiday_name,
    holiday_date: props.formdata.holiday_date,
});

const submitform = () => {
    if (props.formdata.id) {
        form.put(route('public-holidays.update', props.formdata.id));
    }
    else {
        form.post(route('public-holidays.store'));
    }
};


</script>
<template>
    <LayoutAuthenticated>

        <Head title="Public Holidays" />
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiAccountBoxMultiple" title="Public Holidays" main>
                <div class="flex">
                    <Link :href="route('public-holidays.index')">
                        <BaseButton class="m-2" :icon="mdiViewList" color="success" rounded-full small label="List Public Holidays" />
                    </Link>

                </div>
            </SectionTitleLineWithButton>
            <form @submit.prevent="submitform" autocomplete="off">
                <CardBox>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <FormField label="Holiday Name" help="" :error="form.errors.holiday_name">
                            <FormControl name="holiday_name" v-model="form.holiday_name" required />
                        </FormField>
                        <FormField label="Holiday Date" help="" :error="form.errors.holiday_date">
                            <FormControl type="date" name="holiday_date" v-model="form.holiday_date" required />
                        </FormField>
                    </div>
                </CardBox>

                <div class="mt-4 flex">
                    <BaseButton class="mr-2" type="submit" small color="info" :label="props.formdata.id ? 'Update' : 'Save'" />
                    <Link :href="route('public-holidays.index')">
                        <BaseButton type="reset" small color="info" outline label="Cancel" />
                    </Link>
                </div>

            </form>
        </SectionMain>
    </LayoutAuthenticated>
</template>
