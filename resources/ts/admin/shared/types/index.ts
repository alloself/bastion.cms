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
  initialItems?: Record<string, unknown>;
}

export interface IBaseEntity extends Record<string, any> {
  id: string;
}

export interface INestedSetEntity<T> {
  children: T[];
  parent_id: string;
  pivot?: Record<string, unknown>
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
  initialItems?: Record<string, T[]>;
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
  modelValue?: T[keyof T] | string;
  loading?: boolean;
  initialItems?: T[];
  returnObject?: boolean;
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
  ordered?: boolean;
  pivot?: Record<string, unknown> & { order: number }
  initialItems?: Record<string, T[]>
}

export interface IRelationTableProps<T> extends IItems<T> {
  moduleKey: string;
  modelValue: T[];
  initialValues?: Partial<T>;
  morph?: boolean;
  ordered?: boolean;
  headers: Record<string, any>[]
  title?: string;
}

export interface IRelationCardProps<T> {
  loading?: boolean;
  module?: IModule;
  actions?: Array<'search' | 'create' | 'delete'>
  getItemTitle?: (item: T) => T[keyof T] | string
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
  modelValue?: Record<string, any>;
}

export interface PIGenetic<T> {
  data: T;
  update: (value: T) => void;
}

export interface IOrderedEntity {
  order: number;
  pivot: Record<string, unknown> & { order: number, link_id?: string }
}

export type Maybe<T> = T | null | undefined;

export interface ITab {
  id: string;              // уникальный идентификатор вкладки
  title: string;           // заголовок вкладки для отображения
  fullPath: string;        // полный путь маршрута (например, "/pages/1?filter=x")
  routeName?: string;      // имя маршрута (если используете именованные маршруты)
  params?: Record<string, any>; // параметры маршрута (если есть, например { id: 1 })
  query?: Record<string, any>;  // query-параметры маршрута (если есть)
}

export interface IScreen {
  id: string;            // идентификатор экрана (можно использовать для множественных экранов)
  tabs: ITab[];          // список вкладок, открытых в этом экране
  activeTabId: string;   // идентификатор текущей активной вкладки
  name?: string;         // название экрана (опционально, на случай нескольких экранов)
}
