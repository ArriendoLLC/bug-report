# Internationalization (i18n) Usage

The Bug Report package uses **Vue I18n v11** for internationalization support in Vue components.

## Setup

The package exports a pre-configured i18n instance with English translations included by default.

### Basic Setup in Your Application

```javascript
import { createApp } from 'vue';
import { BugReportCreate, i18n } from '@arriendo/bug-report';

const app = createApp({
  // your app
});

// Use the bug report i18n instance
app.use(i18n);

// Register components
app.component('BugReportCreate', BugReportCreate);

app.mount('#app');
```

## Adding Additional Languages

You can add more language translations using the `addLocale` helper:

```javascript
import { i18n, addLocale, setLocale } from '@arriendo/bug-report';

// Add Spanish translations
addLocale('es', {
  form: {
    title: 'Reportar un Error',
    title_label: 'Título',
    title_placeholder: 'Breve descripción del problema',
    description_label: 'Descripción',
    description_placeholder: 'Proporcione información detallada sobre el error',
    url_label: 'URL (donde ocurrió el error)',
    url_placeholder: 'https://ejemplo.com/pagina',
    attachments_label: 'Archivos Adjuntos',
    attachments_help: 'Puede cargar hasta {max} archivos (máx. {size} KB cada uno)',
    drop_files: 'Suelta archivos aquí o haz clic para cargar',
    remove_file: 'Eliminar archivo'
  },
  submit: 'Enviar',
  cancel: 'Cancelar',
  loading: 'Enviando...',
  messages: {
    submit_success: '¡Informe de error enviado con éxito!',
    submit_error: 'Error al enviar el informe. Por favor, inténtelo de nuevo.'
  }
  // ... more translations
});

// Switch to Spanish
setLocale('es');
```

## Available Translation Keys

The package includes the following translation keys:

### General
- `title`, `bug_reports`, `report_bug`, `submit`, `cancel`, `save`, `delete`, `edit`, `close`, `loading`, `no_results`, `search`, `filter`, `reset`

### Form (`form.*`)
- `form.title`, `form.title_label`, `form.title_placeholder`
- `form.description_label`, `form.description_placeholder`
- `form.url_label`, `form.url_placeholder`
- `form.attachments_label`, `form.attachments_help`, `form.drop_files`, `form.remove_file`

### Messages (`messages.*`)
- `messages.submit_success`, `messages.submit_error`
- `messages.update_success`, `messages.update_error`
- `messages.delete_success`, `messages.delete_error`
- `messages.comment_added`, `messages.comment_updated`, `messages.comment_deleted`
- `messages.status_updated`

### Validation (`validation.*`)
- `validation.title_required`, `validation.title_max`
- `validation.description_required`, `validation.invalid_url`
- `validation.file_too_large`, `validation.file_type_not_allowed`, `validation.too_many_files`

### Dashboard (`dashboard.*`)
- Dashboard-specific translations for admin components

### Detail (`detail.*`)
- Detail view translations

### Comments (`comments.*`)
- Comment-specific translations

### Status (`status.*`)
- `status.new`, `status.in_progress`, `status.resolved`, `status.closed`

### Pagination (`pagination.*`)
- Pagination controls

### Errors (`errors.*`)
- Error message translations

## Using with Your Own i18n Instance

If your application already uses vue-i18n, you can merge the bug report translations:

```javascript
import { createI18n } from 'vue-i18n';
import enTranslations from '@arriendo/bug-report/resources/js/i18n/en.js';

const i18n = createI18n({
  legacy: false,
  locale: 'en',
  messages: {
    en: {
      // Your app translations
      myApp: {
        title: 'My Application'
      },
      // Bug report translations
      ...enTranslations
    }
  }
});
```

## Laravel Blade Translations

The package also includes Laravel translation files in `resources/lang/en/bug-report.php` that mirror the Vue translations for use in Blade templates.

To use them in Blade:

```blade
{{ __('bug-report::bug-report.form.title') }}
```

## Dynamic Locale Switching

```javascript
import { setLocale } from '@arriendo/bug-report';

// In your component or app
const switchLanguage = (locale) => {
  setLocale(locale);
};
```

## Notes

- All components use the `$t()` function from vue-i18n for translations
- Interpolation is supported: `$t('form.attachments_help', { max: 5, size: 5120 })`
- The package uses Composition API mode (`legacy: false`) for better tree-shaking
- Translations are globally injected, so `$t` is available in all components
