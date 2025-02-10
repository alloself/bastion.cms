<template>
    <v-data-table-server
        v-if="module"
        :headers="module.headers"
        show-select
        v-model="selected"
        :items-length="data.total || 0"
        :items="data?.items"
        :loading="loading"
        v-bind="tableProps"
        hide-no-data
        @click:row="onRowClick"
        fixed-header
        :height="`calc(100svh - ${mainRect.top + 54 + 40}px)`"
        fixed-footer
    >
        <template #loading>
            <v-skeleton-loader type="table-row@20"></v-skeleton-loader>
        </template>

        <template #top>
            <v-text-field
                prepend-inner-icon="mdi-magnify"
                variant="solo"
                :density="'compact'"
                label="Поиск"
                v-model="search"
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
                            class="mr-4"
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
                    :length="data?.last_page"
                    :total-visible="6"
                ></v-pagination>
            </div>
        </template>
    </v-data-table-server>
</template>

<script lang="ts" setup generic="T extends IBaseEntity">
import type { IBaseEntity, IServerDataList } from "@admin/shared/types";
import type { IModule } from "..";
import { client, loading } from "@admin/shared/api/axios";
import { useLayout } from "vuetify";
import { ref } from "vue";
import { useRouter } from "vue-router";
import { capitalize } from "lodash";

const { module } = defineProps<{
    module?: IModule;
}>();

const { mainRect } = useLayout();
const router = useRouter();
const selected = ref([]);

const data = ref<IServerDataList<T>>({
    total: 0,
    items: [],
    current_page: 0,
    last_page: 0,
    per_page: 0,
});

const tableProps = ref({
    page: 1,
    itemsPerPage: 15,
    sortBy: []
});

const onRowClick = (event: InputEvent, { item }: { item: T }) => {};

const search = ref((router.currentRoute.value.query.search as string) || "");

const setSearch = () => {
    tableProps.value.page = 1;
};

const onDelete = () => {};
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
</style>
