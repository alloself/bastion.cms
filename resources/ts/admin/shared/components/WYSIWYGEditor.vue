<template>
  <v-sheet variant="solo-filled" rounded class="mb-6 wysiwygeditor">
    <v-toolbar density="compact" color="surface">
      <v-btn-group class="flex" v-if="editor">
        <!-- Меню для добавления ссылок -->
        <v-menu v-model="showLinkDialog" :close-on-content-click="false" location="right" offset="16">
          <template v-slot:activator="{ props: menuProps }">
            <v-btn v-bind="menuProps" size="x-small" :color="editor.isActive('link') ? 'primary' : undefined
              " :rounded="0" aria-label="Добавить ссылку">
              <v-icon>mdi-link-variant</v-icon>
            </v-btn>
          </template>

          <v-card width="400">
            <v-card-title class="text-subtitle-1">
              Добавление ссылки
            </v-card-title>
            <v-card-text>
              <v-text-field v-model="linkUrl" label="URL" placeholder="https://example.com" clearable
                @keydown.enter="handleAddLink" />
            </v-card-text>
            <v-card-actions>
              <v-spacer />
              <v-btn variant="text" @click="closeLinkDialog">
                Отмена
              </v-btn>
              <v-btn color="primary" variant="text" @click="handleAddLink">
                Применить
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-menu>

        <!-- Кнопки форматирования -->
        <v-btn v-for="(action, key) in editorActions" :key="key" size="x-small" :color="editor.isActive(key) ? 'primary' : undefined
          " :rounded="0" :aria-label="action.label" @click="action.handler()">
          <v-icon>{{ action.icon }}</v-icon>
        </v-btn>
      </v-btn-group>
    </v-toolbar>

    <!-- Область редактора -->
    <v-sheet class="editor-content v-field-emulate" rounded>
      <editor-content :editor="editor" />
    </v-sheet>
  </v-sheet>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";
import { useEditor, EditorContent } from "@tiptap/vue-3";
import StarterKit from "@tiptap/starter-kit";
import Link from "@tiptap/extension-link";

type EditorAction = {
  icon: string;
  label: string;
  handler: () => void;
  active?: Record<string, any> | string;
};

const props = defineProps<{
  modelValue?: string;
}>();

const emit = defineEmits<{
  (e: "update:model-value", value: string): void;
}>();

// Состояние редактора
const editor = useEditor({
  extensions: [StarterKit, Link.configure({ openOnClick: false })],
  onUpdate: ({ editor }) => {
    emit("update:model-value", editor.getHTML());
  },
});

// Обработка ссылок
const showLinkDialog = ref(false);
const linkUrl = ref("");

const closeLinkDialog = () => {
  showLinkDialog.value = false;
  linkUrl.value = "";
};

const handleAddLink = () => {
  if (linkUrl.value) {
    editor.value
      ?.chain()
      .focus()
      .setLink({ href: linkUrl.value, target: "_blank" })
      .run();
  } else {
    editor.value?.chain().focus().unsetLink().run();
  }
  closeLinkDialog();
};

// Действия редактора
const editorActions = computed<Record<string, EditorAction>>(() => ({
  bold: {
    icon: "mdi-format-bold",
    label: "Жирный текст",
    handler: () => editor.value?.chain().focus().toggleBold().run(),
    active: "bold",
  },
  italic: {
    icon: "mdi-format-italic",
    label: "Курсив",
    handler: () => editor.value?.chain().focus().toggleItalic().run(),
    active: "italic",
  },
  
  strike: {
    icon: "mdi-format-strikethrough",
    label: "Зачеркнутый текст",
    handler: () => editor.value?.chain().focus().toggleStrike().run(),
    active: "strike",
  },
  code: {
    icon: "mdi-code-tags",
    label: "Блок кода",
    handler: () => editor.value?.chain().focus().toggleCodeBlock().run(),
    active: "codeBlock",
  },
  bulletList: {
    icon: "mdi-format-list-bulleted",
    label: "Маркированный список",
    handler: () => editor.value?.chain().focus().toggleBulletList().run(),
    active: "bulletList",
  },
  orderedList: {
    icon: "mdi-format-list-numbered",
    label: "Нумерованный список",
    handler: () => editor.value?.chain().focus().toggleOrderedList().run(),
    active: "orderedList",
  },
  undo: {
    icon: "mdi-undo",
    label: "Отменить",
    handler: () => editor.value?.commands.undo(),
  },
  redo: {
    icon: "mdi-redo",
    label: "Повторить",
    handler: () => editor.value?.commands.redo(),
  },
}));

// Инициализация и дестрой
onMounted(() => {
  if (props.modelValue) {
    editor.value?.commands.setContent(props.modelValue);
  }
});

onBeforeUnmount(() => {
  editor.value?.destroy();
});

// Наблюдатель для внешних изменений
watch(
  () => props.modelValue,
  (newValue) => {
    const isSame = editor.value?.getHTML() === newValue;
    if (!isSame && newValue !== undefined) {
      editor.value?.commands.setContent(newValue, false);
    }
  },
  { flush: "post" }
);
</script>

<style lang="scss">
.v-field-emulate {
  background-color: #2A2A2A;
  transition: all 250ms cubic-bezier(0.4, 0, 0.2, 1);

  &:hover {
    background-color: #323232;
  }
}
.wysiwygeditor {
  box-shadow: 0px 3px 1px -2px var(--v-shadow-key-umbra-opacity, rgba(0, 0, 0, 0.2)), 0px 2px 2px 0px var(--v-shadow-key-penumbra-opacity, rgba(0, 0, 0, 0.14)), 0px 1px 5px 0px var(--v-shadow-key-ambient-opacity, rgba(0, 0, 0, 0.12));
  color: rgba(var(--v-theme-on-surface), var(--v-high-emphasis-opacity));
}

.editor-content {
  min-height: 150px;
  padding: 16px;


  .ProseMirror {
    outline: none;
    word-wrap: break-word;
    white-space: pre-wrap;
    font-variant-ligatures: none;

    &:focus-visible {
      outline: none;
    }

    ul,
    ol {
      padding-left: 24px;
    }

    h1 {
      font-size: 2em;
    }

    h2 {
      font-size: 1.5em;
    }

    h3 {
      font-size: 1.17em;
    }

    h4 {
      font-size: 1em;
    }

    h5 {
      font-size: 0.83em;
    }

    h6 {
      font-size: 0.67em;
    }

    pre {
      padding: 12px;
      background: rgba(0, 0, 0, 0.05);
      border-radius: 4px;
    }
  }
}
</style>
