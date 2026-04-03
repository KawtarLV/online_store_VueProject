import config from '../config.js';

/**
 * API Utility Functions
 * 
 * Helper functions for making API requests using the app configuration
 */

/**
 * Build the full API URL for an endpoint
 * @param {string} endpoint - API endpoint (e.g., '/articles')
 * @returns {string} Full API URL
 */
function buildApiUrl(endpoint) {
  const baseUrl = config.apiDomain.replace(/\/$/, ''); // Remove trailing slash
  const path = endpoint.replace(/^\//, ''); // Remove leading slash from endpoint
  return `${baseUrl}/${path}`;
}

/**
 * Make a GET request to the API
 * @param {string} endpoint - API endpoint (e.g., '/articles')
 * @param {RequestInit} options - Fetch options
 * @returns {Promise<Response>}
 */
export async function get(endpoint, options = {}) {
  const url = buildApiUrl(endpoint);
  return fetch(url, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
      ...options.headers,
    },
    ...options,
  });
}

/**
 * Make a POST request to the API
 * @param {string} endpoint - API endpoint
 * @param {object} data - Request body data
 * @param {RequestInit} options - Fetch options
 * @returns {Promise<Response>}
 */
export async function post(endpoint, data, options = {}) {
  const url = buildApiUrl(endpoint);
  return fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      ...options.headers,
    },
    body: JSON.stringify(data),
    ...options,
  });
}

/**
 * Make a PUT request to the API
 * @param {string} endpoint - API endpoint
 * @param {object} data - Request body data
 * @param {RequestInit} options - Fetch options
 * @returns {Promise<Response>}
 */
export async function put(endpoint, data, options = {}) {
  const url = buildApiUrl(endpoint);
  return fetch(url, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      ...options.headers,
    },
    body: JSON.stringify(data),
    ...options,
  });
}

/**
 * Make a DELETE request to the API
 * @param {string} endpoint - API endpoint
 * @param {RequestInit} options - Fetch options
 * @returns {Promise<Response>}
 */
export async function del(endpoint, options = {}) {
  const url = buildApiUrl(endpoint);
  return fetch(url, {
    method: 'DELETE',
    headers: {
      'Content-Type': 'application/json',
      ...options.headers,
    },
    ...options,
  });
}

/**
 * Get the full API URL for an endpoint
 * @param {string} endpoint - API endpoint
 * @returns {string} Full API URL
 */
export function getApiUrl(endpoint) {
  return buildApiUrl(endpoint);
}

/**
 * Make a POST request with multipart/form-data (used for file uploads)
 * Note: do not set Content-Type manually — the browser sets it with the boundary
 *
 * @param {string} endpoint - API endpoint
 * @param {FormData} formData - the form data object to send
 * @param {RequestInit} options - extra fetch options
 * @returns {Promise<Response>}
 */
export async function postForm(endpoint, formData, options = {}) {
  const url = buildApiUrl(endpoint);
  return fetch(url, {
    method: 'POST',
    body: formData,
    ...options,
  });
}

/**
 * Make a PUT request with multipart/form-data (used for updating products with images)
 * Note: do not set Content-Type manually — the browser sets it with the boundary
 *
 * @param {string} endpoint - API endpoint
 * @param {FormData} formData - the form data object to send
 * @param {RequestInit} options - extra fetch options
 * @returns {Promise<Response>}
 */
export async function putForm(endpoint, formData, options = {}) {
  const url = buildApiUrl(endpoint);
  return fetch(url, {
    method: 'PUT',
    body: formData,
    ...options,
  });
}

/**
 * Upload a single file (multipart). Expects JSON with { url }.
 * @param {string} endpoint
 * @param {File} file
 */
export async function uploadFile(endpoint, file, options = {}) {
  const url = buildApiUrl(endpoint);
  const fd = new FormData();
  fd.append('file', file);
  return fetch(url, {
    method: 'POST',
    body: fd,
    ...options,
  });
}
