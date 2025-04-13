<template>
    <v-data-table-server
        v-if="module"
        :headers="module.headers"
        show-select
        v-model="selected"
        :items-length="listData.total || 0"
        :items="listData?.data"
        :loading="loading"
        v-model:page="tableProps.page"
        v-model:items-per-page="tableProps.itemsPerPage"
        v-model:sort-by="tableProps.sortBy"
        hide-no-data
        @click:row="onRowClick"
        fixed-header
        :height="`calc(100svh - ${mainRect.top + 54 + 52}px)`"
        fixed-footer
    >
        <template #loading>
            <v-skeleton-loader type="table-row@20"></v-skeleton-loader>
        </template>
        <template #[`item.url`]="{ item }">
            <a :href="item.url" target="_blank">{{ item.url }}</a>
        </template>
        <template #[`item.preview`]="{ item }">
            <img
                :src="item.url"
                style="width: 150px; height: 150px; object-fit: contain"
            />
        </template>

        <template #top>
            <v-text-field
                prepend-inner-icon="mdi-magnify"
                variant="solo"
                :density="'compact'"
                label="Поиск"
                class="ml-1 mt-2 search"
                v-model="tableProps.search"
                clearable
                hide-details
                @click:clear="setSearch"
                @keypress.enter="setSearch"
                single-line
            ></v-text-field>
        </template>
        <template #bottom>
            <v-divider role="separator"></v-divider>
            <div class="v-data-table-footer">
                <v-tooltip location="top" text="Создать" color="primary">
                    <template #activator="{ props }">
                        <v-btn
                            icon
                            size="small"
                            v-bind="props"
                            :loading="loading"
                            class="mr-2"
                            flat
                            :to="{ name: `${capitalize(module.key)}Create` }"
                        >
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </template>
                    <span>Создать</span>
                </v-tooltip>
                <v-tooltip
                    location="top"
                    text="Удалить выбранное"
                    color="primary"
                >
                    <template #activator="{ props }">
                        <v-btn
                            icon
                            size="small"
                            :loading="loading"
                            v-bind="props"
                            flat
                            @click="onDelete"
                        >
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                    </template>
                    <span>Удалить выбранное</span>
                </v-tooltip>

                <v-spacer></v-spacer>
                <v-select
                    density="compact"
                    class="per-page-counter"
                    v-model="tableProps.itemsPerPage"
                    variant="solo"
                    hide-details
                    :items="[5, 10, 15, 25, 50, 100]"
                ></v-select>
                <v-pagination
                    :density="'compact'"
                    rounded="circle"
                    :showFirstLastPag="true"
                    v-model="tableProps.page"
                    :length="listData?.last_page"
                    :total-visible="6"
                ></v-pagination>
            </div>
        </template>
    </v-data-table-server>
</template>

<script lang="ts" setup generic="T extends IBaseEntity">
import type {
    IBaseEntity,
    IServerDataList,
    ITableProps,
} from "@admin/shared/types";
import { getModuleUrlPart, type IModule } from "..";
import { client, loading } from "@admin/shared/api/axios";
import { useLayout } from "vuetify";
import { onBeforeMount, ref, watch } from "vue";
import { useRouter } from "vue-router";
import { prepareQueryParams, parseQueryParams, capitalize } from "@admin/shared/helpers";

const { module } = defineProps<{
    module: IModule;
}>();

const { mainRect } = useLayout();
const router = useRouter();
const selected = ref([]);

const listData = ref<IServerDataList<T>>({
    total: 0,
    data: [],
    current_page: 0,
    last_page: 0,
    per_page: 0,
});

const tableProps = ref<ITableProps>({
    page: 1,
    itemsPerPage: 15,
    sortBy: [],
    search: "",
});

const onRowClick = (event: InputEvent, { item }: { item: T }) => {
    if (!(event.target instanceof HTMLInputElement)) {
        router.push({
            name: `${capitalize(module.key)}Detail`,
            params: { id: item.id },
        });
    }
};

const setSearch = () => {
    tableProps.value.page = 1;
    getItems(tableProps.value);
};

const getItems = async (options: Record<string, unknown>) => {
    try {
        const params = prepareQueryParams({
            ...options,
            with: module.relations,
        });
        const { data } = await client.get(
            `/api/admin/${getModuleUrlPart(module.key)}`,
            {
                params,
            }
        );

        router.replace({
            query: params,
        });

        listData.value = data;
    } catch (e) {
        console.log(e);
    }
};

const onDelete = async () => {
    try {
        await client.post(
            `/api/admin/destroy/${getModuleUrlPart(module.key)}`,
            {
                ids: selected.value,
            }
        );
        selected.value = [];
        getItems(tableProps.value);
    } catch (e) {
        console.log(e);
    }
};

onBeforeMount(() => {
    const parsedParams = parseQueryParams(router.currentRoute.value.query);
    tableProps.value = { ...tableProps.value, ...parsedParams };
});

watch(
    tableProps,
    async (value) => {
        await getItems(value);
    },
    {
        deep: true,
    }
);
</script>

<style lang="scss">
.v-data-table__tr {
    cursor: pointer;
}

.per-page-counter {
    max-width: 90px;
    width: 90px;

    .v-field {
        box-shadow: none;
    }
}

.search {
    .v-field {
        box-shadow: none;
    }
}
</style>
