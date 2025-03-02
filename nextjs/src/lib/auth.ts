/**
 * Authentication utilities for handling JWT tokens
 */

/**
 * Refreshes the JWT token by calling the refresh-token API endpoint
 * @returns Promise<boolean> True if token was successfully refreshed
 */
export async function refreshJwtToken(): Promise<boolean> {
  try {
    console.log('Refreshing JWT token...');
    const response = await fetch('/api/refresh-token', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
      cache: 'no-store',  // Prevent caching
    });

    if (!response.ok) {
      const data = await response.json();
      console.error('Token refresh failed:', data);
      return false;
    }

    const data = await response.json();
    console.log('Token refresh response:', data);
    return data.success === true;
  } catch (error) {
    console.error('Error refreshing token:', error);
    return false;
  }
}

/**
 * Checks if the current JWT token is valid by making a test request
 * @returns Promise<boolean> True if token is valid
 */
export async function checkJwtToken(): Promise<boolean> {
  try {
    const response = await fetch('/api/chat', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      cache: 'no-store',
      body: JSON.stringify({
        messages: [{ role: 'user', content: 'test connection' }],
      }),
    });

    return response.ok;
  } catch (error) {
    console.error('Error checking token:', error);
    return false;
  }
}

/**
 * Attempts to refresh the token and then checks if it's valid
 * @returns Promise<boolean> True if token was refreshed and is valid
 */
export async function ensureValidToken(): Promise<boolean> {
  // First try to refresh the token
  const refreshed = await refreshJwtToken();
  if (!refreshed) {
    return false;
  }

  // Then check if the token is valid
  return await checkJwtToken();
}
