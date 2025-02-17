import type { FormContext } from "vee-validate";
import type { Component, Ref } from "vue";
import type { IModule } from "../modules";

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
  sortBy?: Array<Record<string, string>>
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

export interface IDetailProps<T> {
  id?: string | number;
  modal?: boolean;
  initialValues?: Partial<T>;
  module: IModule;
}

export interface IOptionsFieldsFabric<T> {
  entity?: T
  initialValues?: Partial<T>
}

export type TCreateFields<T> = (context?: IOptionsFieldsFabric<T>) => Promise<{
  fields: Ref<ISmartFormField[]>
}>

export interface IRelationFieldConfig {
  type: 'relation-table' | 'relation-tree' | 'autocomplete'
  moduleKey?: string
  title?: string
  morphRelation?: boolean
  propHeaders?: Array<{ title: string; key: string }>
  initialValues?: Record<string, any>
  itemValue?: string
  itemTitle?: string
  fileType?: 'image' | 'file'
}