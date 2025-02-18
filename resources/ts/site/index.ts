import { createApp } from 'vue';

// Динамическая регистрация виджетов
const initWidgets = () => {
  document.querySelectorAll('[data-vue-widget]').forEach(el => {
    const widgetName = el.getAttribute('data-vue-widget');
    
    import(`./${widgetName}.vue`)
      .then(component => {
        createApp(component.default).mount(el);
      });
  });
};

document.addEventListener('DOMContentLoaded', initWidgets);