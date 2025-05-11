<template>
    <fwb-pagination
        class="app-pagination mt-12 justify-center"
        v-model="currentPage"
        :total-items="total"
        large
        :show-labels="false"
        :per-page="perPage"
    >
        <template #prev-button="{ decreasePage }">
            <button
                class="app-pagination__item app-pagination__item--prev"
                @click="decreasePage()"
                :disabled="currentPage === 1"
            >
                <div class="svg-icon rotate-90">
                    <svg>
                        <use xlink:href="#arrow-down"></use>
                    </svg>
                </div>
            </button>
        </template>
        <template #next-button="{ increasePage }">
            <button
                class="app-pagination__item app-pagination__item--prev"
                @click="increasePage()"
                :disabled="currentPage === Math.ceil(Number(total) / Number(perPage))"
            >
                <div class="svg-icon -rotate-90">
                    <svg>
                        <use xlink:href="#arrow-down"></use>
                    </svg>
                </div>
            </button>
        </template>
        <template v-slot:page-button="{ page, setPage }">
            <button
                class="app-pagination__item"
                :class="{ 'is-active': page === Number(currentPage) }"
                @click="setPage(page)"
            >
                {{ page }}
            </button>
        </template>
    </fwb-pagination>
</template>

<script setup lang="ts">
import { FwbPagination } from "flowbite-vue";
import { computed } from "vue";

const props = defineProps<{
    searchKey: string;
    total: number;
    current: string;
    perPage: number;
    sortBy?: string;
    order?: string;
    sortByAttribute?: string;
    usePrefix?: boolean;
}>();

// Получаем актуальный номер страницы из URL-параметров
function getCurrentPageFromUrl(): number {
    if (typeof window === 'undefined') {
        return Number(props.current) || 1;
    }
    
    try {
        const url = new URL(window.location.href);
        const searchParams = url.searchParams;
        
        // Получаем параметр страницы в зависимости от режима
        let pageParam;
        if (props.usePrefix && props.searchKey) {
            pageParam = searchParams.get(`${props.searchKey}_page`);
        } else {
            pageParam = searchParams.get('page');
        }
        
        // Преобразуем в число и проверяем корректность
        const pageNum = pageParam ? parseInt(pageParam, 10) : (Number(props.current) || 1);
        return isNaN(pageNum) ? 1 : pageNum;
    } catch (e) {
        console.error("Error parsing URL parameters:", e);
        return Number(props.current) || 1;
    }
}

// Конвертируем значения для безопасной работы с ними
const pageNumber = getCurrentPageFromUrl();
const totalItems = Number(props.total) || 0;
const itemsPerPage = Number(props.perPage) || 15;

// Флаг использования префикса
const usePrefix = !!props.usePrefix;

/**
 * Функция для безопасного формирования URL с учетом кириллицы
 */
const navigateToPage = (page: number) => {
    if (typeof window !== 'undefined') {
        // Получаем текущие параметры URL
        const currentUrl = new URL(window.location.href);
        const searchParams = currentUrl.searchParams;
        
        // Устанавливаем новые параметры в зависимости от режима
        if (usePrefix && props.searchKey) {
            // Режим с префиксом - используем ключ для формирования имени параметра
            searchParams.set(`${props.searchKey}_page`, String(page));
            searchParams.set(`${props.searchKey}_per_page`, String(itemsPerPage));
            
            // Сохраняем параметры сортировки, если они есть
            // Проверяем сортировку по атрибуту (приоритетно)
            if (props.sortByAttribute) {
                searchParams.delete(`${props.searchKey}_sort_by`); // Удаляем обычную сортировку
                searchParams.set(`${props.searchKey}_sort_by_attribute`, props.sortByAttribute);
                
                if (props.order) {
                    searchParams.set(`${props.searchKey}_order`, props.order);
                }
            } 
            // Если нет атрибута, проверяем обычную сортировку
            else if (props.sortBy) {
                searchParams.set(`${props.searchKey}_sort_by`, props.sortBy);
                searchParams.delete(`${props.searchKey}_sort_by_attribute`); // Удаляем сортировку по атрибуту
                
                if (props.order) {
                    searchParams.set(`${props.searchKey}_order`, props.order);
                }
            }
        } else {
            // Режим без префикса - используем стандартные имена параметров
            searchParams.set('page', String(page));
            searchParams.set('per_page', String(itemsPerPage));
            
            // Сохраняем параметры сортировки, если они есть
            // Проверяем сортировку по атрибуту (приоритетно)
            if (props.sortByAttribute) {
                searchParams.delete('sort_by'); // Удаляем обычную сортировку
                searchParams.set('sort_by_attribute', props.sortByAttribute);
                
                if (props.order) {
                    searchParams.set('order', props.order);
                }
            } 
            // Если нет атрибута, проверяем обычную сортировку
            else if (props.sortBy) {
                searchParams.set('sort_by', props.sortBy);
                searchParams.delete('sort_by_attribute'); // Удаляем сортировку по атрибуту
                
                if (props.order) {
                    searchParams.set('order', props.order);
                }
            }
        }
        
        // Формируем новый URL и переходим по нему
        window.location.href = currentUrl.toString();
    }
};

// Используем реактивную переменную для текущей страницы
const currentPage = computed({
    get: () => pageNumber,
    set: navigateToPage
});
</script>

<style lang="sass" scoped></style>
