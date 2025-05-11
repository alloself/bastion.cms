<template>
    <div class="app-sort" :class="{'is-open': state.isOpen}">
        <div class="app-sort__current" @click="onClickHandler">
            <div class="app-sort__value">{{ currentSort?.text }}</div>
            <div class="app-sort__arrow">
                <div class="svg-icon">
                    <svg><use xlink:href="#arrow-down"></use></svg>
                </div>
            </div>
        </div>
        <div 
            v-if="state.sortList.length"
            class="app-sort__dropdown"
            :class="dropdownClass"
        >
            <ul>
                <li
                    v-for="(item, itemIndex) in state.sortList"
                    :key="itemIndex" 
                    :class="{'text-brand': item.current}"
                    @click="onSortChange(item)"
                >{{ item.text }}</li>
            </ul>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, reactive, computed } from 'vue'
import queryString from 'query-string'

interface SortItem {
    current: boolean,
    key: string,
    text: string,
    value: string,
    attribute?: string
}

const props = withDefaults(
    defineProps<{
        sortItems?: SortItem[],
        dropdownClass?: string,
        sortKey: string,
        usePrefix?: boolean
    }>(),
    {
        sortItems: () => [],
        dropdownClass: 'left-0 origin-top-left md:left-auto md:right-0 md:origin-top-right',
        usePrefix: false
    }
)

const emits = defineEmits(['change'])

const state = reactive<{
    isOpen: boolean,
    sortList: SortItem[],
}>({
    isOpen: false,
    sortList: []
})

const currentSort = computed<SortItem | undefined>(() => {
    return state.sortList.find(item => item.current)
})

/**
 * Функция для безопасного формирования URL с учетом кириллицы
 */
function onSortChange(sortItem: SortItem) {
    state.isOpen = false

    if (sortItem.current) {
        return
    }

    state.sortList.forEach(item => item.current = false)
    sortItem.current = true
    
    if (typeof window !== 'undefined') {
        // Используем URL API для безопасной работы с URL
        const currentUrl = new URL(window.location.href);
        const searchParams = currentUrl.searchParams;
        
        // Проверяем, является ли выбранный элемент сортировкой "По умолчанию"
        const isDefaultSort = sortItem.key === 'order' && sortItem.text.includes('По умолчанию');
        
        if (props.usePrefix && props.sortKey) {
            // Режим с префиксом
            if (isDefaultSort) {
                // Если выбрана сортировка "По умолчанию", удаляем все параметры сортировки
                searchParams.delete(`${props.sortKey}_sort_by`);
                searchParams.delete(`${props.sortKey}_sort_by_attribute`);
                searchParams.delete(`${props.sortKey}_order`);
            } else if (sortItem.attribute) {
                // Если есть атрибут, используем сортировку по атрибуту
                searchParams.delete(`${props.sortKey}_sort_by`); // Удаляем обычный параметр сортировки
                searchParams.set(`${props.sortKey}_sort_by_attribute`, sortItem.attribute);
                searchParams.set(`${props.sortKey}_order`, sortItem.value);
            } else {
                // Иначе используем обычную сортировку
                searchParams.set(`${props.sortKey}_sort_by`, sortItem.key);
                searchParams.set(`${props.sortKey}_order`, sortItem.value);
                searchParams.delete(`${props.sortKey}_sort_by_attribute`); // Удаляем параметр сортировки по атрибуту
            }
            
            // Сбрасываем пагинацию при изменении сортировки
            if (searchParams.has(`${props.sortKey}_page`)) {
                searchParams.set(`${props.sortKey}_page`, '1');
            }
        } else {
            // Режим без префикса
            if (isDefaultSort) {
                // Если выбрана сортировка "По умолчанию", удаляем все параметры сортировки
                searchParams.delete('sort_by');
                searchParams.delete('sort_by_attribute');
                searchParams.delete('order');
            } else if (sortItem.attribute) {
                // Если есть атрибут, используем сортировку по атрибуту
                searchParams.delete('sort_by'); // Удаляем обычный параметр сортировки
                searchParams.set('sort_by_attribute', sortItem.attribute);
                searchParams.set('order', sortItem.value);
            } else {
                // Иначе используем обычную сортировку
                searchParams.set('sort_by', sortItem.key);
                searchParams.set('order', sortItem.value);
                searchParams.delete('sort_by_attribute'); // Удаляем параметр сортировки по атрибуту
            }
            
            // Сбрасываем пагинацию при изменении сортировки
            if (searchParams.has('page')) {
                searchParams.set('page', '1');
            }
        }
        
        // Переходим по новому URL
        window.location.href = currentUrl.toString();
    }
    
    emits('change', sortItem)
}

function onClickHandler() {
    state.isOpen = !state.isOpen
}

function onClickOutside(event: Event) {
    const target = event.target as HTMLElement

    if (!target.classList.contains('app-sort') && !target.closest('.app-sort')) {
        state.isOpen = false
    }
}

function initCurrentSort() {
    if (typeof window !== 'undefined') {
        // Используем URL API для безопасной работы с параметрами
        const currentUrl = new URL(window.location.href);
        const searchParams = currentUrl.searchParams;
        
        let sortBy, order, attribute;
        
        if (props.usePrefix && props.sortKey) {
            sortBy = searchParams.get(`${props.sortKey}_sort_by`);
            order = searchParams.get(`${props.sortKey}_order`);
            attribute = searchParams.get(`${props.sortKey}_sort_by_attribute`);
        } else {
            sortBy = searchParams.get('sort_by');
            order = searchParams.get('order');
            attribute = searchParams.get('sort_by_attribute');
        }
        
        // Если нет ни sort_by, ни sort_by_attribute, используем первый элемент как активный по умолчанию
        if (!sortBy && !attribute) {
            if (state.sortList.length > 0) {
                state.sortList[0].current = true;
            }
            return;
        }
        
        // Сначала ищем соответствие по атрибуту (если он есть)
        if (attribute && order) {
            const attributeItem = state.sortList.find(item => {
                return item.attribute === attribute && item.value === order;
            });
            
            if (attributeItem) {
                state.sortList.forEach(i => i.current = false);
                attributeItem.current = true;
                return;
            }
        }
        
        // Затем ищем соответствие по обычному полю (если нет соответствия по атрибуту)
        if (sortBy && order) {
            const item = state.sortList.find(item => {
                return item.key === sortBy && item.value === order && !item.attribute;
            });
            
            if (item) {
                state.sortList.forEach(i => i.current = false);
                item.current = true;
                return;
            }
        }
        
        // Если ничего не найдено, используем первый элемент как активный
        if (state.sortList.length > 0) {
            state.sortList.forEach(i => i.current = false);
            state.sortList[0].current = true;
        }
    }
}

onMounted(() => {
    state.sortList = [...props.sortItems]
    initCurrentSort()
    document.addEventListener('click', onClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', onClickOutside)
})
</script>

<style lang="scss">
.app-sort {
    @apply 
        relative 
        z-[50]
        text-[16px] 
        font-semibold 
        tracking-[-0.32px]
        select-none
    ;

    &__current {
        -webkit-tap-highlight-color: transparent;
        @apply
            cursor-pointer
            flex 
            items-center 
            gap-2
        ;
    }

    &__arrow {
        @apply 
            w-6 
            h-6 
            rounded-full 
            border 
            border-neutral 
            flex 
            items-center 
            flex-none
            justify-center
            transition-all
            duration-200
        ;
    }

    &__dropdown {
        @apply 
            absolute
            top-[140%]
            transition-all
            duration-200
            px-5
            py-3
            rounded-xl
            shadow-lg
            border
            border-opacity-50
            scale-0
            opacity-0
            pointer-events-none
            border-neutral
            bg-white
        ;

        ul {
            @apply space-y-1;

            li {
                @apply 
                    cursor-pointer
                    whitespace-nowrap
                    hover:text-brand
                    [&.is-active]:text-brand
                ;
            }
        }
    }

    &.is-open {
        .app-sort__arrow {
            @apply 
                rotate-180
            ;
        }

        .app-sort__dropdown {
            @apply 
                scale-100
                opacity-100
                pointer-events-auto
            ;
        }
    }
}
</style>