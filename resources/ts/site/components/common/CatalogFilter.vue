<template>
    <div class="pb-2.5 border-b border-neutral-alpha">
        <div
            class="text-[12px] leading-[1.4] uppercase mb-5 text-dark flex items-center min-h-[28px]"
        >
            <div>Фильтр:</div>
            <div class="ml-auto flex items-center gap-3">
                <button
                    type="button"
                    class="py-1.5 px-2.5 text-[12px] app-button app-button--secondary"
                    @click.prevent="onResetFilter"
                    v-if="state.reset"
                >
                    Сбросить
                </button>
                <button
                    type="button"
                    class="py-1.5 px-2.5 text-[12px] app-button app-button--primary"
                    @click.prevent="reloadWithParams"
                    v-if="state.touched"
                >
                    Применить
                </button>
            </div>
        </div>
        <div
            class="text-[20px] font-semibold leading-[1.1] tracking-[-0.8px] space-y-1"
            @keydown.enter="reloadWithParams()"
        >
            <appdetails
                v-for="(filter, filterIndex) in state.list"
                :key="filterIndex"
                :open="filter.open || false"
            >
                <template #title="{ toggle }">
                    <div @click="toggle" class="cursor-pointer">
                        {{ filter.label }}
                    </div>
                </template>
                <template #content>
                    <template v-if="filter.type === 'range'">
                        <div class="flex py-2 gap-4">
                            <div class="flex-1 min-w-0">
                                <input
                                    class="app-form-control"
                                    type="text"
                                    v-model="filter.model[0]"
                                    placeholder="от"
                                    :name="filter.name"
                                    @input="onInputRange"
                                />
                            </div>
                            <div class="flex-1 min-w-0">
                                <input
                                    class="app-form-control"
                                    type="text"
                                    v-model="filter.model[1]"
                                    placeholder="до"
                                    :name="filter.name"
                                    @input="onInputRange"
                                />
                            </div>
                        </div>
                    </template>
                    <template v-if="filter.type === 'select'">
                        <div class="py-3">
                            <appselect
                                v-model="filter.model"
                                :options="[
                                    { text: 'Выбрать', value: '' },
                                    ...filter.dataEntities,
                                ]"
                                @change="onSelectChange"
                            ></appselect>
                        </div>
                    </template>
                    <template v-if="filter.type === 'checkbox'">
                        <div class="grid grid-cols-2 gap-4 py-3 select-none">
                            <label
                                v-for="(item, itemIndex) in filter.dataEntities"
                                :key="itemIndex"
                                class="inline-flex items-center gap-2"
                            >
                                <div class="app-checkmark w-[22px] h-[22px]">
                                    <input
                                        type="checkbox"
                                        :name="filter.name"
                                        :value="item.value"
                                        v-model="filter.model"
                                        @change="onChangeCheckbox()"
                                    />
                                    <div class="svg-icon">
                                        <svg>
                                            <use xlink:href="#checkmark"></use>
                                        </svg>
                                    </div>
                                </div>
                                <div>{{ item.text }}</div>
                            </label>
                        </div>
                    </template>
                </template>
            </appdetails>
        </div>
    </div>
</template>

<script setup lang="ts">
import queryString from "query-string";
import { reactive, onBeforeMount } from "vue";

type IFilterDataEnitity = {
    text: string;
    value: string;
} & Record<string, string>;

interface IFilterItem {
    name: string;
    label: string;
    open?: boolean;
    type?: "select" | "checkbox" | "range";
    multiple?: boolean;
    attribute?: {
        name: string;
        key: string;
    };
    dataEntities?: IFilterDataEnitity[];
    dataCollection?: any[];
    model?: any;
}

const props = defineProps<{
    list: IFilterItem[];
}>();

const state = reactive<{
    reset: boolean;
    touched: boolean;
    list: IFilterItem[];
}>({
    list: [],
    touched: false,
    reset: false,
});

function serializeUrlParams(): string {
    let paramsObj: Record<any, any> = {};

    state.list.forEach((filter) => {
        if (filter.type === "checkbox" && filter.model.length) {
            paramsObj[filter.name] = [...filter.model];
        }

        if (filter.type === "range" && filter.model.length) {
            paramsObj[filter.name] = [...filter.model];
        }

        if (filter.type === "select" && filter.model !== "") {
            paramsObj[filter.name] = filter.model;
        }
    });

    let urlSearchParams = queryString.stringify(paramsObj, {
        arrayFormat: "comma",
    });

    return urlSearchParams;
}

function reloadWithParams() {
    let urlParams = serializeUrlParams();
    window.location.search = urlParams;
}

function setParamsFromUrl() {
    const parsedUrlSearchParams = queryString.parse(window.location.search, {
        arrayFormat: "comma",
    });

    state.list.forEach((filter) => {
        let paramValue = parsedUrlSearchParams[filter.name];

        if (paramValue) {
            filter.open = true;
            state.reset = true;

            if (filter.type === "checkbox") {
                if (!Array.isArray(paramValue)) {
                    filter.model = [paramValue];
                } else {
                    filter.model = paramValue;
                }
            }
            if (filter.type === "select") {
                filter.model = paramValue;
            }
            if (filter.type === "range") {
                filter.model = paramValue;
            }
        }
    });
}

function onResetFilter() {
    window.location.search = "";
}

function onChangeCheckbox() {
    state.touched = true;
}

function onInputRange() {
    state.touched = true;
}

function onSelectChange() {
    state.touched = true;
}

onBeforeMount(() => {
    state.list = props.list;
    setParamsFromUrl();
});
</script>
