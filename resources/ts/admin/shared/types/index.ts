import type { FormContext } from "vee-validate";
import type { Component } from "vue";

export interface ISmartFormField {
    component: Component | string;
    key: string | (() => string);
    props?: Record<string, unknown>;
    events?: Record<string, Function>;
    rule?: unknown;
  }
  
  export interface ISmartFormProps {
    fields: ISmartFormField[];
    form: FormContext | undefined | null;
    initialValues?: Record<string, unknown>;
    loading?: boolean;
  }