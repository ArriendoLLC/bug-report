/**
 * Bug Report API Service
 *
 * Axios-based API client for interacting with the Bug Report package endpoints.
 * Handles CSRF tokens, error handling, and provides a clean interface for all API operations.
 */

import axios from 'axios';

class BugReportApi {
  constructor(baseUrl = '/api/bug-reports') {
    this.baseUrl = baseUrl;

    // Create axios instance with default config
    this.axios = axios.create({
      baseURL: baseUrl,
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    // Set up interceptors
    this.setupInterceptors();
  }

  /**
   * Set up axios interceptors for CSRF token and error handling
   */
  setupInterceptors() {
    // Request interceptor - add CSRF token
    this.axios.interceptors.request.use(
      (config) => {
        const token = this.getCsrfToken();
        if (token) {
          config.headers['X-CSRF-TOKEN'] = token;
        }
        return config;
      },
      (error) => {
        return Promise.reject(error);
      }
    );

    // Response interceptor - handle errors
    this.axios.interceptors.response.use(
      (response) => response,
      (error) => {
        this.handleError(error);
      }
    );
  }

  /**
   * Get CSRF token from meta tag
   */
  getCsrfToken() {
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    return tokenMeta ? tokenMeta.getAttribute('content') : '';
  }

  /**
   * Handle API errors
   */
  handleError(error) {
    if (error.response) {
      // Server responded with error status
      const { status, data } = error.response;

      switch (status) {
        case 401:
          throw new ApiError('Unauthorized. Please log in.', 'UNAUTHORIZED', status);
        case 403:
          throw new ApiError('You do not have permission to perform this action.', 'FORBIDDEN', status);
        case 404:
          throw new ApiError('Resource not found.', 'NOT_FOUND', status);
        case 422:
          throw new ValidationError('Validation failed.', data.errors || {}, status);
        case 429:
          throw new ApiError('Too many requests. Please try again later.', 'RATE_LIMIT_EXCEEDED', status);
        default:
          throw new ApiError(
            data.message || data.error || 'An error occurred.',
            data.code || 'SERVER_ERROR',
            status
          );
      }
    } else if (error.request) {
      // Request made but no response received
      throw new ApiError('Network error. Please check your connection.', 'NETWORK_ERROR');
    } else {
      // Something else happened
      throw new ApiError('An unexpected error occurred.', 'UNKNOWN_ERROR');
    }
  }

  /**
   * Create a new bug report
   *
   * @param {Object} reportData - { title, description, url }
   * @param {File[]} files - Array of File objects
   * @returns {Promise<Object>} Created bug report
   */
  async createReport(reportData, files = []) {
    const formData = new FormData();
    formData.append('title', reportData.title);
    formData.append('description', reportData.description);

    if (reportData.url) {
      formData.append('url', reportData.url);
    }

    files.forEach((file, index) => {
      formData.append(`attachments[${index}]`, file);
    });

    const response = await this.axios.post('/', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });

    return response.data;
  }

  /**
   * List all bug reports (admin)
   *
   * @param {Object} params - { page, status, search }
   * @returns {Promise<Object>} Paginated bug reports
   */
  async listReports(params = {}) {
    const response = await this.axios.get('/', { params });
    return response.data;
  }

  /**
   * Get a single bug report by ID (admin)
   *
   * @param {number} id - Bug report ID
   * @returns {Promise<Object>} Bug report details
   */
  async getReport(id) {
    const response = await this.axios.get(`/${id}`);
    return response.data;
  }

  /**
   * Update bug report status (admin)
   *
   * @param {number} id - Bug report ID
   * @param {string} status - New status (new, in_progress, resolved, closed)
   * @returns {Promise<Object>} Updated bug report
   */
  async updateStatus(id, status) {
    const response = await this.axios.put(`/${id}/status`, { status });
    return response.data;
  }

  /**
   * Delete a bug report (admin)
   *
   * @param {number} id - Bug report ID
   * @returns {Promise<Object>} Success response
   */
  async deleteReport(id) {
    const response = await this.axios.delete(`/${id}`);
    return response.data;
  }

  /**
   * Add a comment to a bug report
   *
   * @param {number} reportId - Bug report ID
   * @param {string} comment - Comment text
   * @returns {Promise<Object>} Created comment
   */
  async addComment(reportId, comment) {
    const response = await this.axios.post(`/${reportId}/comments`, { comment });
    return response.data;
  }

  /**
   * Update a comment
   *
   * @param {number} reportId - Bug report ID
   * @param {number} commentId - Comment ID
   * @param {string} comment - Updated comment text
   * @returns {Promise<Object>} Updated comment
   */
  async updateComment(reportId, commentId, comment) {
    const response = await this.axios.put(`/${reportId}/comments/${commentId}`, { comment });
    return response.data;
  }

  /**
   * Delete a comment
   *
   * @param {number} reportId - Bug report ID
   * @param {number} commentId - Comment ID
   * @returns {Promise<Object>} Success response
   */
  async deleteComment(reportId, commentId) {
    const response = await this.axios.delete(`/${reportId}/comments/${commentId}`);
    return response.data;
  }
}

/**
 * Custom API Error class
 */
class ApiError extends Error {
  constructor(message, code, status = null) {
    super(message);
    this.name = 'ApiError';
    this.code = code;
    this.status = status;
  }
}

/**
 * Validation Error class
 */
class ValidationError extends ApiError {
  constructor(message, errors, status) {
    super(message, 'VALIDATION_ERROR', status);
    this.name = 'ValidationError';
    this.errors = errors;
  }
}

// Export singleton instance
export default new BugReportApi();

// Also export classes for custom instantiation
export { BugReportApi, ApiError, ValidationError };
