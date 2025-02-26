<template>
  <form class="smart-form">
      <slot
          v-for="schemeField in normalizedFields"
          :name="schemeField.key"
          :key="schemeField.uniqueKey"
      >
          <Field
              :name="schemeField.key"
              :validateOnBlur="false"
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
              ></component>
          </Field>
      </slot>
  </form>
</template>

<script lang="ts" setup>
import { computed, watchEffect } from "vue";
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

const normalizedFields = computed(() => {
  return fields.map((field, index) => ({
      ...field,
      uniqueKey: `${field.key}-${index}`,
      props: {
          ...field.props,
      },
      events: {
          ...field.events || {},
      },
  }));
});

const getFieldErrors = (field: ISmartFormField) => {
  return formContext.errors.value[field.key] || "";
};

watchEffect(() => {
  emits("update:form", formContext);
});

watchEffect(() => {
  if (initialValues && Object.keys(initialValues).length) {
      formContext.setValues(initialValues);
  }
});
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