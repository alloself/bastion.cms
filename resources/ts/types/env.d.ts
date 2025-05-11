/// <reference types="vite/client" />

interface ImportMetaEnv {
  readonly VITE_PUBLIC_API_URL: string
  // добавьте другие переменные окружения при необходимости
}

interface ImportMeta {
  readonly env: ImportMetaEnv
} 