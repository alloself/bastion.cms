import { get } from "lodash"
import type { IItems } from "../types"

export const useItems = <T>({ itemTitle, itemValue }: IItems<T>) => {

  const getItemValue = (item: T): string | T[keyof T] | undefined => {
    if (!itemValue) {
      return;
    }
    return typeof itemValue === 'function' ? itemValue(item) : get(item, itemValue)
  }

  const getItemTitle = (item: T): string | T[keyof T] | undefined => {
    if (!itemTitle) {
      return;
    }
    return typeof itemTitle === 'function' ? itemTitle(item) : get(item, itemTitle)
  }

  return {
    getItemValue,
    getItemTitle
  }
}