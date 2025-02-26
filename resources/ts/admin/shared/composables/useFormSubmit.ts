import type { FormContext } from "vee-validate";
import type { Ref } from "vue";

export const useFormSubmit = (
  fn: () => Promise<void>,
  form: Ref<FormContext | undefined>
) => {
  const handler = async () => {
    if (!form.value) {
      return
    }
    try {
      const validateRezult = await form.value.validate();
      if (!validateRezult.valid) {
        return
      }
      await fn();
    } catch (error: any) {
      if (!error) {
        return;
      }
      const formErrors = error?.response?.data.errors;
      if (formErrors && form) {
        form.value.setErrors(formErrors);
      }
    }
  };

  return { handler };
};
