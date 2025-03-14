import type { FormContext } from "vee-validate";
import type { Component, InjectionKey, Ref } from "vue";
import type { IModule } from "../modules";
import type { AuditModel, User } from "@/ts/types/models";

export interface ISmartFormField {
  component: Component | string;
  key: string;
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

export interface INestedSetEntity<T> {
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
  itemValue?: keyof T | string | ((item: T) => T[keyof T] | string);
  itemTitle?: keyof T | string | ((item: T) => string);
}

export interface IRelationTreeProps<T> extends IItems<T> {
  moduleKey: string;
  modelValue: T[];
  initialValues?: Partial<T>;
  morph?: boolean;
}

export interface IRelationCardProps {
  loading?: boolean;
  title?: string;
  icon?: string;
}

export type THistoryItem = AuditModel & { user: User }

export interface IHistoryBottomSheetProps {
  modelValue: boolean;
  history: THistoryItem[]
}

export interface IJSONEditorProps {
  loading?: boolean;
  title: string;
  icon?: string;
  modelValue?: Record<string, string>;
}

export interface ITreeViewProps<T> extends IItems<T> {
  items: T[]
}

export interface ITreeViewItemProps<T> {
  item: T;
  depth: number;
  search: string;
  getItemValue: (item: T) => T[keyof T] | string;
  getItemTitle: (item: T) => string;
  onItemClick: (item: T) => void;
}


export interface PIGenetic<T> {
  data: T;
  update: (value: T) => void;
}