export interface Attribute {
  // columns
  id: string
  name: string
  key: string
  deleted_at: string | null
  created_at: string | null
  updated_at: string | null
  // relations
  attributeable?: Attribute
  audits?: AuditModel[]
}
export type Attributefillable = Pick<Attribute, 'name' | 'key'>

export interface AuditModel {
  // columns
  id: string
  user_type: string | null
  user_id: string | null
  event: string
  auditable_type: string
  auditable_id: string
  old_values: Record<string, unknown> | null
  new_values: Record<string, unknown> | null
  url: string | null
  ip_address: string | null
  user_agent: string | null
  tags: string | null
  created_at: string | null
  updated_at: string | null
  // relations
  auditable?: AuditModel
  user?: AuditModel
}
export type AuditModelfillable = Pick<AuditModel, >

export interface BaseModel {
  // relations
  audits?: AuditModel[]
}
export type BaseModelfillable = Pick<BaseModel, >

export interface ContentBlock {
  // columns
  id: string
  name: string
  content: string | null
  order: number
  _lft: number
  _rgt: number
  parent_id: string | null
  created_at: string | null
  updated_at: string | null
  deleted_at: string | null
  template_id: string | null
  // relations
  template?: Template
  audits?: AuditModel[]
  parent?: ContentBlock
  children?: ContentBlock[]
  link?: Link
}
export type ContentBlockfillable = Pick<ContentBlock, 'name' | 'content' | 'order' | 'parent_id' | 'template_id'>

export interface DataCollection {
  // columns
  id: string
  name: string
  meta: Record<string, unknown> | null
  order: number
  _lft: number
  _rgt: number
  parent_id: string | null
  template_id: string | null
  page_id: string | null
  created_at: string | null
  updated_at: string | null
  deleted_at: string | null
  // relations
  page?: Page
  template?: Template
  audits?: AuditModel[]
  parent?: DataCollection
  children?: DataCollection[]
  link?: Link
  content_blocks?: ContentBlock[]
  attributes?: Attribute[]
  images?: File[]
  files?: File[]
  data_entities?: DataEntity[]
}
export type DataCollectionfillable = Pick<DataCollection, 'name' | 'meta' | 'parent_id' | 'page_id' | 'order' | 'template_id'>

export interface DataEntity {
  // columns
  id: string
  name: string | null
  meta: Record<string, unknown> | null
  content: string | null
  order: number
  parent_id: string | null
  template_id: string | null
  created_at: string | null
  updated_at: string | null
  deleted_at: string | null
  // relations
  data_entityables?: DataEntityable[]
  variants?: DataEntity[]
  audits?: AuditModel[]
}
export type DataEntityfillable = Pick<DataEntity, 'name' | 'meta' | 'template_id' | 'content' | 'order' | 'parent_id'>

export interface File {
  // columns
  id: string
  url: unknown
  name: string
  extension: string
  created_at: string | null
  updated_at: string | null
  // relations
  fileables?: Fileable[]
}
export type Filefillable = Pick<File, 'url' | 'name' | 'extension'>

export interface Link {
  // columns
  id: string
  title: string
  subtitle: string | null
  slug: string
  url: string | null
  linkable_id: string | null
  linkable_type: string | null
  deleted_at: string | null
  created_at: string | null
  updated_at: string | null
  // relations
  linkable?: Link
  audits?: AuditModel[]
}
export type Linkfillable = Pick<Link, 'title' | 'subtitle' | 'slug' | 'url'>

export interface Page {
  // columns
  id: string
  meta: Record<string, unknown> | null
  index: boolean
  _lft: number
  _rgt: number
  parent_id: string | null
  created_at: string | null
  updated_at: string | null
  deleted_at: string | null
  template_id: string | null
  // relations
  template?: Template
  link?: Link
  audits?: AuditModel[]
  parent?: Page
  children?: Page[]
  content_blocks?: ContentBlock[]
  attributes?: Attribute[]
  images?: File[]
  files?: File[]
  data_collections?: DataCollection[]
}
export type Pagefillable = Pick<Page, 'index' | 'meta' | 'parent_id' | 'template_id'>

export interface Permission {
  // columns
  uuid: string
  name: string
  guard_name: string
  created_at: string | null
  updated_at: string | null
  // relations
  roles?: Role[]
  users?: User[]
  permissions?: Permission[]
}
export type Permissionfillable = Pick<Permission, >

export interface ContentBlockable {
}
export type ContentBlockablefillable = Pick<ContentBlockable, >

export interface DataCollectionable {
}
export type DataCollectionablefillable = Pick<DataCollectionable, >

export interface DataEntityable {
  // columns
  id: string
  data_entityable_type: string
  data_entityable_id: string
  key: string | null
  order: number
  data_entity_id: string | null
  // relations
  data_entityable?: DataEntityable
  data_entity?: DataEntity
  link?: Link
}
export type DataEntityablefillable = Pick<DataEntityable, 'data_entity_id' | 'data_entityable_type' | 'data_entityable_id' | 'key' | 'order' | 'link_id'>

export interface Fileable {
  // columns
  id: string
  fileable_type: string
  fileable_id: string
  type: string
  key: string | null
  order: number
  file_id: string
  // relations
  fileable?: Fileable
  file?: File
}
export type Fileablefillable = Pick<Fileable, 'file_id' | 'fileable_id' | 'fileable_type'>

export interface Role {
  // columns
  uuid: string
  name: string
  guard_name: string
  created_at: string | null
  updated_at: string | null
  // relations
  permissions?: Permission[]
  users?: User[]
}
export type Rolefillable = Pick<Role, >

export interface Template {
  // columns
  id: string
  name: string
  value: string
  deleted_at: string | null
  created_at: string | null
  updated_at: string | null
  // relations
  audits?: AuditModel[]
}
export type Templatefillable = Pick<Template, 'name' | 'value'>

export interface User {
  // columns
  id: string
  first_name: string
  last_name: string
  middle_name: string | null
  email: string
  created_at: string | null
  updated_at: string | null
  deleted_at: string | null
  // relations
  notifications?: DatabaseNotification[]
  roles?: Role[]
  permissions?: Permission[]
  audits?: AuditModel[]
}
export type Userfillable = Pick<User, 'first_name' | 'last_name' | 'middle_name' | 'email' | 'password'>
