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

export interface IBaseEntity {
  id: string;
}

export interface IServerDataList<T> {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  data: T[],
  sortBy?: Array<Record<string,string>>
  search?: string;
}

export interface ISortBy {
  key: string;
  order: "asc" | "desc";
}

export interface ITableProps {
  page: number;
  itemsPerPage: number;
  sortBy: ISortBy[];
  search: string;
}