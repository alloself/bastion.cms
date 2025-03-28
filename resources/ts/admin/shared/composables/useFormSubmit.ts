import type { FormContext } from "vee-validate";
import type { Ref } from "vue";

function formatResponseErrors(
    errors: Record<string, string[]>,
    prefixMap: Record<string, string> = {}
) {
    const formatted: Record<string, string[]> = {};

    Object.entries(errors).forEach(([key, messages]) => {
        const prefixedKey = prefixMap[key] ? `${prefixMap[key]}${key}` : key;
        formatted[prefixedKey] = messages;
    });

    return formatted;
}

export const useFormSubmit = (
    fn: () => Promise<void>,
    form: Ref<FormContext | undefined>
) => {
    const handler = async () => {
        if (!form.value) {
            return;
        }
        try {
            const validateRezult = await form.value.validate();
            if (!validateRezult.valid) {
                return;
            }
            await fn();
        } catch (error: any) {
            if (!error) {
                return;
            }
            const formErrors = error?.response?.data.errors;
            if (formErrors && form) {
                const formattedErrors = formatResponseErrors(formErrors, {
                    title: "link.",
                    subtitle: "link.",
                });
                form.value.setErrors(formattedErrors);
            }
        }
    };

    return { handler };
};
