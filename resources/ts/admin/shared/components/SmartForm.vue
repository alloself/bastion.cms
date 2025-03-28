<template>
    <form class="smart-form">
        <slot
            v-for="schemeField in normalizedFields"
            :name="schemeField.key"
            :key="schemeField.uniqueKey"
        >
            <Field
                :name="schemeField.key"
                :validate-on-mount="false"
                :validate-on-blur="false"
                :validate-on-input="false"
                v-slot="{ value, handleChange }"
            >
                <component
                    :is="schemeField.component"
                    :model-value="value"
                    :readonly="readonly || schemeField.readonly"
                    @update:modelValue="handleChange"
                    :error-messages="getFieldErrors(schemeField)"
                    v-bind="schemeField.props"
                    v-on="schemeField.events"
                    class="mb-1"
                ></component>
            </Field>
        </slot>
    </form>
</template>

<script lang="ts" setup>
import { computed, watch, watchEffect } from "vue";
import {
    useForm,
    Field,
    type FormContext,
    type GenericObject,
} from "vee-validate";
import type { ISmartFormField, ISmartFormProps } from "@admin/shared/types";

const {
    fields = [],
    initialValues = {},
    readonly = false,
} = defineProps<ISmartFormProps>();

const emits = defineEmits<{
    "update:form": [value: FormContext];
}>();

const mergedValidationSchema = computed(() => {
    return fields.reduce((schema, field) => {
        if (field.rule) {
            schema[field.key] = field.rule;
        }
        return schema;
    }, {} as GenericObject);
});

const formContext = useForm({
    validationSchema: mergedValidationSchema,
    initialValues: initialValues,
    keepValuesOnUnmount: true,
    validateOnMount: false,
});
emits("update:form", formContext);

const normalizedFields = computed(() => {
    return fields.map((field, index) => ({
        ...field,
        uniqueKey: `${field.key}-${index}`,
        props: {
            ...(field.props || {}),
        },
        events: {
            ...(field.events || {}),
        },
    }));
});

const getFieldErrors = (field: ISmartFormField) => {
    return formContext.errors.value[field.key] || "";
};

watch(
  () => initialValues,
  (newValues) => {
    if (newValues && Object.keys(newValues).length) {
        formContext.setValues(newValues);
    }
  },
  { deep: true }
);
</script>

<style lang="scss" scoped>
.smart-form {
    height: 100%;
    width: 100%;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    padding: 1rem;

    & > * {
        flex: none;
    }
}
</style>
