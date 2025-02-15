<template>
    <form>
        <slot
            v-for="(schemeField, index) in fields"
            :name="schemeField.key"
            :key="`${index}-${schemeField.key}`"
        >
            <Field
                :name="getFieldKey(schemeField)"
                v-slot="{ value, handleChange, errors }"
            >
                <component
                    :is="schemeField.component"
                    class="mb-2"
                    :model-value="value"
                    :loading="loading"
                    @update:modelValue="handleChange"
                    :error-messages="errors"
                    v-bind="getFieldProps(schemeField)"
                    v-on="getFieldEvents(schemeField)"
                ></component>
            </Field>
        </slot>
    </form>
</template>
<script lang="ts" setup>
import { computed } from "vue";
import { useForm, Field, type FormContext } from "vee-validate";
import type { ISmartFormField, ISmartFormProps } from "@admin/shared/types";
import { configure } from "vee-validate";

configure({
    validateOnBlur: false,
});

const {
    fields = [],
    form,
    loading = false,
    initialValues = {},
} = defineProps<ISmartFormProps>();

const emits = defineEmits<{
    "update:form": [value: FormContext];
}>();

const getFieldProps = (field: ISmartFormField) => {
    return field.props || {};
};

const getFieldEvents = (field: ISmartFormField) => {
    return field.events || {};
};

const getFieldKey = (field: ISmartFormField) => {
    return typeof field.key === "function" ? field.key() : field.key;
};

const validationSchema = computed(() => {
    const rules = fields.reduce((acc) => {
        return acc;
    }, {});

    return rules;
});

if (!form) {
    emits(
        "update:form",
        useForm({
            validationSchema,
            initialValues,
            keepValuesOnUnmount: true,
        })
    );
}
</script>
