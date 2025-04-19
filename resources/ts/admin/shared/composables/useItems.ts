import { get } from "lodash"
import type { IBaseEntity, IItems } from "../types"

export const useItems = <T extends IBaseEntity>({ itemTitle, itemValue }: IItems<T>) => {

  const getItemValue = (item: T) => {
    if (!itemValue) {
      return get(item, 'id');
    }
    return typeof itemValue === 'function' ? itemValue(item) : get(item, itemValue)
  }

  const getItemTitle = (item: T) => {
    if (!itemTitle) {
      return get(item, 'name');
    }
    return typeof itemTitle === 'function' ? itemTitle(item) : get(item, itemTitle)
  }

  return {
    getItemValue,
    getItemTitle
  }
}