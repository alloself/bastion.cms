import { get } from "lodash"
import type { IItems } from "../types"

export const useItems = <T>({ itemTitle, itemValue }: IItems<T>) => {

  const getItemValue = (item: T): T[keyof T] | string => {
    if (!itemValue) {
      return 'id';
    }
    return typeof itemValue === 'function' ? itemValue(item) : get(item, itemValue)
  }

  const getItemTitle = (item: T): string => {
    if (!itemTitle) {
      return '';
    }
    return typeof itemTitle === 'function' ? itemTitle(item) : get(item, itemTitle)
  }

  return {
    getItemValue,
    getItemTitle
  }
}