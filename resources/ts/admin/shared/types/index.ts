import type { FormContext } from "vee-validate";
import type { Component, Ref } from "vue";
import type { IModule } from "../modules";
import type { AuditModel, User } from "@/ts/types/models";

export interface ISmartFormField {
  component: Component | string;
  key: string | (() => string);
  props?: Record<string, unknown>;
  events?: Record<string, Function>;
  rule?: unknown;
  readonly?: boolean;
}

export interface ISmartFormProps {
  fields: ISmartFormField[];
  form: FormContext | undefined | null;
  initialValues?: Record<string, unknown>;
  loading?: boolean;
  readonly?: boolean;
}

export interface IBaseEntity {
  id: string;
}

export interface INestedSet<T> {
  children: T[];
  parent_id: string;
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
  detailClass?: string;
}

export interface IOptionsFieldsFabric<T> {
  entity?: T
  initialValues?: Partial<T>
}

export type TCreateFields<T> = (context?: IOptionsFieldsFabric<T>) => Promise<{
  fields: Ref<ISmartFormField[]>
  readonly?: boolean;
}>

export interface IRelationAutocompleteProps<T extends IBaseEntity> extends IItems<T> {
  readonly?: boolean;
  moduleKey: string;
  modelValue?: keyof T;
  loading?: boolean;
  initialItems?: T[];
}

export interface IItems<T> {
  itemValue?: keyof T | ((item: T) => string);
  itemTitle?: keyof T | ((item: T) => string);
}

export interface IRelationTreeProps<T> extends IItems<T> {
  moduleKey: string;
  modelValue: T[];
  initialValues: T[];
}

export interface IRelationCardProps {
  loading?: boolean;
  title?: string;
  icon?: string;
}

export type THistoryItem =  AuditModel & { user: User }

export interface IHistoryBottomSheetProps {
  modelValue: boolean;
  history: THistoryItem[]
}