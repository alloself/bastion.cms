export default [{
  component: "v-text-field",
  key: "link.title",
  props: {
    autocomplete: "title",
    label: "Заголовок",
    name: "title",
    type: "text",
  },
},
{
  component: "v-text-field",
  key: "link.subtitle",
  props: {
    autocomplete: "subtitle",
    label: "Подзаголовок",
    name: "subtitle",
    type: "text",
  },
},
{
  component: "v-text-field",
  key: "link.url",
  props: {
    autocomplete: "url",
    label: "Ссылка",
    messages:
      "Генерируется автоматически при создании и изменении заголовка страницы,можно обновить вручную.",
    name: "url",
    type: "text",
    class: "pb-1 mb-2",
  },
}]