import { createI18n } from 'vue-i18n';
import en from './en.js';

// Create i18n instance with default settings
export const i18n = createI18n({
  legacy: false, // Use Composition API mode
  locale: 'en',
  fallbackLocale: 'en',
  messages: {
    en
  },
  globalInjection: true // Make $t available globally
});

// Export function to add more locales dynamically
export function addLocale(locale, messages) {
  i18n.global.setLocaleMessage(locale, messages);
}

// Export function to change locale
export function setLocale(locale) {
  i18n.global.locale.value = locale;
}

export default i18n;
