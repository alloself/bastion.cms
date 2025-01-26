export {}
declare global {
  export namespace Models {

    export interface User {
      // columns
      id: number
      name: string
      email: string
      email_verified_at: string | null
      password?: string
      remember_token?: string | null
      created_at: string | null
      updated_at: string | null
      // relations
      notifications: DatabaseNotification[]
    }

  }
}
