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

export interface ContentBlock {
  // columns
  id: number
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
}
export type ContentBlockfillable = Pick<ContentBlock, >

export interface Link {
  // columns
  id: string
  title: string
  subtitle: string | null
  slug: string
  url: string | null
  linkable_type: string
  linkable_id: string
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
