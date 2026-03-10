<template>
  <div class="bug-report-create">
    <div class="bug-report-create__header">
      <h2 class="bug-report-create__title">{{ $t('form.title') }}</h2>
    </div>

    <form @submit.prevent="handleSubmit" class="bug-report-create__form">
      <!-- Title Field -->
      <div class="form-group">
        <label for="title" class="form-label">
          {{ $t('form.title_label') }} <span class="required">*</span>
        </label>
        <input
          id="title"
          v-model="form.title"
          type="text"
          class="form-input"
          :class="{ 'form-input--error': errors.title }"
          :placeholder="$t('form.title_placeholder')"
          :disabled="loading"
        />
        <span v-if="errors.title" class="form-error">{{ errors.title }}</span>
      </div>

      <!-- Description Field -->
      <div class="form-group">
        <label for="description" class="form-label">
          {{ $t('form.description_label') }} <span class="required">*</span>
        </label>
        <textarea
          id="description"
          v-model="form.description"
          class="form-textarea"
          :class="{ 'form-input--error': errors.description }"
          :placeholder="$t('form.description_placeholder')"
          :disabled="loading"
          rows="6"
        ></textarea>
        <span v-if="errors.description" class="form-error">{{ errors.description }}</span>
      </div>

      <!-- URL Field -->
      <div class="form-group">
        <label for="url" class="form-label">
          {{ $t('form.url_label') }}
        </label>
        <input
          id="url"
          v-model="form.url"
          type="url"
          class="form-input"
          :class="{ 'form-input--error': errors.url }"
          :placeholder="$t('form.url_placeholder')"
          :disabled="loading"
        />
        <span v-if="errors.url" class="form-error">{{ errors.url }}</span>
      </div>

      <!-- File Upload -->
      <div class="form-group">
        <label class="form-label">
          {{ $t('form.attachments_label') }}
        </label>
        <div class="file-upload">
          <div
            class="file-upload__dropzone"
            :class="{ 'file-upload__dropzone--dragover': isDragging }"
            @drop.prevent="handleDrop"
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @click="$refs.fileInput.click()"
          >
            <input
              ref="fileInput"
              type="file"
              multiple
              accept="image/*,video/*,.pdf"
              @change="handleFileSelect"
              class="file-upload__input"
              :disabled="loading"
            />
            <div class="file-upload__message">
              <svg class="file-upload__icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
              </svg>
              <p>{{ $t('form.drop_files') }}</p>
              <p class="file-upload__help">{{ $t('form.attachments_help', { max: maxFiles, size: maxFileSize }) }}</p>
            </div>
          </div>

          <!-- File Preview List -->
          <div v-if="form.attachments.length > 0" class="file-preview-list">
            <div
              v-for="(file, index) in form.attachments"
              :key="index"
              class="file-preview"
            >
              <div class="file-preview__icon">
                <img v-if="isImage(file)" :src="getPreviewUrl(file)" alt="" class="file-preview__image" />
                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <div class="file-preview__info">
                <p class="file-preview__name">{{ file.name }}</p>
                <p class="file-preview__size">{{ formatFileSize(file.size) }}</p>
              </div>
              <button
                type="button"
                @click="removeFile(index)"
                class="file-preview__remove"
                :disabled="loading"
              >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
        </div>
        <span v-if="errors.attachments" class="form-error">{{ errors.attachments }}</span>
      </div>

      <!-- Success Message -->
      <div v-if="successMessage" class="alert alert--success">
        {{ successMessage }}
      </div>

      <!-- Error Message -->
      <div v-if="errorMessage" class="alert alert--error">
        {{ errorMessage }}
      </div>

      <!-- Form Actions -->
      <div class="form-actions">
        <button
          type="button"
          @click="handleCancel"
          class="btn btn--secondary"
          :disabled="loading"
        >
          {{ $t('cancel') }}
        </button>
        <button
          type="submit"
          class="btn btn--primary"
          :disabled="loading"
        >
          <span v-if="loading" class="btn__spinner"></span>
          {{ loading ? $t('loading') : $t('submit') }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';

const props = defineProps({
  apiEndpoint: {
    type: String,
    default: '/api/bug-reports'
  },
  maxFiles: {
    type: Number,
    default: 5
  },
  maxFileSize: {
    type: Number,
    default: 5120 // KB
  },
  onSuccess: {
    type: Function,
    default: null
  },
  onCancel: {
    type: Function,
    default: null
  }
});

const emit = defineEmits(['success', 'error', 'cancel']);

const form = reactive({
  title: '',
  description: '',
  url: '',
  attachments: []
});

const errors = reactive({
  title: '',
  description: '',
  url: '',
  attachments: ''
});

const loading = ref(false);
const isDragging = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const fileInput = ref(null);

// Initialize URL from query parameter
onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search);
  const urlParam = urlParams.get('url');
  if (urlParam) {
    form.url = urlParam;
  }
});

// File handling
const handleFileSelect = (event) => {
  const files = Array.from(event.target.files);
  addFiles(files);
};

const handleDrop = (event) => {
  isDragging.value = false;
  const files = Array.from(event.dataTransfer.files);
  addFiles(files);
};

const addFiles = (files) => {
  errors.attachments = '';

  if (form.attachments.length + files.length > props.maxFiles) {
    errors.attachments = `Maximum ${props.maxFiles} files allowed`;
    return;
  }

  files.forEach(file => {
    if (file.size / 1024 > props.maxFileSize) {
      errors.attachments = `File ${file.name} exceeds maximum size of ${props.maxFileSize} KB`;
      return;
    }
    form.attachments.push(file);
  });
};

const removeFile = (index) => {
  form.attachments.splice(index, 1);
};

const isImage = (file) => {
  return file.type.startsWith('image/');
};

const getPreviewUrl = (file) => {
  return URL.createObjectURL(file);
};

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

// Form validation
const validateForm = () => {
  let isValid = true;
  errors.title = '';
  errors.description = '';
  errors.url = '';

  if (!form.title.trim()) {
    errors.title = 'Title is required';
    isValid = false;
  }

  if (!form.description.trim()) {
    errors.description = 'Description is required';
    isValid = false;
  }

  if (form.url && !isValidUrl(form.url)) {
    errors.url = 'Please enter a valid URL';
    isValid = false;
  }

  return isValid;
};

const isValidUrl = (string) => {
  try {
    new URL(string);
    return true;
  } catch (_) {
    return false;
  }
};

// Form submission
const handleSubmit = async () => {
  if (!validateForm()) {
    return;
  }

  loading.value = true;
  successMessage.value = '';
  errorMessage.value = '';

  try {
    const formData = new FormData();
    formData.append('title', form.title);
    formData.append('description', form.description);
    if (form.url) {
      formData.append('url', form.url);
    }

    form.attachments.forEach((file, index) => {
      formData.append(`attachments[${index}]`, file);
    });

    // Get CSRF token
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const response = await fetch(props.apiEndpoint, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json'
      },
      body: formData
    });

    const data = await response.json();

    if (response.ok) {
      successMessage.value = 'Bug report submitted successfully!';
      resetForm();
      emit('success', data);
      if (props.onSuccess) {
        props.onSuccess(data);
      }
    } else {
      errorMessage.value = data.message || 'Failed to submit bug report';
      emit('error', data);
    }
  } catch (error) {
    errorMessage.value = 'An error occurred. Please try again.';
    emit('error', error);
  } finally {
    loading.value = false;
  }
};

const handleCancel = () => {
  resetForm();
  emit('cancel');
  if (props.onCancel) {
    props.onCancel();
  }
};

const resetForm = () => {
  form.title = '';
  form.description = '';
  form.url = '';
  form.attachments = [];
  errors.title = '';
  errors.description = '';
  errors.url = '';
  errors.attachments = '';
};
</script>

<style scoped>
/* Component Container */
.bug-report-create {
  max-width: 800px;
  margin: 0 auto;
  padding: 2rem;
  background: #ffffff;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.bug-report-create__header {
  margin-bottom: 2rem;
}

.bug-report-create__title {
  font-size: 1.875rem;
  font-weight: 700;
  color: #1a202c;
  margin: 0;
}

/* Form Styles */
.bug-report-create__form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-label {
  font-weight: 600;
  color: #2d3748;
  font-size: 0.875rem;
}

.required {
  color: #e53e3e;
}

.form-input,
.form-textarea {
  padding: 0.75rem;
  border: 1px solid #cbd5e0;
  border-radius: 0.375rem;
  font-size: 1rem;
  transition: all 0.2s;
  font-family: inherit;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: #3490dc;
  box-shadow: 0 0 0 3px rgba(52, 144, 220, 0.1);
}

.form-input--error {
  border-color: #e53e3e;
}

.form-error {
  color: #e53e3e;
  font-size: 0.875rem;
}

.form-textarea {
  resize: vertical;
  min-height: 120px;
}

/* File Upload */
.file-upload__input {
  display: none;
}

.file-upload__dropzone {
  border: 2px dashed #cbd5e0;
  border-radius: 0.5rem;
  padding: 2rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
}

.file-upload__dropzone:hover,
.file-upload__dropzone--dragover {
  border-color: #3490dc;
  background-color: #ebf8ff;
}

.file-upload__icon {
  width: 48px;
  height: 48px;
  margin: 0 auto 1rem;
  color: #a0aec0;
}

.file-upload__message p {
  margin: 0.5rem 0;
  color: #4a5568;
}

.file-upload__help {
  font-size: 0.875rem;
  color: #718096;
}

/* File Preview */
.file-preview-list {
  margin-top: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.file-preview {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem;
  border: 1px solid #e2e8f0;
  border-radius: 0.375rem;
  background: #f7fafc;
}

.file-preview__icon {
  flex-shrink: 0;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #718096;
}

.file-preview__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 0.25rem;
}

.file-preview__info {
  flex: 1;
  min-width: 0;
}

.file-preview__name {
  font-weight: 500;
  color: #2d3748;
  margin: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.file-preview__size {
  font-size: 0.875rem;
  color: #718096;
  margin: 0.25rem 0 0 0;
}

.file-preview__remove {
  flex-shrink: 0;
  padding: 0.5rem;
  border: none;
  background: none;
  color: #e53e3e;
  cursor: pointer;
  border-radius: 0.25rem;
  transition: all 0.2s;
}

.file-preview__remove:hover {
  background-color: #fff5f5;
}

.file-preview__remove svg {
  width: 20px;
  height: 20px;
}

/* Alerts */
.alert {
  padding: 1rem;
  border-radius: 0.375rem;
  font-size: 0.875rem;
}

.alert--success {
  background-color: #c6f6d5;
  color: #22543d;
  border: 1px solid #9ae6b4;
}

.alert--error {
  background-color: #fed7d7;
  color: #742a2a;
  border: 1px solid #fc8181;
}

/* Form Actions */
.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 1rem;
}

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.375rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn--primary {
  background-color: #3490dc;
  color: #ffffff;
}

.btn--primary:hover:not(:disabled) {
  background-color: #2779bd;
}

.btn--secondary {
  background-color: #e2e8f0;
  color: #2d3748;
}

.btn--secondary:hover:not(:disabled) {
  background-color: #cbd5e0;
}

.btn__spinner {
  width: 16px;
  height: 16px;
  border: 2px solid #ffffff;
  border-top-color: transparent;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
