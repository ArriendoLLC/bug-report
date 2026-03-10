<template>
  <button
    :class="buttonClasses"
    @click="handleClick"
    type="button"
  >
    <slot>{{ text }}</slot>
  </button>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  text: {
    type: String,
    default: 'Report Bug'
  },
  size: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large'].includes(value)
  },
  color: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'danger'].includes(value)
  },
  routeName: {
    type: String,
    default: null
  },
  routePath: {
    type: String,
    default: '/bug-reports/create'
  }
});

const emit = defineEmits(['click']);

const buttonClasses = computed(() => {
  const baseClasses = 'bug-report-button';
  const sizeClass = `bug-report-button--${props.size}`;
  const colorClass = `bug-report-button--${props.color}`;

  return `${baseClasses} ${sizeClass} ${colorClass}`;
});

const handleClick = () => {
  // Capture current URL
  const currentUrl = window.location.href;

  // Emit event for parent components
  emit('click', { url: currentUrl });

  // Navigate to report form with URL as query parameter
  if (props.routeName) {
    // If using Vue Router
    if (window.$router) {
      window.$router.push({
        name: props.routeName,
        query: { url: currentUrl }
      });
    }
  } else {
    // Fallback to direct navigation
    const separator = props.routePath.includes('?') ? '&' : '?';
    window.location.href = `${props.routePath}${separator}url=${encodeURIComponent(currentUrl)}`;
  }
};
</script>

<style scoped>
.bug-report-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: 500;
  border: none;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: all 0.2s ease;
  font-family: inherit;
}

.bug-report-button:hover {
  opacity: 0.9;
  transform: translateY(-1px);
}

.bug-report-button:active {
  transform: translateY(0);
}

.bug-report-button:focus {
  outline: 2px solid currentColor;
  outline-offset: 2px;
}

/* Size variants */
.bug-report-button--small {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
}

.bug-report-button--medium {
  padding: 0.625rem 1.25rem;
  font-size: 1rem;
}

.bug-report-button--large {
  padding: 0.75rem 1.5rem;
  font-size: 1.125rem;
}

/* Color variants */
.bug-report-button--primary {
  background-color: #3490dc;
  color: #ffffff;
}

.bug-report-button--primary:hover {
  background-color: #2779bd;
}

.bug-report-button--secondary {
  background-color: #6c757d;
  color: #ffffff;
}

.bug-report-button--secondary:hover {
  background-color: #5a6268;
}

.bug-report-button--danger {
  background-color: #e3342f;
  color: #ffffff;
}

.bug-report-button--danger:hover {
  background-color: #cc1f1a;
}
</style>
