<template>
    <form>
        <slot
            v-for="(schemeField, index) in normalizedFields"
            :name="schemeField.key"
            :key="`${index}-${schemeField.key}`"
        >
            <Field
                :name="getFieldKey(schemeField)"
                :validateOnBlur="false",
                v-slot="{ value, handleChange, errors }"
            >
                <component
                    :is="schemeField.component"                  
                    :model-value="value"
                    :loading="loading"
                    :readonly="readonly || schemeField.readonly"
                    @update:modelValue="handleChange"
                    :error-messages="errors"
                    v-bind="schemeField.props"
                    v-on="schemeField.events"
                ></component>
            </Field>
        </slot>
    </form>
</template>
<script lang="ts" setup>
import { computed, watchEffect } from "vue";
import { useForm, Field, type FormContext, type GenericObject } from "vee-validate";
import type { ISmartFormField, ISmartFormProps } from "@admin/shared/types";

const {
    fields = [],
    loading = false,
    initialValues = {},
    readonly = false
} = defineProps<ISmartFormProps>();

const emits = defineEmits<{
    "update:form": [value: FormContext];
}>();


const getFieldKey = (field: ISmartFormField) => {
    return typeof field.key === "function" ? field.key() : field.key;
};

const mergedValidationSchema = computed(() => {
  const fieldRules = fields.reduce((schema, field) => {
    if (field.rule) {
      schema[getFieldKey(field)] = field.rule
    }
    return schema
  }, {} as GenericObject)

  return fieldRules
})

const normalizedFields = computed(() => {
  return fields.map((field, index) => ({
    ...field,
    uniqueKey: `${field.key}-${index}`,
    name: typeof field.key === 'function' ? field.key() : field.key,
    props: {
      ...field.props,
    },
    events: {
      ...field.events,
    }
  }))
})

const formContext = useForm({
  validationSchema: mergedValidationSchema,
  initialValues: initialValues,
  keepValuesOnUnmount: true,
  validateOnMount: false,
})

watchEffect(() => {
  emits('update:form', formContext)
})

watchEffect(() => {
  if (Object.keys(initialValues).length) {
    formContext.setValues(initialValues)
  }
})

</script>
