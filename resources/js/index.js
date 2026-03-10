/**
 * Bug Report Package - Vue Components
 *
 * Export all Vue components and the API service for use in consuming applications
 */

// Components
import ReportBugButton from './components/ReportBugButton.vue';
import BugReportCreate from './components/BugReportCreate.vue';

// API Service
import BugReportApi, { ApiError, ValidationError } from './services/BugReportApi.js';

// i18n
import i18n, { addLocale, setLocale } from './i18n/index.js';

// Export components
export {
  ReportBugButton,
  BugReportCreate,
  BugReportApi,
  ApiError,
  ValidationError,
  i18n,
  addLocale,
  setLocale
};

// Default export for convenience
export default {
  ReportBugButton,
  BugReportCreate,
  BugReportApi,
  i18n
};
