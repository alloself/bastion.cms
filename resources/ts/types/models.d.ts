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
  // counts
  audits_count: number
  // exists
  audits_exists: boolean
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
  // counts
  // exists
}
export type AuditModelfillable = Pick<AuditModel, >

export interface BaseModel {
  // relations
  audits?: AuditModel[]
  // counts
  audits_count: number
  // exists
  audits_exists: boolean
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
  images?: File[]
  files?: File[]
  data_entities?: DataEntity[]
  data_collections?: DataCollection[]
  attributes?: Attribute[]
  // counts
  audits_count: number
  children_count: number
  images_count: number
  files_count: number
  data_entities_count: number
  data_collections_count: number
  attributes_count: number
  // exists
  template_exists: boolean
  audits_exists: boolean
  parent_exists: boolean
  children_exists: boolean
  link_exists: boolean
  images_exists: boolean
  files_exists: boolean
  data_entities_exists: boolean
  data_collections_exists: boolean
  attributes_exists: boolean
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
  // mutators
  root: unknown
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
  // counts
  audits_count: number
  children_count: number
  content_blocks_count: number
  attributes_count: number
  images_count: number
  files_count: number
  data_entities_count: number
  // exists
  page_exists: boolean
  template_exists: boolean
  audits_exists: boolean
  parent_exists: boolean
  children_exists: boolean
  link_exists: boolean
  content_blocks_exists: boolean
  attributes_exists: boolean
  images_exists: boolean
  files_exists: boolean
  data_entities_exists: boolean
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
  // mutators
  link: unknown
  // relations
  data_entityables?: DataEntityable[]
  variants?: DataEntity[]
  template?: Template
  audits?: AuditModel[]
  images?: File[]
  files?: File[]
  attributes?: Attribute[]
  content_blocks?: ContentBlock[]
  // counts
  data_entityables_count: number
  variants_count: number
  audits_count: number
  images_count: number
  files_count: number
  attributes_count: number
  content_blocks_count: number
  // exists
  data_entityables_exists: boolean
  variants_exists: boolean
  template_exists: boolean
  audits_exists: boolean
  images_exists: boolean
  files_exists: boolean
  attributes_exists: boolean
  content_blocks_exists: boolean
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
  // counts
  fileables_count: number
  // exists
  fileables_exists: boolean
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
  // counts
  audits_count: number
  // exists
  audits_exists: boolean
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
  // counts
  audits_count: number
  children_count: number
  content_blocks_count: number
  attributes_count: number
  images_count: number
  files_count: number
  data_collections_count: number
  // exists
  template_exists: boolean
  link_exists: boolean
  audits_exists: boolean
  parent_exists: boolean
  children_exists: boolean
  content_blocks_exists: boolean
  attributes_exists: boolean
  images_exists: boolean
  files_exists: boolean
  data_collections_exists: boolean
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
  // counts
  roles_count: number
  users_count: number
  permissions_count: number
  // exists
  roles_exists: boolean
  users_exists: boolean
  permissions_exists: boolean
}
export type Permissionfillable = Pick<Permission, >

export interface Attributeable {
  // columns
  attributeable_type: string
  attributeable_id: string
  value: string | null
  order: number
  attribute_id: string
  // relations
  attributeable?: Attributeable
  attribute?: Attribute
  // counts
  // exists
  attribute_exists: boolean
}
export type Attributeablefillable = Pick<Attributeable, 'attribute_id' | 'attributeable_id' | 'attributeable_type' | 'value' | 'order'>

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
  // counts
  // exists
  data_entity_exists: boolean
  link_exists: boolean
}
export type DataEntityablefillable = Pick<DataEntityable, 'data_entity_id' | 'data_entityable_type' | 'data_entityable_id' | 'key' | 'order'>

export interface Fileable {
  // relations
  fileable?: Fileable
  file?: File
  // counts
  // exists
  file_exists: boolean
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
  // counts
  permissions_count: number
  users_count: number
  // exists
  permissions_exists: boolean
  users_exists: boolean
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
  content_blocks?: ContentBlock[]
  pages?: Page[]
  audits?: AuditModel[]
  attributes?: Attribute[]
  // counts
  content_blocks_count: number
  pages_count: number
  audits_count: number
  attributes_count: number
  // exists
  content_blocks_exists: boolean
  pages_exists: boolean
  audits_exists: boolean
  attributes_exists: boolean
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
  // counts
  notifications_count: number
  roles_count: number
  permissions_count: number
  audits_count: number
  // exists
  notifications_exists: boolean
  roles_exists: boolean
  permissions_exists: boolean
  audits_exists: boolean
}
export type Userfillable = Pick<User, 'first_name' | 'last_name' | 'middle_name' | 'email' | 'password'>
