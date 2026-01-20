import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
  },
});

// Add token to requests
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Auth
export const login = (credentials) => api.post('/login', credentials);
export const logout = () => api.post('/logout');
export const getUser = () => api.get('/me');

// Destinations
export const getDestinations = () => api.get('/destinations');
export const createDestination = (data) => api.post('/destinations', data);

// Cities
export const getCitiesByDestination = (destinationId) => 
  api.get(`/destinations/${destinationId}/cities`);

// Sightseeing
export const getSightseeingsByCity = (cityId) => 
  api.get(`/cities/${cityId}/sightseeings`);

// Hotels
export const getHotelsByCity = (cityId) => 
  api.get(`/cities/${cityId}/hotels`);

// Itineraries
export const getItineraries = () => api.get('/itineraries');
export const createItinerary = (data) => api.post('/itineraries', data);
export const getItinerary = (id) => api.get(`/itineraries/${id}`);
export const updateItinerary = (id, data) => api.put(`/itineraries/${id}`, data);
export const addItineraryItem = (id, data) => api.post(`/itineraries/${id}/items`, data);
export const removeItineraryItem = (itineraryId, itemId) => 
  api.delete(`/itineraries/${itineraryId}/items/${itemId}`);

export default api;
