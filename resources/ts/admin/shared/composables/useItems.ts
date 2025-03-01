import { get } from "lodash"
import type { IItems } from "../types"

export const useItems = <T>({ itemTitle, itemValue }: IItems<T>) => {

  const getItemValue = (item: T) => {
    if (!itemValue) {
      return;
    }
    return typeof itemValue === 'function' ? itemValue(item) : get(item, itemValue)
  }

  const getItemTitle = (item: T) => {
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