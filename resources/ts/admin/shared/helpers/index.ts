import type { LocationQuery } from "vue-router";

// Утилиты для преобразования camelCase ↔ snake_case
export const camelToSnakeCase = (str: string) =>
  str.replace(/[A-Z]/g, letter => `_${letter.toLowerCase()}`);

export const snakeToCamelCase = (str: string) =>
  str.toLowerCase().replace(/([-_][a-z])/g, group =>
    group.slice(1).toUpperCase()
  );

// Преобразование объекта в query-строку с поддержкой сложных структур
export function toQueryString<T extends Record<string, unknown>>(
  obj: T,
  prefix = ""
): string {
  const encode = encodeURIComponent;
  const entries = [];

  for (const [key, value] of Object.entries(obj)) {
    const currentKey = prefix + camelToSnakeCase(key);

    if (Array.isArray(value)) {
      for (const [index, item] of value.entries()) {
        if (item && typeof item === "object") {
          entries.push(...toQueryString(item, `${currentKey}[${index}][`));
        } else {
          entries.push(`${encode(`${currentKey}[${index}]`)}=${encode(String(item))}`);
        }
      }
    } else if (value && typeof value === "object") {
      entries.push(...toQueryString(value as Record<string, unknown>, `${currentKey}[`));
    } else if (value !== undefined && value !== null) {
      entries.push(`${encode(currentKey)}=${encode(String(value))}`);
    }
  }

  return entries.join("&");
}

// Конвертация параметров с обработкой типов данных
export function parseValue(v: string): unknown {
  if (v === "true") return true;
  if (v === "false") return false;
  if (!isNaN(Number(v)) && v.trim() !== "") return Number(v);
  return v;
}

// Глубокий парсинг ключей параметров
export function parseKeyParts(key: string): (string | number)[] {
  return key.split(/[[\]]/g)
    .filter(Boolean)
    .map((part, index) => {
      const camelPart = snakeToCamelCase(part);
      return index > 0 && /^\d+$/.test(camelPart)
        ? parseInt(camelPart, 10)
        : camelPart;
    });
}

// Рекурсивное создание структуры объекта
export function assignDeep(
  target: Record<string, unknown>,
  keys: (string | number)[],
  value: unknown
): void {
  let current = target;
  for (let i = 0; i < keys.length - 1; i++) {
    const key = keys[i];
    const nextKey = keys[i + 1];

    if (current[key] === undefined) {
      current[key] = typeof nextKey === "number" ? [] : {};
    }

    current = current[key] as Record<string, unknown>;
  }

  const lastKey = keys[keys.length - 1];
  current[lastKey] = value;
}

// Основная функция парсинга query-параметров
export function parseQueryParams(query: LocationQuery): Record<string, unknown> {
  const result: Record<string, unknown> = {};

  for (const [rawKey, rawValue] of Object.entries(query)) {
    if (rawValue === null || rawValue === undefined) continue;

    const keys = parseKeyParts(rawKey);
    const value = Array.isArray(rawValue)
      ? rawValue.filter((v): v is string => v !== null).map(parseValue)
      : parseValue(rawValue);

    assignDeep(result, keys, value);
  }

  return result;
}

// Вспомогательные функции для API
export function prepareQueryParams<T extends Record<string, unknown>>(
  params: T
): Record<string, string> {
  const prepared: Record<string, string> = {};

  const processValue = (key: string, value: unknown, parentKey = "") => {
    const fullKey = parentKey ? `${parentKey}[${key}]` : camelToSnakeCase(key);

    if (Array.isArray(value)) {
      value.forEach((item, index) => {
        if (typeof item === "object" && item !== null) {
          for (const [subKey, subValue] of Object.entries(item)) {
            processValue(subKey, subValue, `${fullKey}[${index}]`);
          }
        } else {
          prepared[`${fullKey}[${index}]`] = String(item);
        }
      });
    } else if (value && typeof value === "object") {
      for (const [subKey, subValue] of Object.entries(value)) {
        processValue(subKey, subValue, fullKey);
      }
    } else if (value) {
      prepared[fullKey] = String(value);
    }
  };

  for (const [key, value] of Object.entries(params)) {
    processValue(key, value);
  }

  return prepared;
}

function parseMargin(value: string): number {
  const parsed = parseFloat(value);
  return Number.isNaN(parsed) ? 0 : parsed;
}

export function getFreeSpace(parentElement: HTMLElement) {
  const parentWidth = parentElement.clientWidth;
  const parentHeight = parentElement.clientHeight;

  let usedWidth = 0;
  let usedHeight = 0;

  Array.from(parentElement.children).forEach(child => {
    // Проверка типа элемента
    if (!(child instanceof HTMLElement)) return;

    const style = window.getComputedStyle(child);

    // Пропускаем скрытые элементы
    if (style.display === 'none' || style.visibility === 'hidden') return;

    // Расчет margin
    const marginX = parseMargin(style.marginLeft) + parseMargin(style.marginRight);
    const marginY = parseMargin(style.marginTop) + parseMargin(style.marginBottom);

    // Расчет размеров элемента
    const width = style.display === 'flex'
      ? child.scrollWidth
      : child.offsetWidth;

    const height = style.display === 'flex'
      ? child.scrollHeight
      : child.offsetHeight;

    usedWidth += width + marginX;
    usedHeight += height + marginY;
  });

  return {
    freeWidth: Math.max(0, parentWidth - usedWidth),
    freeHeight: Math.max(0, parentHeight - usedHeight),
    parentWidth,
    parentHeight,
    usedWidth,
    usedHeight
  };
}